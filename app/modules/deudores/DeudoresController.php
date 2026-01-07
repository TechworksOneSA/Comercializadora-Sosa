<?php

require_once __DIR__ . "/DeudoresModel.php";
require_once __DIR__ . "/../clientes/ClientesModel.php";
require_once __DIR__ . "/../productos/ProductosModel.php";

class DeudoresController extends Controller
{
    private DeudoresModel $model;

    public function __construct()
    {
        $this->model = new DeudoresModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $deudas = $this->model->getAllDeudas();

        $this->viewWithLayout("deudores/views/index", [
            "title" => "Deudores",
            "user" => $_SESSION['user'],
            "deudas" => $deudas,
        ]);
    }

    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $clientesModel = new ClientesModel();
        $productosModel = new ProductosModel();

        $clientes = $clientesModel->listar();
        $productos = $productosModel->listarActivos();

        $errors = $_SESSION['deudores_errors'] ?? [];
        $old = $_SESSION['deudores_old'] ?? [];
        unset($_SESSION['deudores_errors'], $_SESSION['deudores_old']);

        $this->viewWithLayout("deudores/views/crear", [
            "title" => "Nueva Deuda",
            "user" => $_SESSION['user'],
            "clientes" => $clientes,
            "productos" => $productos,
            "errors" => $errors,
            "old" => $old,
        ]);
    }

    // Endpoint temporal para depuración: devuelve clientes en JSON
    public function debugClientes()
    {
        RoleMiddleware::requireAdminOrVendedor();
        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->listar();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['count' => count($clientes), 'rows' => $clientes], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function guardar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;
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
            $_SESSION['deudores_errors'] = $errors;
            $_SESSION['deudores_old'] = $data;
            redirect('/admin/deudores/crear');
            return;
        }

        // Preparar datos de productos
        $productosModel = new ProductosModel();
        $detalles = [];
        $subtotal = 0;

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
            $_SESSION['deudores_errors'] = $errors;
            $_SESSION['deudores_old'] = $data;
            redirect('/admin/deudores/crear');
            return;
        }

        if (empty($detalles)) {
            $_SESSION['flash_error'] = "No hay productos válidos en la deuda";
            redirect('/admin/deudores/crear');
            return;
        }

        // Crear deuda
        try {
            $deudaData = [
                'cliente_id' => (int)$data['cliente_id'],
                'usuario_id' => (int)$_SESSION['user']['id'],
                'total' => $subtotal,
                'descripcion' => $data['descripcion'] ?? '',
                'detalles' => $detalles,
            ];

            $deudaId = $this->model->crearDeuda($deudaData);

            if (!$deudaId) {
                throw new Exception("No se pudo obtener el ID de la deuda creada");
            }

            $_SESSION['flash_success'] = "Deuda #{$deudaId} creada exitosamente";
            redirect('/admin/deudores');
        } catch (Exception $e) {
            // Debug: mostrar error directamente
            echo "<h1>Error al crear deuda:</h1>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "<a href='/admin/deudores/crear'>Volver</a>";
            exit;

            $_SESSION['flash_error'] = "Error al crear la deuda: " . $e->getMessage();
            error_log("Error creando deuda: " . $e->getMessage());
            redirect('/admin/deudores/crear');
        }
    }

    public function ver()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = "ID inválido";
            redirect('/admin/deudores');
            return;
        }

        $deuda = $this->model->getDeudaById($id);
        if (!$deuda) {
            $_SESSION['flash_error'] = "Deuda no encontrada";
            redirect('/admin/deudores');
            return;
        }

        $detalle = $this->model->getDetalleProductos($id);
        $pagos = $this->model->getPagosByDeuda($id);

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->viewWithLayout("deudores/views/ver", [
            "title" => "Deuda #{$id}",
            "user" => $_SESSION['user'],
            "deuda" => $deuda,
            "detalle" => $detalle,
            "pagos" => $pagos,
            "success" => $success,
            "error" => $error,
        ]);
    }

    public function registrarPago()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;
        $deudaId = (int)($data['deuda_id'] ?? 0);
        $monto = (float)($data['monto'] ?? 0);
        $metodoPago = trim($data['metodo_pago'] ?? '');

        if ($deudaId <= 0 || $monto <= 0 || empty($metodoPago)) {
            $_SESSION['flash_error'] = "❌ Datos de pago inválidos. Verifique monto y método de pago.";
            redirect('/admin/deudores/ver?id=' . $deudaId);
            return;
        }

        try {
            $resultado = $this->model->registrarPago($deudaId, $monto, (int)$_SESSION['user']['id'], $metodoPago);

            // Verificar si se convirtió automáticamente a venta
            $deudaActualizada = $this->model->getDeudaById($deudaId);

            if ($deudaActualizada && $deudaActualizada['estado'] === 'CONVERTIDA') {
                $_SESSION['flash_success'] = "🎉 ¡Pago registrado exitosamente! La deuda se ha convertido automáticamente a VENTA #" .
                    ($deudaActualizada['venta_generada_id'] ?? 'N/A') .
                    " porque está completamente saldada.";
            } else {
                $_SESSION['flash_success'] = "✅ Pago de Q" . number_format($monto, 2) . " registrado exitosamente con " . htmlspecialchars($metodoPago);
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "❌ Error registrando pago: " . $e->getMessage();
        }

        redirect('/admin/deudores/ver?id=' . $deudaId);
    }

    public function ampliar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;
        $deudaId = (int)($data['deuda_id'] ?? 0);
        $monto = (float)($data['monto'] ?? 0);

        if ($deudaId <= 0 || $monto <= 0) {
            $_SESSION['flash_error'] = "Datos inválidos";
            redirect('/admin/deudores');
            return;
        }

        try {
            $this->model->ampliarDeuda($deudaId, $monto);
            $_SESSION['flash_success'] = "Deuda ampliada";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error ampliando deuda: " . $e->getMessage();
        }

        redirect('/admin/deudores/ver?id=' . $deudaId);
    }
}
