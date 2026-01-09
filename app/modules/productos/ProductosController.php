<?php

require_once __DIR__ . "/ProductosModel.php";
require_once __DIR__ . "/../categorias/CategoriasModel.php";
require_once __DIR__ . "/../marcas/MarcasModel.php";

class ProductosController extends Controller
{
    private ProductosModel $model;

    public function __construct()
    {
        $this->model = new ProductosModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $q = trim($_GET["q"] ?? "");

        $filters = [
            "categoria_id" => (int)($_GET["categoria_id"] ?? 0),
            "marca_id"     => (int)($_GET["marca_id"] ?? 0),
            "stock"        => $_GET["stock"] ?? "all",     // all | bajo | cero
            "estado"       => $_GET["estado"] ?? "ALL",    // ALL | ACTIVO | INACTIVO
            "tipo"         => $_GET["tipo"] ?? "ALL",      // ALL | NORMAL | SERIE
        ];

        $productos = $this->model->buscar($q, $filters);
        $kpis      = $this->model->getKpis();

        // catálogos para filtros
        $categoriasModel = new CategoriasModel();
        $marcasModel     = new MarcasModel();

        $categorias = $categoriasModel->listarActivas();
        $marcas     = $marcasModel->listarActivas();

        $this->viewWithLayout("productos/views/index", [
            "title"      => "Inventario",
            "user"       => $_SESSION["user"],
            "productos"  => $productos,
            "kpis"       => $kpis,
            "q"          => $q,
            "filters"    => $filters,
            "categorias" => $categorias,
            "marcas"     => $marcas,
        ]);
    }

    // =========================
    // TABLA (AJAX - BÚSQUEDA EN VIVO + FILTROS)
    // =========================
    public function tabla()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $q = trim($_GET["q"] ?? "");

        $filters = [
            "categoria_id" => (int)($_GET["categoria_id"] ?? 0),
            "marca_id"     => (int)($_GET["marca_id"] ?? 0),
            "stock"        => $_GET["stock"] ?? "all",
            "estado"       => $_GET["estado"] ?? "ALL",
            "tipo"         => $_GET["tipo"] ?? "ALL",
        ];

        $productos = $this->model->buscar($q, $filters);

        $this->viewOnly("productos/views/tabla", [
            "productos" => $productos
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        require_once __DIR__ . '/../clasificacion/ClasificacionModel.php';
        $clasificacionModel = new ClasificacionModel();
        $categorias = $clasificacionModel->listarCategorias();

        if (isset($_GET['modal']) && $_GET['modal'] == '1') {
            $this->viewOnly("productos/views/crear", [
                "errors"     => [],
                "old"        => [],
                "categorias" => $categorias,
            ]);
            return;
        }

        $this->viewWithLayout("productos/views/crear", [
            "title"      => "Crear Producto",
            "user"       => $_SESSION["user"],
            "errors"     => [],
            "old"        => [],
            "categorias" => $categorias,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data   = $this->sanitizar($_POST);
        
        // Generar SKU automáticamente
        $tipoProducto = strtoupper(trim($data["tipo_producto"] ?? "UNIDAD"));
        $skuGenerado = $this->generarSKU($tipoProducto);
        $data["sku"] = $skuGenerado;
        
        $errors = $this->validar($data);

        if ($errors) {
            $categoriasModel = new CategoriasModel();
            $marcasModel     = new MarcasModel();

            $categorias = $categoriasModel->listarActivas();
            $marcas     = $marcasModel->listarActivas();

            $this->viewWithLayout("productos/views/crear", [
                "title"      => "Crear Producto",
                "user"       => $_SESSION["user"],
                "errors"     => $errors,
                "old"        => $data,
                "categorias" => $categorias,
                "marcas"     => $marcas,
            ]);
            return;
        }

        $imagenesGuardadas = [];
        if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
            $imagenesGuardadas = $this->procesarImagenes($_FILES['fotos']);
        }

        // Para UNIDAD (código de barras), guardar el código de barras
        // Para MISC, no hay código de barras
        $codigoBarra = null;
        if ($tipoProducto === "UNIDAD") {
            $codigoBarra = trim($data["codigo_barra"] ?? "");
            $codigoBarra = empty($codigoBarra) ? null : $codigoBarra;
        }

        $payload = [
            "sku"              => $skuGenerado,
            "codigo_barra"     => $codigoBarra,
            "nombre"           => trim($data["nombre"] ?? ""),
            "tipo_producto"    => $tipoProducto,
            "categoria_id"     => (int)($data["categoria_id"] ?? 1),
            "marca_id"         => (int)($data["marca_id"] ?? 1),
            "unidad_medida_id" => 1,

            "precio_venta"     => (float)($data["precio"] ?? 0),
            "costo_actual"     => (float)($data["costo"] ?? 0),

            "stock"            => 0,
            "stock_minimo"     => (int)($data["stock_minimo"] ?? 5),

            "descripcion"      => "",
            "activo"           => 1,
            "estado"           => "ACTIVO",
        ];

        try {
            $resultado = $this->model->crear($payload);

            if (!$resultado) {
                error_log("Error al crear producto: modelo retornó false");
                redirect('/admin/productos/crear?error=Error al crear el producto');
                return;
            }

            if (!empty($imagenesGuardadas)) {
                error_log("Producto creado con imágenes: " . implode(', ', $imagenesGuardadas));
            }

            redirect('/admin/productos?ok=creado');
        } catch (Exception $e) {
            error_log("Excepción al crear producto: " . $e->getMessage());
            redirect('/admin/productos/crear?error=Error interno: ' . $e->getMessage());
        }
    }

    // =========================
    // EDITAR
    // =========================
    public function editar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $producto = $this->model->obtenerPorId((int)$id);
        if (!$producto) {
            header("Location: /admin/productos?err=noexiste");
            exit;
        }

        $categoriasModel = new CategoriasModel();
        $marcasModel     = new MarcasModel();

        $categorias = $categoriasModel->listarActivas();
        $marcas     = $marcasModel->listarActivas();

        if (isset($_GET['modal']) && $_GET['modal'] == '1') {
            $this->viewOnly("productos/views/editar", [
                "errors"     => [],
                "producto"   => $producto,
                "categorias" => $categorias,
                "marcas"     => $marcas,
            ]);
            return;
        }

        $this->viewWithLayout("productos/views/editar", [
            "title"      => "Editar Producto",
            "user"       => $_SESSION["user"],
            "errors"     => [],
            "producto"   => $producto,
            "categorias" => $categorias,
            "marcas"     => $marcas,
        ]);
    }

    public function actualizar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $producto = $this->model->obtenerPorId((int)$id);
        if (!$producto) {
            header("Location: /admin/productos?err=noexiste");
            exit;
        }

        $data   = $this->sanitizar($_POST);
        $errors = $this->validar($data);

        if ($this->model->skuExiste($data["sku"], (int)$id)) {
            $errors[] = "El SKU ya existe en otro producto.";
        }
        if (!empty($data["codigo_barra"]) && $this->model->codigoExiste($data["codigo_barra"], (int)$id)) {
            $errors[] = "El código de barras/QR ya existe en otro producto.";
        }

        if ($errors) {
            $categoriasModel = new CategoriasModel();
            $marcasModel     = new MarcasModel();

            $categorias = $categoriasModel->listarActivas();
            $marcas     = $marcasModel->listarActivas();

            $this->viewWithLayout("productos/views/editar", [
                "title"      => "Editar Producto",
                "user"       => $_SESSION["user"],
                "errors"     => $errors,
                "producto"   => array_merge($producto, $data),
                "categorias" => $categorias,
                "marcas"     => $marcas,
            ]);
            return;
        }

        $payload = [
            "sku"              => $data["sku"],
            "codigo_barra"     => $data["codigo_barra"],
            "nombre"           => $data["nombre"],
            "tipo_producto"    => $data["tipo_producto"] ?? "UNIDAD",
            "categoria_id"     => $data["categoria_id"],
            "marca_id"         => $data["marca_id"],
            "unidad_medida_id" => 1,
            "costo_actual"     => $data["costo"],
            "precio_venta"     => $data["precio"],
            "stock"            => $data["stock"],
            "stock_minimo"     => $data["stock_minimo"],
            "descripcion"      => $producto["descripcion"] ?? "",
            "estado"           => $producto["estado"] ?? "ACTIVO",
        ];

        $this->model->actualizar((int)$id, $payload);

        header("Location: /admin/productos?ok=actualizado");
        exit;
    }

    public function desactivar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $resultado = $this->model->desactivar((int)$id);

        if ($resultado) {
            header("Location: " . url('/admin/productos?msg=Producto desactivado correctamente'));
        } else {
            header("Location: " . url('/admin/productos?error=No se pudo desactivar el producto'));
        }
        exit;
    }

    public function activar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $resultado = $this->model->activar((int)$id);

        if ($resultado) {
            header("Location: " . url('/admin/productos?msg=Producto activado correctamente'));
        } else {
            header("Location: " . url('/admin/productos?error=No se pudo activar el producto'));
        }
        exit;
    }

    private function sanitizar(array $input): array
    {
        return [
            "sku"           => strtoupper(trim($input["sku"] ?? "")),
            "codigo_barra"  => trim($input["codigo_barra"] ?? ""),
            "nombre"        => trim($input["nombre"] ?? ""),
            "categoria_id"  => (int)($input["categoria_id"] ?? 0),
            "marca_id"      => (int)($input["marca_id"] ?? 0),
            "costo"         => (float)($input["costo"] ?? 0),
            "precio"        => (float)($input["precio"] ?? 0),
            "stock"         => (int)($input["stock"] ?? 0),
            "stock_minimo"  => (int)($input["stock_minimo"] ?? 0),
            "requiere_serie" => isset($input["requiere_serie"]) ? 1 : 0,
        ];
    }

    private function validar(array $data): array
    {
        $errors = [];
        // El SKU ya no es obligatorio porque se genera automáticamente
        if (empty($data["nombre"])) $errors[] = "Nombre es obligatorio.";
        if ($data["precio"] < $data["costo"]) $errors[] = "Precio no puede ser menor al costo.";
        if ($data["categoria_id"] <= 0) $errors[] = "Categoría es obligatoria.";
        if ($data["marca_id"] <= 0) $errors[] = "Marca es obligatoria.";
        if ($data["stock"] < 0) $errors[] = "Stock no puede ser negativo.";
        if ($data["stock_minimo"] < 0) $errors[] = "Stock mínimo no puede ser negativo.";
        return $errors;
    }

    private function procesarImagenes($files)
    {
        $imagenesGuardadas = [];
        $uploadDir = __DIR__ . '/../../../public/uploads/productos/';

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        if (empty($files['name'][0])) return $imagenesGuardadas;

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024;

        $totalFiles = count($files['name']);
        for ($i = 0; $i < $totalFiles && $i < 3; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            if ($files['size'][$i] > $maxFileSize) continue;

            $extension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions)) continue;

            $uniqueName = uniqid('prod_', true) . '_' . time() . '.' . $extension;
            $destino = $uploadDir . $uniqueName;

            if (move_uploaded_file($files['tmp_name'][$i], $destino)) {
                $imagenesGuardadas[] = $uniqueName;
            }
        }

        return $imagenesGuardadas;
    }

    public function eliminarPermanente($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        try {
            $this->model->eliminar((int)$id);
            $_SESSION['ok'] = "Producto eliminado permanentemente.";
        } catch (Exception $e) {
            $_SESSION['err'] = "Error al eliminar: " . $e->getMessage();
        }

        header("Location: " . url('/admin/productos'));
        exit;
    }

    /**
     * Genera un SKU único automáticamente
     * Formato: Comer_sosa_[número aleatorio de 5 dígitos]
     */
    private function generarSKU($tipoProducto = "UNIDAD")
    {
        $intentos = 0;
        $maxIntentos = 10;
        
        do {
            $numeroAleatorio = mt_rand(10000, 99999);
            $skuGenerado = "Comer_sosa_" . $numeroAleatorio;
            
            // Verificar si ya existe
            if (!$this->model->skuExiste($skuGenerado)) {
                return $skuGenerado;
            }
            
            $intentos++;
        } while ($intentos < $maxIntentos);
        
        // Si después de 10 intentos no se genera uno único, agregar timestamp
        return "Comer_sosa_" . mt_rand(10000, 99999) . "_" . time();
    }
}
