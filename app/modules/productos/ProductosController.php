<?php

require_once __DIR__ . "/ProductosModel.php";
require_once __DIR__ . "/../categorias/CategoriasModel.php";
require_once __DIR__ . "/../marcas/MarcasModel.php";

class ProductosController extends Controller
{
    private ProductosModel $model;

    // ===== Storage persistente (FUERA del repo) =====
    private string $UPLOAD_BASE_DIR   = '/srv/storage/comercializadora/uploads';
    private string $UPLOAD_PUBLIC_DIR = '/uploads';

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
            "stock"        => $_GET["stock"] ?? "all",   // all | bajo | cero
            "estado"       => $_GET["estado"] ?? "ALL",  // ALL | ACTIVO | INACTIVO
            "tipo"         => $_GET["tipo"] ?? "ALL",    // ALL | NORMAL | SERIE
        ];

        $productos = $this->model->buscar($q, $filters);
        $kpis      = $this->model->getKpis();

        $categoriasModel = new CategoriasModel();
        $marcasModel     = new MarcasModel();

        $categorias = $categoriasModel->listarActivas();
        $marcas     = $marcasModel->listarActivas();

        $vistaIndex = "productos/views/index";
        if (($_SESSION['user']['rol'] ?? '') === 'VENDEDOR') {
            $vistaIndex = "productos/views/index_vendedor";
        }

        $this->viewWithLayout($vistaIndex, [
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
    // TABLA (AJAX)
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

        $vistaTabla = "productos/views/tabla";
        if (($_SESSION['user']['rol'] ?? '') === 'VENDEDOR') {
            $vistaTabla = "productos/views/tabla_vendedor";
        }

        $this->viewOnly($vistaTabla, [
            "productos" => $productos
        ]);
    }

    // =========================
    // CREAR (FORM)
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        require_once __DIR__ . '/../clasificacion/ClasificacionModel.php';
        $clasificacionModel = new ClasificacionModel();
        $categorias = $clasificacionModel->listarCategorias();

        if (($_GET['modal'] ?? null) == '1') {
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

    // =========================
    // GUARDAR (POST)
    // =========================
    public function guardar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $this->sanitizar($_POST);

        $tipoProducto = strtoupper(trim($data["tipo_producto"] ?? "UNIDAD"));
        $skuGenerado  = $this->generarSKU();

        $errors = $this->validar($data);
        if (!empty($errors)) {
            $categoriasModel = new CategoriasModel();
            $marcasModel     = new MarcasModel();

            $this->viewWithLayout("productos/views/crear", [
                "title"      => "Crear Producto",
                "user"       => $_SESSION["user"],
                "errors"     => $errors,
                "old"        => $data,
                "categorias" => $categoriasModel->listarActivas(),
                "marcas"     => $marcasModel->listarActivas(),
            ]);
            return;
        }

        // ===== Imagen (single) =====
        $imagenPath = null;
        if (isset($_FILES['imagen']) && ($_FILES['imagen']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $fileName = $this->procesarImagenUnica($_FILES['imagen']);
            if ($fileName) {
                // Guardamos ruta pública absoluta (consumida por el navegador)
                $imagenPath = $this->UPLOAD_PUBLIC_DIR . '/productos/' . $fileName;
            }
        }

        // Código de barra solo para UNIDAD
        $codigoBarra = null;
        if ($tipoProducto === "UNIDAD") {
            $cb = trim($data["codigo_barra"] ?? "");
            $codigoBarra = $cb !== "" ? $cb : null;
        }

        $payload = [
            "sku"              => $skuGenerado,
            "codigo_barra"     => $codigoBarra,
            "nombre"           => trim($data["nombre"] ?? ""),
            "tipo_producto"    => $tipoProducto,
            "categoria_id"     => (int)$data["categoria_id"],
            "marca_id"         => (int)$data["marca_id"],
            "unidad_medida_id" => 1,

            "precio_venta"     => (float)$data["precio"],
            "costo_actual"     => (float)$data["costo"],

            "stock"            => 0,
            "stock_minimo"     => (int)$data["stock_minimo"],

            "descripcion"      => "",
            "activo"           => 1,
            "estado"           => "ACTIVO",

            // ✅ Número de serie
            "numero_serie"     => trim($data["numero_serie"] ?? ''),

            // ✅ ÚNICO CAMPO REAL EN DB
            "imagen_path"      => $imagenPath,
        ];

        try {
            $result = $this->model->crear($payload);
            if (!$result) {
                error_log("Error al crear producto - modelo retornó false");
                redirect('/admin/productos/crear?error=No se pudo crear el producto');
                return;
            }

            redirect('/admin/productos?ok=creado');
        } catch (Exception $e) {
            error_log("Error creando producto: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            redirect('/admin/productos/crear?error=' . urlencode($e->getMessage()));
        }
    }

    // =========================
    // EDITAR (FORM)
    // =========================
    public function editar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $producto = $this->model->obtenerPorId((int)$id);
        if (!$producto) {
            redirect("/admin/productos?err=noexiste");
            return;
        }

        // ✅ Obtener el número de serie si existe
        require_once __DIR__ . '/ProductosSeriesModel.php';
        $seriesModel = new ProductosSeriesModel();
        $numeroSerie = $seriesModel->getSerieDelProducto((int)$id);

        $categoriasModel = new CategoriasModel();
        $marcasModel     = new MarcasModel();

        $this->viewWithLayout("productos/views/editar", [
            "title"      => "Editar Producto",
            "user"       => $_SESSION["user"],
            "producto"   => $producto,
            "numero_serie" => $numeroSerie, // ✅ Pasar la serie a la vista
            "categorias" => $categoriasModel->listarActivas(),
            "marcas"     => $marcasModel->listarActivas(),
            "errors"     => [],
        ]);
    }

    // =========================
    // ACTUALIZAR (POST)
    // =========================
    public function actualizar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $producto = $this->model->obtenerPorId((int)$id);
        if (!$producto) {
            redirect("/admin/productos?err=noexiste");
            return;
        }

        $data   = $this->sanitizar($_POST);
        $errors = $this->validar($data);

        if (!empty($errors)) {
            $categoriasModel = new CategoriasModel();
            $marcasModel     = new MarcasModel();

            // ✅ Obtener número de serie para mantenerlo en caso de error
            require_once __DIR__ . '/ProductosSeriesModel.php';
            $seriesModel = new ProductosSeriesModel();
            $numeroSerie = $seriesModel->getSerieDelProducto((int)$id);

            $this->viewWithLayout("productos/views/editar", [
                "title"      => "Editar Producto",
                "user"       => $_SESSION["user"],
                "errors"     => $errors,
                "producto"   => array_merge($producto, $data),
                "numero_serie" => $numeroSerie, // ✅ Pasar la serie
                "categorias" => $categoriasModel->listarActivas(),
                "marcas"     => $marcasModel->listarActivas(),
            ]);
            return;
        }

        // Mantener imagen actual
        $imagen_path = $producto['imagen_path'] ?? null;

        // Si el usuario marcó eliminar imagen
        if (!empty($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1') {
            if (!empty($imagen_path)) {
                $anterior = $this->UPLOAD_BASE_DIR . '/productos/' . basename($imagen_path);
                if (file_exists($anterior)) {
                    @unlink($anterior);
                }
            }
            $imagen_path = null;
        } else if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            // Si suben nueva imagen, reemplazar y borrar anterior
            $nuevo = $this->procesarImagenUnica($_FILES['imagen']);
            if ($nuevo) {
                if (!empty($imagen_path)) {
                    $anterior = $this->UPLOAD_BASE_DIR . '/productos/' . basename($imagen_path);
                    if (file_exists($anterior)) {
                        @unlink($anterior);
                    }
                }
                $imagen_path = $this->UPLOAD_PUBLIC_DIR . '/productos/' . $nuevo;
            }
        }

        $payload = [
            // coherencia básica
            "sku"              => $producto["sku"] ?? null,
            "codigo_barra"     => $producto["codigo_barra"] ?? null,
            "nombre"           => trim($data["nombre"]),
            "tipo_producto"    => $producto["tipo_producto"] ?? "UNIDAD",
            "categoria_id"     => (int)$data["categoria_id"],
            "marca_id"         => (int)$data["marca_id"],
            "unidad_medida_id" => 1,

            "precio_venta"     => (float)$data["precio"],
            "costo_actual"     => (float)$data["costo"],

            "stock"            => (int)$data["stock"],
            "stock_minimo"     => (int)$data["stock_minimo"],

            "descripcion"      => $producto["descripcion"] ?? "",
            "estado"           => $producto["estado"] ?? "ACTIVO",

            // ✅ Número de serie
            "numero_serie"     => trim($data["numero_serie"] ?? ''),

            // ✅ ÚNICO CAMPO REAL EN DB
            "imagen_path" => $imagen_path,
        ];

        try {
            $this->model->actualizar((int)$id, $payload);

            redirect("/admin/productos?ok=actualizado");
        } catch (Exception $e) {
            error_log("Error actualizando producto: " . $e->getMessage());
            redirect("/admin/productos?error=No se pudo actualizar");
        }
    }

    // =========================
    // HELPERS
    // =========================
    private function procesarImagenUnica(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $dir = rtrim($this->UPLOAD_BASE_DIR, '/') . '/productos/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Límite 5MB
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $name = uniqid('prod_', true) . '_' . time() . '.' . $ext;
        $dest = $dir . $name;

        return move_uploaded_file($file['tmp_name'], $dest) ? $name : null;
    }

    private function sanitizar(array $in): array
    {
        return [
            "nombre"        => trim($in["nombre"] ?? ""),
            "categoria_id"  => (int)($in["categoria_id"] ?? 0),
            "marca_id"      => (int)($in["marca_id"] ?? 0),
            "costo"         => (float)($in["costo"] ?? 0),
            "precio"        => (float)($in["precio"] ?? 0),
            "stock"         => (int)($in["stock"] ?? 0),
            "stock_minimo"  => (int)($in["stock_minimo"] ?? 0),
            "codigo_barra"  => trim($in["codigo_barra"] ?? ""),
            "tipo_producto" => strtoupper(trim($in["tipo_producto"] ?? "UNIDAD")),
        ];
    }

    private function validar(array $d): array
    {
        $e = [];
        if (empty($d["nombre"])) $e[] = "Nombre es obligatorio.";
        if ($d["precio"] < $d["costo"]) $e[] = "Precio no puede ser menor al costo.";
        if ($d["categoria_id"] <= 0) $e[] = "Categoría es obligatoria.";
        if ($d["marca_id"] <= 0) $e[] = "Marca es obligatoria.";
        if ($d["stock"] < 0) $e[] = "Stock no puede ser negativo.";
        if ($d["stock_minimo"] < 0) $e[] = "Stock mínimo no puede ser negativo.";
        return $e;
    }

    private function generarSKU(): string
    {
        $intentos = 0;

        do {
            $sku = "Comer_sosa_" . mt_rand(10000, 99999);
            $intentos++;
            if ($intentos > 20) {
                return "Comer_sosa_" . mt_rand(10000, 99999) . "_" . time();
            }
        } while ($this->model->skuExiste($sku));

        return $sku;
    }
}
