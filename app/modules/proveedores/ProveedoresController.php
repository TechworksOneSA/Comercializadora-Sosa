<?php

require_once __DIR__ . '/ProveedoresModel.php';

class ProveedoresController extends Controller
{
    private ProveedoresModel $model;

    public function __construct()
    {
        $this->model = new ProveedoresModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdmin();

        $proveedores = $this->model->listarTodos();

        $this->viewWithLayout("proveedores/views/index", [
            "title"       => "Proveedores",
            "user"        => $_SESSION["user"],
            "proveedores" => $proveedores,
        ]);
    }

    public function crear()
    {
        RoleMiddleware::requireAdmin();

        $errors = $_SESSION['prov_errors'] ?? [];
        $old    = $_SESSION['prov_old'] ?? [];

        unset($_SESSION['prov_errors'], $_SESSION['prov_old']);

        $this->viewWithLayout("proveedores/views/crear", [
            "title"   => "Crear proveedor",
            "user"    => $_SESSION["user"],
            "errors"  => $errors,
            "old"     => $old,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdmin();

        $input = $_POST;

        $nit       = trim($input['nit'] ?? '');
        $nombre    = trim($input['nombre'] ?? '');
        $direccion = trim($input['direccion'] ?? '');
        $telefono  = trim($input['telefono'] ?? '');
        $correo    = trim($input['correo'] ?? '');
        $activo    = isset($input['activo']) ? 1 : 0;

        $errors = [];

        if ($nit === '')    $errors[] = "El NIT es obligatorio.";
        if ($nombre === '') $errors[] = "El nombre es obligatorio.";

        // Validar NIT duplicado
        if ($nit !== '' && $this->model->existeNit($nit)) {
            $errors[] = "Ya existe un proveedor con el NIT: {$nit}";
        }

        // Validar nombre duplicado
        if ($nombre !== '' && $this->model->existeNombre($nombre)) {
            $errors[] = "Ya existe un proveedor con el nombre: {$nombre}";
        }

        if ($errors) {
            $_SESSION['prov_errors'] = $errors;
            $_SESSION['prov_old']    = $input;
            header("Location: " . url('/admin/proveedores/crear'));
            exit;
        }

        $this->model->crear([
            'nit'       => $nit,
            'nombre'    => $nombre,
            'direccion' => $direccion,
            'telefono'  => $telefono,
            'correo'    => $correo,
            'activo'    => $activo,
        ]);

        header("Location: " . url('/admin/proveedores?ok=creado'));
        exit;
    }

        public function cambiarEstado($id)
    {
        RoleMiddleware::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $proveedor = $this->model->obtenerPorId($id);
        if (!$proveedor) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        // Toggle: si está activo (1) lo ponemos 0, si está 0 lo ponemos 1
        $nuevoEstado = !empty($proveedor['activo']) ? 0 : 1;

        $this->model->cambiarEstado($id, $nuevoEstado);

        header("Location: " . url('/admin/proveedores'));
        exit;
    }

    public function editar($id)
    {
        RoleMiddleware::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $proveedor = $this->model->obtenerPorId($id);
        if (!$proveedor) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $errors = $_SESSION['prov_errors'] ?? [];
        $old    = $_SESSION['prov_old'] ?? $proveedor;

        unset($_SESSION['prov_errors'], $_SESSION['prov_old']);

        $this->viewWithLayout("proveedores/views/editar", [
            "title"     => "Editar proveedor",
            "user"      => $_SESSION["user"],
            "proveedor" => $proveedor,
            "errors"    => $errors,
            "old"       => $old,
        ]);
    }

    public function actualizar($id)
    {
        RoleMiddleware::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $proveedor = $this->model->obtenerPorId($id);
        if (!$proveedor) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $input = $_POST;

        $nit       = trim($input['nit'] ?? '');
        $nombre    = trim($input['nombre'] ?? '');
        $direccion = trim($input['direccion'] ?? '');
        $telefono  = trim($input['telefono'] ?? '');
        $correo    = trim($input['correo'] ?? '');
        $activo    = isset($input['activo']) ? 1 : 0;

        $errors = [];

        if ($nit === '')    $errors[] = "El NIT es obligatorio.";
        if ($nombre === '') $errors[] = "El nombre es obligatorio.";

        // Validar NIT duplicado (excluyendo el actual)
        if ($nit !== '' && $this->model->existeNitExcluyendoId($nit, $id)) {
            $errors[] = "Ya existe otro proveedor con el NIT: {$nit}";
        }

        // Validar nombre duplicado (excluyendo el actual)
        if ($nombre !== '' && $this->model->existeNombreExcluyendoId($nombre, $id)) {
            $errors[] = "Ya existe otro proveedor con el nombre: {$nombre}";
        }

        if ($errors) {
            $_SESSION['prov_errors'] = $errors;
            $_SESSION['prov_old']    = $input;
            header("Location: " . url('/admin/proveedores/editar/' . $id));
            exit;
        }

        $this->model->actualizar($id, [
            'nit'       => $nit,
            'nombre'    => $nombre,
            'direccion' => $direccion,
            'telefono'  => $telefono,
            'correo'    => $correo,
            'activo'    => $activo,
        ]);

        header("Location: " . url('/admin/proveedores?ok=actualizado'));
        exit;
    }

    public function eliminar($id)
    {
        RoleMiddleware::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $proveedor = $this->model->obtenerPorId($id);
        if (!$proveedor) {
            header("Location: " . url('/admin/proveedores'));
            exit;
        }

        $this->model->eliminar($id);

        header("Location: " . url('/admin/proveedores?ok=eliminado'));
        exit;
    }

}
