<?php

class CajaController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new CajaModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $resumenCaja = $this->model->obtenerResumenCaja();
        $ultimosMovimientos = $this->model->obtenerUltimosMovimientos(10);

        $this->viewWithLayout("caja/views/index", [
            "title" => "Gestión de Caja",
            "user" => $_SESSION["user"],
            "resumenCaja" => $resumenCaja,
            "ultimosMovimientos" => $ultimosMovimientos,
        ]);
    }

    public function movimientos()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $movimientos = $this->model->obtenerMovimientos();

        $this->viewWithLayout("caja/views/movimientos", [
            "title" => "Movimientos de Caja",
            "user" => $_SESSION["user"],
            "movimientos" => $movimientos,
        ]);
    }

    public function nuevoMovimiento()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $errors = $_SESSION['caja_errors'] ?? [];
        $old = $_SESSION['caja_old'] ?? [];
        unset($_SESSION['caja_errors'], $_SESSION['caja_old']);

        $this->viewWithLayout("caja/views/nuevo_movimiento", [
            "title" => "Nuevo Gasto/Movimiento",
            "user" => $_SESSION["user"],
            "errors" => $errors,
            "old" => $old,
        ]);
    }

    public function guardarMovimiento()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $concepto = trim($_POST['concepto'] ?? '');
        $monto = (float)($_POST['monto'] ?? 0);
        $metodoPago = $_POST['metodo_pago'] ?? '';
        $observaciones = trim($_POST['observaciones'] ?? '');

        $errors = [];

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
            $_SESSION['caja_errors'] = $errors;
            $_SESSION['caja_old'] = $_POST;
            redirect('/admin/caja/nuevo-movimiento');
            return;
        }

        // Detectar automáticamente si es retiro o gasto basándose en el concepto
        $conceptoLower = strtolower($concepto);
        $tipo = 'gasto'; // Por defecto es gasto
        
        // Solo es retiro si menciona explícitamente retiro personal/dueño
        if (
            strpos($conceptoLower, 'retiro') !== false && 
            (strpos($conceptoLower, 'personal') !== false || 
             strpos($conceptoLower, 'dueño') !== false ||
             strpos($conceptoLower, 'dueno') !== false ||
             strpos($conceptoLower, 'propietario') !== false)
        ) {
            $tipo = 'retiro';
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

            $_SESSION['flash_success'] = "Movimiento registrado exitosamente como " . strtoupper($tipo);
            redirect('/admin/caja/movimientos');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al registrar movimiento: " . $e->getMessage();
            redirect('/admin/caja/nuevo-movimiento');
        }
    }

    public function eliminarMovimiento($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        try {
            $this->model->eliminarMovimiento($id);
            $_SESSION['flash_success'] = "Movimiento eliminado exitosamente";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al eliminar movimiento: " . $e->getMessage();
        }

        redirect('/admin/caja/movimientos');
    }
}
