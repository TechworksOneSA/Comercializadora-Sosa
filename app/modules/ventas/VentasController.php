<?php

require_once __DIR__ . "/VentasModel.php";
require_once __DIR__ . "/../productos/ProductosModel.php";
require_once __DIR__ . "/../clientes/ClientesModel.php";

class VentasController extends Controller
{
    private VentasModel $model;

    public function __construct()
    {
        $this->model = new VentasModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $ventas = $this->model->getAllVentas();
        $kpis = $this->model->getKpis();

        // Mensajes flash
        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->viewWithLayout("ventas/views/index", [
            "title"   => "Ventas",
            "user"    => $_SESSION["user"],
            "ventas"  => $ventas,
            "kpis"    => $kpis,
            "success" => $success,
            "error"   => $error,
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $productosModel = new ProductosModel();
        $clientesModel = new ClientesModel();

        $productos = $productosModel->listarActivos();
        $clientes = $clientesModel->listar();

        $errors = $_SESSION['ventas_errors'] ?? [];
        $old    = $_SESSION['ventas_old']    ?? [];

        unset($_SESSION['ventas_errors'], $_SESSION['ventas_old']);

        $this->viewWithLayout("ventas/views/crear", [
            "title"     => "Nueva Venta",
            "user"      => $_SESSION["user"],
            "productos" => $productos,
            "clientes"  => $clientes,
            "errors"    => $errors,
            "old"       => $old,
        ]);
    }

    // =========================
    // GUARDAR
    // =========================
    public function guardar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;

        // Validaciones
        $errors = [];

        if (empty($data['cliente_id'])) {
            $errors[] = "Debe seleccionar un cliente";
        }

        $productosIds = $data['producto_id'] ?? [];
        $cantidades = $data['cantidad'] ?? [];

        if (empty($productosIds) || empty($cantidades)) {
            $errors[] = "Debe agregar al menos un producto";
        }

        if (!empty($errors)) {
            $_SESSION['ventas_errors'] = $errors;
            $_SESSION['ventas_old'] = $data;
            redirect('/admin/ventas/crear');
            return;
        }

        // Preparar datos
        $productosModel = new ProductosModel();
        $detalles = [];
        $subtotal = 0;

        // Validar y obtener la fecha de la venta
        $fechaVenta = !empty($data['fecha_venta']) ? $data['fecha_venta'] : date('Y-m-d');
        // Validar formato YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaVenta)) {
            $errors[] = "La fecha de la venta no es válida";
        }

        foreach ($productosIds as $index => $productoId) {
            $cantidad = (int)($cantidades[$index] ?? 0);

            if ($cantidad <= 0) {
                continue;
            }

            $producto = $productosModel->obtenerPorId((int)$productoId);

            if (!$producto || $producto['estado'] !== 'ACTIVO') {
                $errors[] = "Producto ID {$productoId} no válido";
                continue;
            }

            if ($producto['stock'] < $cantidad) {
                $errors[] = "Stock insuficiente para {$producto['nombre']}. Disponible: {$producto['stock']}";
                continue;
            }

            $precioUnitario = (float)$producto['precio_venta'];
            $subtotalLinea = $cantidad * $precioUnitario;
            $subtotal += $subtotalLinea;

            $detalles[] = [
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotalLinea,
            ];
        }

        if (!empty($errors)) {
            $_SESSION['ventas_errors'] = $errors;
            $_SESSION['ventas_old'] = $data;
            redirect('/admin/ventas/crear');
            return;
        }

        if (empty($detalles)) {
            $_SESSION['flash_error'] = "No hay productos válidos en la venta";
            redirect('/admin/ventas/crear');
            return;
        }

        // Crear venta
        try {
            $ventaData = [
                'cliente_id' => (int)$data['cliente_id'],
                'usuario_id' => (int)$_SESSION['user']['id'], // TODO: Verificar que exista sesión
                'metodo_pago' => $data['metodo_pago'] ?? 'PENDIENTE', // Valor por defecto
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'detalles' => $detalles,
                'fecha_venta' => $fechaVenta,
            ];

            $ventaId = $this->model->crearVentaManual($ventaData);

            $_SESSION['flash_success'] = "Venta #{$ventaId} creada exitosamente";
            redirect('/admin/ventas');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al crear la venta: " . $e->getMessage();
            redirect('/admin/ventas/crear');
        }
    }

    // =========================
    // VER DETALLE
    // =========================
    public function ver()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $id = (int)($_GET['id'] ?? 0);

        error_log("🔍 [VentasController@ver] ID solicitado: {$id}");
        error_log("🔍 [VentasController@ver] GET params: " . json_encode($_GET));
        error_log("🔍 [VentasController@ver] REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));

        $venta = $this->model->getVentaById($id);

        if (!$venta) {
            error_log("❌ [VentasController@ver] Venta no encontrada para ID: {$id}");
            $_SESSION['flash_error'] = "Venta no encontrada";
            redirect('/admin/ventas');
            return;
        }

        $detalle = $this->model->getVentaDetalle($id);

        $this->viewWithLayout("ventas/views/ver", [
            "title"   => "Ver Venta #{$id}",
            "user"    => $_SESSION["user"],
            "venta"   => $venta,
            "detalle" => $detalle,
        ]);
    }

    // =========================
    // ANULAR VENTA
    // =========================
    public function anular()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;
        $ventaId = (int)($data['venta_id'] ?? 0);

        if ($ventaId <= 0) {
            $_SESSION['flash_error'] = "ID de venta inválido";
            redirect('/admin/ventas');
            return;
        }

        try {
            $usuarioId = (int)$_SESSION['user']['id']; // TODO: Verificar sesión
            $resultado = $this->model->anularVenta($ventaId, $usuarioId);

            if ($resultado) {
                $_SESSION['flash_success'] = "Venta #{$ventaId} anulada exitosamente. Stock y totales revertidos.";
            } else {
                $_SESSION['flash_error'] = "No se pudo anular la venta";
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al anular venta: " . $e->getMessage();
        }

        redirect('/admin/ventas');
    }

    // =========================
    // CONVERTIR DESDE COTIZACIÓN
    // =========================
    public function convertirDesdeCotizacion()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;
        $cotizacionId = (int)($data['cotizacion_id'] ?? 0);

        error_log("🔍 [VentasController] Iniciando conversión cotización ID: {$cotizacionId}");
        error_log("🔍 [VentasController] POST data: " . json_encode($_POST, JSON_PRETTY_PRINT));

        if ($cotizacionId <= 0) {
            error_log("❌ [VentasController] ID de cotización inválido: {$cotizacionId}");
            $_SESSION['flash_error'] = "ID de cotización inválido";
            redirect('/admin/cotizaciones');
            return;
        }

        try {
            $usuarioId = (int)$_SESSION['user']['id'];
            error_log("🔍 [VentasController] Usuario ID: {$usuarioId}");

            $ventaId = $this->model->convertirCotizacion($cotizacionId, $usuarioId);

            error_log("✅ [VentasController] Conversión exitosa. Venta ID: {$ventaId}");
            $_SESSION['flash_success'] = "Cotización #{$cotizacionId} convertida a Venta #{$ventaId} exitosamente";
            redirect('/admin/ventas/ver?id=' . $ventaId);
        } catch (Exception $e) {
            error_log("🚨 [VentasController] Error en conversión: " . $e->getMessage());
            error_log("🚨 [VentasController] Error trace: " . $e->getTraceAsString());
            $_SESSION['flash_error'] = "Error al convertir cotización: " . $e->getMessage();
            redirect('/admin/cotizaciones');
        }
    }
}
