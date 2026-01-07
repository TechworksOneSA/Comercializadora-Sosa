<?php

require_once __DIR__ . "/PosModel.php";

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

        if ($montoCobrado > $saldoPendiente) {
            $_SESSION['flash_error'] = "El monto cobrado no puede ser mayor al saldo pendiente";
            redirect("/admin/pos/cobrar/{$ventaId}");
            return;
        }

        // Registrar cobro
        try {
            $this->model->registrarCobro($ventaId, $montoCobrado, $metodoPago, $observaciones);
            $_SESSION['flash_success'] = "Cobro registrado exitosamente";
            redirect('/admin/pos');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al registrar cobro: " . $e->getMessage();
            redirect("/admin/pos/cobrar/{$ventaId}");
        }
    }

    // ==================== GASTOS Y MOVIMIENTOS ====================
    public function gastos()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $gastos = $this->model->obtenerGastos();

        $this->viewWithLayout("pos/views/gastos", [
            "title" => "Gastos y Movimientos",
            "user" => $_SESSION["user"],
            "gastos" => $gastos,
        ]);
    }

    public function nuevoGasto()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $errors = $_SESSION['pos_errors'] ?? [];
        $old = $_SESSION['pos_old'] ?? [];
        unset($_SESSION['pos_errors'], $_SESSION['pos_old']);

        $this->viewWithLayout("pos/views/nuevo_gasto", [
            "title" => "Nuevo Gasto/Movimiento",
            "user" => $_SESSION["user"],
            "errors" => $errors,
            "old" => $old,
        ]);
    }

    public function guardarGasto()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $tipo = $_POST['tipo'] ?? '';
        $concepto = trim($_POST['concepto'] ?? '');
        $monto = (float)($_POST['monto'] ?? 0);
        $metodoPago = $_POST['metodo_pago'] ?? '';
        $observaciones = trim($_POST['observaciones'] ?? '');

        $errors = [];

        if (empty($tipo) || !in_array($tipo, ['gasto', 'retiro'])) {
            $errors[] = "Debe seleccionar un tipo válido";
        }

        if (empty($concepto)) {
            $errors[] = "El concepto es obligatorio";
        }

        if ($monto <= 0) {
            $errors[] = "El monto debe ser mayor a 0";
        }

        if (empty($metodoPago)) {
            $errors[] = "Debe seleccionar un método de pago";
        }

        if (!empty($errors)) {
            $_SESSION['pos_errors'] = $errors;
            $_SESSION['pos_old'] = $_POST;
            redirect('/admin/pos/nuevo-gasto');
            return;
        }

        try {
            $this->model->registrarMovimiento([
                'tipo' => $tipo,
                'concepto' => $concepto,
                'monto' => $monto,
                'metodo_pago' => $metodoPago,
                'observaciones' => $observaciones,
                'usuario_id' => $_SESSION['user']['id']
            ]);

            $_SESSION['flash_success'] = "Movimiento registrado exitosamente";
            redirect('/admin/pos/gastos');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al registrar movimiento: " . $e->getMessage();
            redirect('/admin/pos/nuevo-gasto');
        }
    }

    public function eliminarGasto($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        try {
            $this->model->eliminarMovimiento($id);
            $_SESSION['flash_success'] = "Movimiento eliminado exitosamente";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al eliminar movimiento: " . $e->getMessage();
        }

        redirect('/admin/pos/gastos');
    }
}
