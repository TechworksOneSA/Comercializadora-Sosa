<?php

require_once __DIR__ . '/VentasModel.php';

class VentasController extends Controller
{
    private VentasModel $model;

    public function __construct()
    {
        //parent::__construct();
        $this->model = new VentasModel();
    }

    // =========================
    // LISTADO DE VENTAS
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $ventas = $this->model->getAllVentas();
        $kpis   = $this->model->getKpis();

        $this->viewWithLayout("ventas/views/index", [
            "title"  => "Ventas",
            "ventas" => $ventas,
            "kpis"   => $kpis,
            "user"   => $_SESSION['user'],
            "success"=> $_SESSION['flash_success'] ?? null,
            "error"  => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    // =========================
    // VER VENTA
    // =========================
    public function ver()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = "ID de venta inválido";
            redirect('/admin/ventas');
            return;
        }

        $venta = $this->model->getVentaById($id);

        if (!$venta) {
            error_log("❌ [VentasController@ver] Venta no encontrada para ID {$id}");
            $_SESSION['flash_error'] = "Venta no encontrada";
            redirect('/admin/ventas');
            return;
        }

        $detalle = $this->model->getVentaDetalle($id);

        $this->viewWithLayout("ventas/views/ver", [
            "title"   => "Ver Venta #{$id}",
            "venta"   => $venta,
            "detalle" => $detalle,
            "user"    => $_SESSION['user'],
        ]);
    }

    // =========================
    // ANULAR VENTA (FIXED)
    // =========================
    public function anular()
    {
        RoleMiddleware::requireAdminOrVendedor();

        // Log defensivo (no dependemos de Apache/PHP config)
        @file_put_contents(
            '/tmp/ventas_anular.log',
            "\n[" . date('Y-m-d H:i:s') . "] POST=" . json_encode($_POST) .
            " | SESSION_USER=" . json_encode($_SESSION['user'] ?? null),
            FILE_APPEND
        );

        $ventaId = (int)($_POST['venta_id'] ?? 0);

        if ($ventaId <= 0) {
            $_SESSION['flash_error'] = "ID de venta inválido";
            redirect('/admin/ventas');
            return;
        }

        try {
            if (empty($_SESSION['user']['id'])) {
                throw new Exception("Sesión inválida o usuario no autenticado");
            }

            $usuarioId = (int)$_SESSION['user']['id'];

            $resultado = $this->model->anularVenta($ventaId, $usuarioId);

            if ($resultado) {
                $_SESSION['flash_success'] = "Venta #{$ventaId} anulada correctamente.";
            } else {
                $_SESSION['flash_error'] = "No se pudo anular la venta (posible estado inválido).";
            }

        } catch (Throwable $e) {
            @file_put_contents(
                '/tmp/ventas_anular.log',
                "\n[" . date('Y-m-d H:i:s') . "] ERROR: " .
                $e->getMessage() .
                " | " . $e->getFile() . ":" . $e->getLine() .
                "\nTRACE:\n" . $e->getTraceAsString() . "\n",
                FILE_APPEND
            );

            $_SESSION['flash_error'] = "Error interno al anular la venta.";
        }

        redirect('/admin/ventas');
    }

    // =========================
    // CONVERTIR DESDE COTIZACIÓN
    // =========================
    public function convertirDesdeCotizacion()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $cotizacionId = (int)($_POST['cotizacion_id'] ?? 0);

        if ($cotizacionId <= 0) {
            $_SESSION['flash_error'] = "ID de cotización inválido";
            redirect('/admin/cotizaciones');
            return;
        }

        try {
            $usuarioId = (int)$_SESSION['user']['id'];

            $ventaId = $this->model->convertirCotizacion($cotizacionId, $usuarioId);

            $_SESSION['flash_success'] = "Cotización #{$cotizacionId} convertida a Venta #{$ventaId}";
            redirect('/admin/ventas/ver?id=' . $ventaId);

        } catch (Throwable $e) {
            error_log("❌ Error convertirDesdeCotizacion: " . $e->getMessage());
            $_SESSION['flash_error'] = "Error al convertir cotización";
            redirect('/admin/cotizaciones');
        }
    }
}