<?php

require_once __DIR__ . "/CotizacionesModel.php";
require_once __DIR__ . "/../clientes/ClientesModel.php";
require_once __DIR__ . "/../productos/ProductosModel.php";

class CotizacionesController extends Controller
{
    private CotizacionesModel $model;

    public function __construct()
    {
        $this->model = new CotizacionesModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        // Actualizar estados de cotizaciones vencidas antes de listar
        $this->model->marcarVencidas();

        $cotizaciones = $this->model->listar();
        $stats = $this->model->obtenerEstadisticas();

        // Mensajes flash
        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->viewWithLayout("cotizaciones/views/index", [
            "title"        => "Cotizaciones",
            "user"         => $_SESSION["user"],
            "cotizaciones" => $cotizaciones,
            "stats"        => $stats,
            "success"      => $success,
            "error"        => $error,
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $clientesModel = new ClientesModel();
        $productosModel = new ProductosModel();
        require_once __DIR__ . '/../productos/ProductosSeriesModel.php';
        $seriesModel = new ProductosSeriesModel();

        $clientes = $clientesModel->listar();
        $productos = $productosModel->listarActivos();

        // ✅ Cargar series existentes
        $series_existentes = [];
        foreach ($productos as $producto) {
            $serie = $seriesModel->getSerieDelProducto($producto['id']);
            if ($serie) {
                $series_existentes[$producto['id']] = $serie;
            }
        }

        $errors = $_SESSION['cotizaciones_errors'] ?? [];
        $old = $_SESSION['cotizaciones_old'] ?? [];

        unset($_SESSION['cotizaciones_errors'], $_SESSION['cotizaciones_old']);

        $this->viewWithLayout("cotizaciones/views/crear", [
            "title"     => "Nueva Cotización",
            "user"      => $_SESSION["user"],
            "clientes"  => $clientes,
            "productos" => $productos,
            "series_existentes" => $series_existentes,
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
        $errors = $this->validarCotizacion($data);

        if (!empty($errors)) {
            $_SESSION['cotizaciones_errors'] = $errors;
            $_SESSION['cotizaciones_old'] = $data;
            redirect('/admin/cotizaciones/crear');
            return;
        }

        // Validar productos y stock
        $productosModel = new ProductosModel();
        $productosIds = $data['producto_id'] ?? [];
        $cantidades = $data['cantidad'] ?? [];

        if (empty($productosIds)) {
            $_SESSION['flash_error'] = "Debes agregar al menos un producto a la cotización.";
            $_SESSION['cotizaciones_old'] = $data;
            redirect('/admin/cotizaciones/crear');
            return;
        }

        // Validar stock y calcular totales
        $detalles = [];
        $subtotal = 0;

        for ($i = 0; $i < count($productosIds); $i++) {
            $productoId = (int)$productosIds[$i];
            $cantidad = (int)$cantidades[$i];

            $producto = $productosModel->obtenerPorId($productoId);

            if (!$producto) {
                $_SESSION['flash_error'] = "Producto con ID {$productoId} no existe.";
                $_SESSION['cotizaciones_old'] = $data;
                redirect('/admin/cotizaciones/crear');
                return;
            }

            if ($cantidad > $producto['stock']) {
                $_SESSION['flash_error'] = "Stock insuficiente para {$producto['nombre']}. Disponible: {$producto['stock']}";
                $_SESSION['cotizaciones_old'] = $data;
                redirect('/admin/cotizaciones/crear');
                return;
            }

            if ($cantidad <= 0) {
                continue; // Ignorar líneas con cantidad 0
            }

            $precioUnitario = (float)$producto['precio_venta'];
            $totalLinea = $cantidad * $precioUnitario;
            $subtotal += $totalLinea;

            $detalles[] = [
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'total_linea' => $totalLinea,
            ];
        }

        if (empty($detalles)) {
            $_SESSION['flash_error'] = "No hay productos válidos en la cotización.";
            $_SESSION['cotizaciones_old'] = $data;
            redirect('/admin/cotizaciones/crear');
            return;
        }

        // Calcular fecha de expiración
        $diasValidez = (int)($data['dias_validez'] ?? 7);
        $fechaExpiracion = date('Y-m-d', strtotime("+{$diasValidez} days"));

        // Preparar datos de la cabecera
        $cabecera = [
            'cliente_id' => (int)$data['cliente_id'],
            'fecha' => date('Y-m-d'),
            'fecha_expiracion' => $fechaExpiracion,
            'estado' => 'ACTIVA',
            'subtotal' => $subtotal,
            'total' => $subtotal, // Por ahora sin descuentos/impuestos
        ];

        // Guardar cotización con detalle
        $cotizacionId = $this->model->crear($cabecera, $detalles);

        $_SESSION['flash_success'] = "Cotización #{$cotizacionId} creada exitosamente. Vence el " . date('d/m/Y', strtotime($fechaExpiracion)) . ".";
        redirect('/admin/cotizaciones');
    }

    // =========================
    // VER DETALLE
    // =========================
    public function ver($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $cotizacion = $this->model->obtenerPorId((int)$id);

        if (!$cotizacion) {
            $_SESSION['flash_error'] = "Cotización no encontrada.";
            redirect('/admin/cotizaciones');
            return;
        }

        $detalle = $this->model->obtenerDetalle((int)$id);

        $this->viewWithLayout("cotizaciones/views/ver", [
            "title"      => "Ver Cotización #{$id}",
            "user"       => $_SESSION["user"],
            "cotizacion" => $cotizacion,
            "detalle"    => $detalle,
        ]);
    }

    // =========================
    // CONVERTIR A VENTA
    // =========================
    public function convertir($id)
    {
        error_log("🔍 [CotizacionesController] Iniciando convertir cotización ID: {$id}");
        RoleMiddleware::requireAdminOrVendedor();

        $cotizacion = $this->model->obtenerPorId((int)$id);

        if (!$cotizacion) {
            $_SESSION['flash_error'] = "Cotización no encontrada.";
            redirect('/admin/cotizaciones');
            return;
        }

        // Validar que no esté vencida
        if ($cotizacion['estado'] === 'VENCIDA') {
            $_SESSION['flash_error'] = "No se puede convertir una cotización vencida.";
            redirect('/admin/cotizaciones');
            return;
        }

        // Validar que no esté ya convertida
        if ($cotizacion['estado'] === 'CONVERTIDA') {
            $_SESSION['flash_error'] = "Esta cotización ya fue convertida a venta.";
            redirect('/admin/cotizaciones');
            return;
        }

        // Llamar al VentasController para crear la venta desde la cotización
        require_once __DIR__ . "/../ventas/VentasController.php";
        require_once __DIR__ . "/../ventas/VentasModel.php";

        $ventasModel = new VentasModel();

        try {
            $usuarioId = (int)$_SESSION['user']['id'];
            $ventaId = $ventasModel->convertirCotizacion((int)$id, $usuarioId);

            $_SESSION['flash_success'] = "Cotización #{$id} convertida a Venta #{$ventaId} exitosamente";
            redirect('/admin/ventas/ver?id=' . $ventaId);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error al convertir cotización: " . $e->getMessage();
            redirect('/admin/cotizaciones');
        }
    }

    // =========================
    // ELIMINAR
    // =========================
    public function eliminar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $cotizacion = $this->model->obtenerPorId((int)$id);

        if (!$cotizacion) {
            $_SESSION['flash_error'] = "Cotización no encontrada.";
            redirect('/admin/cotizaciones');
            return;
        }

        $this->model->eliminar((int)$id);

        $_SESSION['flash_success'] = "Cotización #{$id} eliminada correctamente.";
        redirect('/admin/cotizaciones');
    }

    // =========================
    // LIMPIAR VENCIDAS
    // =========================
    public function limpiarVencidas()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $eliminadas = $this->model->eliminarVencidas();

        $_SESSION['flash_success'] = "Se eliminaron {$eliminadas} cotización(es) vencida(s).";
        redirect('/admin/cotizaciones');
    }

    // =========================
    // VALIDACIONES
    // =========================
    private function validarCotizacion(array $data): array
    {
        $errors = [];

        if (empty($data['cliente_id']) || (int)$data['cliente_id'] <= 0) {
            $errors[] = "Debes seleccionar un cliente.";
        }

        $diasValidez = (int)($data['dias_validez'] ?? 0);
        if ($diasValidez < 1 || $diasValidez > 30) {
            $errors[] = "Los días de validez deben estar entre 1 y 30.";
        }

        return $errors;
    }
}
