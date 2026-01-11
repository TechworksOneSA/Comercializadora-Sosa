<?php

require_once __DIR__ . "/PosModel.php";
require_once __DIR__ . "/../caja/CajaModel.php";

class PosController extends Controller
{
    private PosModel $model;

    public function __construct()
    {
        $this->model = new PosModel();
    }

    // ==================== DASHBOARD POS ====================
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $ventasPendientes = $this->model->obtenerVentasPendientesCobro();
        $resumenCaja = $this->model->obtenerResumenCaja();
        $ultimosMovimientos = $this->model->obtenerUltimosMovimientos(10);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->viewWithLayout("pos/views/index", [
            "title" => "Punto de Venta - Caja",
            "user" => $_SESSION["user"],
            "ventasPendientes" => $ventasPendientes,
            "resumenCaja" => $resumenCaja,
            "ultimosMovimientos" => $ultimosMovimientos,
            "success" => $success,
            "error" => $error,
        ]);
    }

    // ==================== COBROS ====================
    public function cobrar($ventaId)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $venta = $this->model->obtenerVentaPorId($ventaId);

        if (!$venta) {
            $_SESSION['flash_error'] = "Venta no encontrada";
            redirect('/admin/pos');
            return;
        }

        $this->viewWithLayout("pos/views/cobrar", [
            "title" => "Registrar Cobro",
            "user" => $_SESSION["user"],
            "venta" => $venta,
        ]);
    }

    public function registrarCobro()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $ventaId = (int)($_POST['venta_id'] ?? 0);
        $montoCobrado = (float)($_POST['monto_cobrado'] ?? 0);
        $metodoPago = $_POST['metodo_pago'] ?? '';
        $observaciones = trim($_POST['observaciones'] ?? '');

        $errors = [];

        if ($ventaId <= 0) {
            $errors[] = "ID de venta inválido";
        }

        if ($montoCobrado <= 0) {
            $errors[] = "El monto debe ser mayor a 0";
        }

        if (empty($metodoPago)) {
            $errors[] = "Debe seleccionar un método de pago";
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(". ", $errors);
            redirect("/admin/pos/cobrar/{$ventaId}");
            return;
        }

        $venta = $this->model->obtenerVentaPorId($ventaId);

        if (!$venta) {
            $_SESSION['flash_error'] = "Venta no encontrada";
            redirect('/admin/pos');
            return;
        }

        $saldoPendiente = $venta['total'] - $venta['total_pagado'];

        // Permitir monto mayor solo si es pago con tarjeta (por recargo)
        if ($montoCobrado > $saldoPendiente && $metodoPago !== 'Tarjeta') {
            $_SESSION['flash_error'] = "El monto cobrado no puede ser mayor al saldo pendiente";
            redirect("/admin/pos/cobrar/{$ventaId}");
            return;
        }

        // Registrar cobro
        try {
            $this->model->registrarCobro($ventaId, $montoCobrado, $metodoPago, $observaciones);
            $_SESSION['flash_success'] = "Venta #{$ventaId} cobrada exitosamente";
            redirect('/admin/ventas');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al registrar cobro: " . $e->getMessage();
            redirect("/admin/pos/cobrar/{$ventaId}");
        }
    }
}
