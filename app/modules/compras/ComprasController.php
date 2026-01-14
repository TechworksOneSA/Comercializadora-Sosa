<?php

require_once __DIR__ . "/ComprasModel.php";
require_once __DIR__ . "/../productos/ProductosModel.php";
require_once __DIR__ . "/../productos/ProductosSeriesModel.php";
require_once __DIR__ . "/../proveedores/ProveedoresModel.php";

class ComprasController extends Controller
{
    private ComprasModel $model;

    public function __construct()
    {
        $this->model = new ComprasModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdmin();

        $compras = $this->model->listar();

        $this->viewWithLayout("compras/views/index", [
            "title"   => "Compras",
            "user"    => $_SESSION["user"],
            "compras" => $compras,
        ]);
    }

    public function crear()
    {
        RoleMiddleware::requireAdmin();

        $productosModel   = new ProductosModel();
        $proveedoresModel = new ProveedoresModel();
        $seriesModel      = new ProductosSeriesModel();

        $productos   = $productosModel->listarActivos();   // catálogo de productos
        $proveedores = $proveedoresModel->listarActivos(); // catálogo de proveedores

        // ✅ Obtener las series existentes de cada producto
        $seriesExistentes = [];
        foreach ($productos as $producto) {
            if ($producto['tipo_producto'] === 'UNIDAD') {
                $serie = $seriesModel->getSerieDelProducto($producto['id']);
                if ($serie) {
                    $seriesExistentes[$producto['id']] = $serie;
                }
            }
        }

        $errors = $_SESSION['compras_errors'] ?? [];
        $old    = $_SESSION['compras_old'] ?? [];

        unset($_SESSION['compras_errors'], $_SESSION['compras_old']);

        $this->viewWithLayout("compras/views/crear", [
            "title"       => "Registrar compra",
            "user"        => $_SESSION["user"],
            "productos"   => $productos,
            "proveedores" => $proveedores,
            "series_existentes" => $seriesExistentes, // ✅ Pasar series existentes
            "errors"      => $errors,
            "old"         => $old,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdmin();

        $input = $_POST;

        // Encabezado
        $proveedor_id = (int)($input['proveedor_id'] ?? 0);
        $fecha_compra = trim($input['fecha_compra'] ?? date('Y-m-d'));
        $numero_doc   = trim($input['numero_doc'] ?? '');
        $notas        = trim($input['notas'] ?? '');

        // Decodificar productos desde JSON
        $productosJson = $input['productos_json'] ?? '[]';
        $productosData = json_decode($productosJson, true);

        $detalles = [];
        $seriesPorProducto = []; // Almacenar series para cada producto
        $totalBruto = 0;
        $totalDesc  = 0;

        foreach ($productosData as $item) {
            $pid   = (int)($item['id'] ?? 0);
            $cant  = (float)($item['cantidad'] ?? 0);
            $costo = (float)($item['costo_unitario'] ?? 0);
            $desc  = (float)($item['descuento'] ?? 0);
            $tipo  = $item['tipo_producto'] ?? 'MISC';
            $serie = $item['serie'] ?? ''; // ✅ Ahora es una cadena única, no array

            if ($pid <= 0 || $cant <= 0 || $costo < 0) continue;

            $subtotal   = $cant * $costo - $desc;
            $totalBruto += $cant * $costo;
            $totalDesc  += $desc;

            $detalles[] = [
                'producto_id'    => $pid,
                'cantidad'       => $cant,
                'costo_unitario' => $costo,
                'descuento'      => $desc,
                'subtotal'       => $subtotal,
            ];
            
            // ✅ Si el producto aplica serie y se proporcionó, almacenarla
            if ($tipo === 'UNIDAD' && !empty($serie)) {
                $seriesPorProducto[$pid] = $serie; // Ahora es una cadena, no array
            }
        }

        $errors = [];
        if ($proveedor_id <= 0) $errors[] = "El proveedor es obligatorio.";
        if (empty($detalles))  $errors[] = "Debe ingresar al menos un producto en la compra.";

        if ($errors) {
            // Si hay errores, volvemos a cargar catálogos para la vista
            $productosModel   = new ProductosModel();
            $proveedoresModel = new ProveedoresModel();

            $productos   = $productosModel->listarActivos();
            $proveedores = $proveedoresModel->listarActivos();

            $this->view("modules/dashboard/views/_admin_layout", [
                "title"       => "Registrar compra",
                "user"        => $_SESSION["user"],
                "content"     => "compras/views/crear",
                "productos"   => $productos,
                "proveedores" => $proveedores,
                "errors"      => $errors,
                "old"         => $input,
            ]);
            return;
        }

        $totalNeto = $totalBruto - $totalDesc;

        $header = [
            'proveedor_id' => $proveedor_id,
            'usuario_id'   => $_SESSION["user"]["id"] ?? 1,
            'fecha_compra' => $fecha_compra,
            'numero_doc'   => $numero_doc,
            'total_bruto'  => $totalBruto,
            'descuento'    => $totalDesc,
            'total_neto'   => $totalNeto,
            'estado'       => 'REGISTRADA',
            'notas'        => $notas,
        ];

        $compra_id = $this->model->crearCompra($header, $detalles);
        
        // ✅ Guardar/actualizar la serie única de cada producto si se proporcionó
        if (!empty($seriesPorProducto) && $compra_id) {
            $seriesModel = new ProductosSeriesModel();
            
            foreach ($seriesPorProducto as $producto_id => $serie) {
                // $serie es una cadena única, no un array
                if (!empty(trim($serie))) {
                    $seriesModel->guardarSerieUnica($producto_id, trim($serie));
                }
            }
        }

        header("Location: " . url('/admin/compras?msg=Compra registrada e inventario actualizado correctamente'));
        exit;
    }

    public function editar($id)
    {
        RoleMiddleware::requireAdmin();

        $compra = $this->model->obtenerPorId($id);
        if (!$compra) {
            header("Location: " . url('/admin/compras?error=Compra no encontrada'));
            exit;
        }

        $detalles = $this->model->obtenerDetalles($id);

        $productosModel   = new ProductosModel();
        $proveedoresModel = new ProveedoresModel();

        $productos   = $productosModel->listarActivos();
        $proveedores = $proveedoresModel->listarActivos();

        $errors = $_SESSION['compras_errors'] ?? [];
        $old    = $_SESSION['compras_old'] ?? [];

        unset($_SESSION['compras_errors'], $_SESSION['compras_old']);

        $this->viewWithLayout("compras/views/editar", [
            "title"       => "Editar compra #" . $id,
            "user"        => $_SESSION["user"],
            "compra"      => $compra,
            "detalles"    => $detalles,
            "productos"   => $productos,
            "proveedores" => $proveedores,
            "errors"      => $errors,
            "old"         => $old,
        ]);
    }

    public function actualizar($id)
    {
        RoleMiddleware::requireAdmin();

        $compra = $this->model->obtenerPorId($id);
        if (!$compra) {
            header("Location: " . url('/admin/compras?error=Compra no encontrada'));
            exit;
        }

        $input = $_POST;

        $proveedor_id = (int)($input['proveedor_id'] ?? 0);
        $fecha_compra = trim($input['fecha_compra'] ?? date('Y-m-d'));
        $numero_doc   = trim($input['numero_doc'] ?? '');
        $notas        = trim($input['notas'] ?? '');

        $productosJson = $input['productos_json'] ?? '[]';
        $productosData = json_decode($productosJson, true);

        $detalles   = [];
        $totalBruto = 0;
        $totalDesc  = 0;

        foreach ($productosData as $item) {
            $pid   = (int)($item['id'] ?? 0);
            $cant  = (float)($item['cantidad'] ?? 0);
            $costo = (float)($item['costo_unitario'] ?? 0);
            $desc  = (float)($item['descuento'] ?? 0);

            if ($pid <= 0 || $cant <= 0 || $costo < 0) continue;

            $subtotal   = $cant * $costo - $desc;
            $totalBruto += $cant * $costo;
            $totalDesc  += $desc;

            $detalles[] = [
                'producto_id'    => $pid,
                'cantidad'       => $cant,
                'costo_unitario' => $costo,
                'descuento'      => $desc,
                'subtotal'       => $subtotal,
            ];
        }

        $errors = [];
        if ($proveedor_id <= 0) $errors[] = "El proveedor es obligatorio.";
        if (empty($detalles))  $errors[] = "Debe ingresar al menos un producto en la compra.";

        if ($errors) {
            $_SESSION['compras_errors'] = $errors;
            $_SESSION['compras_old'] = $input;
            header("Location: " . url('/admin/compras/editar/' . $id));
            exit;
        }

        $totalNeto = $totalBruto - $totalDesc;

        $header = [
            'proveedor_id' => $proveedor_id,
            'fecha_compra' => $fecha_compra,
            'numero_doc'   => $numero_doc,
            'total_bruto'  => $totalBruto,
            'descuento'    => $totalDesc,
            'total_neto'   => $totalNeto,
            'notas'        => $notas,
        ];

        $this->model->actualizarCompra($id, $header, $detalles);

        header("Location: " . url('/admin/compras?msg=Compra actualizada correctamente'));
        exit;
    }

    public function eliminar($id)
    {
        RoleMiddleware::requireAdmin();

        $compra = $this->model->obtenerPorId($id);
        if (!$compra) {
            header("Location: " . url('/admin/compras?error=Compra no encontrada'));
            exit;
        }

        try {
            $this->model->eliminarCompra($id);
            header("Location: " . url('/admin/compras?msg=Compra eliminada e inventario revertido correctamente'));
        } catch (Exception $e) {
            header("Location: " . url('/admin/compras?error=Error al eliminar la compra: ' . $e->getMessage()));
        }
        exit;
    }

    public function ver($id)
    {
        RoleMiddleware::requireAdmin();

        $compra = $this->model->obtenerPorId($id);
        if (!$compra) {
            header("Location: " . url('/admin/compras?error=Compra no encontrada'));
            exit;
        }

        $detalles = $this->model->obtenerDetalles($id);

        $this->viewWithLayout("compras/views/ver", [
            "title"    => "Ver compra #" . $id,
            "user"     => $_SESSION["user"],
            "compra"   => $compra,
            "detalles" => $detalles,
        ]);
    }
}
