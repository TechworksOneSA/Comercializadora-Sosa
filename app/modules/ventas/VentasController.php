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

    // =========================
    // CREAR VENTA (FORMULARIO)
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        require_once __DIR__ . '/../clientes/ClientesModel.php';
        require_once __DIR__ . '/../productos/ProductosModel.php';

        $clientesModel = new ClientesModel();
        $productosModel = new ProductosModel();

        // Obtener todos los clientes
        $clientes = $clientesModel->listar();

        // Obtener todos los productos activos
        $productos = $productosModel->buscar("", ["estado" => "ACTIVO"]);

        // Series existentes (si aplica, para productos que requieren serie)
        $series_existentes = [];

        $this->viewWithLayout("ventas/views/crear", [
            "title" => "Nueva Venta",
            "user" => $_SESSION['user'],
            "clientes" => $clientes,
            "productos" => $productos,
            "series_existentes" => $series_existentes,
            "errors" => [],
            "old" => []
        ]);
    }

    // =========================
    // GUARDAR VENTA
    // =========================
    public function guardar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $errors = [];
        $old = $_POST;

        // Validar datos básicos
        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $fechaVenta = trim($_POST['fecha_venta'] ?? '');
        $productosIds = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $numerosSerie = $_POST['numero_serie'] ?? [];

        if ($clienteId <= 0) {
            $errors[] = "Debe seleccionar un cliente";
        }

        if (empty($fechaVenta)) {
            $errors[] = "Debe especificar la fecha de la venta";
        }

        if (empty($productosIds) || !is_array($productosIds)) {
            $errors[] = "Debe agregar al menos un producto";
        }

        // Si hay errores, recargar el formulario
        if (!empty($errors)) {
            require_once __DIR__ . '/../clientes/ClientesModel.php';
            require_once __DIR__ . '/../productos/ProductosModel.php';

            $clientesModel = new ClientesModel();
            $productosModel = new ProductosModel();

            $clientes = $clientesModel->listar();
            $productos = $productosModel->buscar("", ["estado" => "ACTIVO"]);

            $this->viewWithLayout("ventas/views/crear", [
                "title" => "Nueva Venta",
                "user" => $_SESSION['user'],
                "clientes" => $clientes,
                "productos" => $productos,
                "series_existentes" => [],
                "errors" => $errors,
                "old" => $old
            ]);
            return;
        }

        try {
            require_once __DIR__ . '/../productos/ProductosModel.php';
            $productosModel = new ProductosModel();

            // Preparar detalles
            $detalles = [];
            $subtotal = 0;

            foreach ($productosIds as $index => $productoId) {
                $productoId = (int)$productoId;
                $cantidad = (int)($cantidades[$index] ?? 0);
                $numeroSerie = trim($numerosSerie[$index] ?? '');

                if ($productoId <= 0 || $cantidad <= 0) {
                    continue;
                }

                // Obtener info del producto
                $producto = $productosModel->obtenerPorId($productoId);
                if (!$producto) {
                    $errors[] = "Producto #{$productoId} no encontrado";
                    continue;
                }

                $precioUnitario = (float)$producto['precio_venta'];
                $subtotalLinea = $precioUnitario * $cantidad;
                $subtotal += $subtotalLinea;

                $detalles[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalLinea,
                    'numero_serie' => $numeroSerie ?: null
                ];
            }

            if (empty($detalles)) {
                throw new Exception("No se pudieron procesar los productos");
            }

            $total = $subtotal; // Por ahora sin descuentos adicionales

            // Preparar datos para el modelo
            $ventaData = [
                'cliente_id' => $clienteId,
                'usuario_id' => (int)$_SESSION['user']['id'],
                'metodo_pago' => 'Efectivo', // Por defecto, se puede ajustar
                'subtotal' => $subtotal,
                'total' => $total,
                'fecha_venta' => $fechaVenta,
                'detalles' => $detalles
            ];

            // Crear la venta
            $ventaId = $this->model->crearVentaManual($ventaData);

            $_SESSION['flash_success'] = "Venta #{$ventaId} creada exitosamente";
            redirect('/admin/ventas/ver?id=' . $ventaId);

        } catch (Throwable $e) {
            error_log("❌ [VentasController@guardar] Error: " . $e->getMessage());
            error_log("❌ Stack trace: " . $e->getTraceAsString());

            $_SESSION['flash_error'] = "Error al crear la venta: " . $e->getMessage();
            redirect('/admin/ventas/crear');
        }
    }
}