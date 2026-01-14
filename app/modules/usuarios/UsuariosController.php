<?php

require_once __DIR__ . "/UsuariosModel.php";
require_once __DIR__ . "/../../core/ImageUploadHelper.php";

class UsuariosController extends Controller
{
    private UsuariosModel $model;

    public function __construct()
    {
        $this->model = new UsuariosModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdmin();

        $usuarios = $this->model->listar();
        $stats    = $this->model->obtenerEstadisticas();

        // Ocultar el usuario administrador principal (ID 1)
        $usuarios = array_filter($usuarios, function ($usuario) {
            return (int)$usuario['id'] !== 1;
        });

        // Restar 1 a cada estadística si es mayor a 0
        foreach (['total', 'activos', 'admins', 'vendedores'] as $key) {
            if (isset($stats[$key]) && $stats[$key] > 0) {
                $stats[$key]--;
            }
        }

        $this->viewWithLayout("usuarios/views/index", [
            "title"    => "Gestión de Usuarios",
            "user"     => $_SESSION["user"],
            "usuarios" => $usuarios,
            "stats"    => $stats,
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdmin();

        $errors = $_SESSION['usuarios_errors'] ?? [];
        $old    = $_SESSION['usuarios_old'] ?? [];

        unset($_SESSION['usuarios_errors'], $_SESSION['usuarios_old']);

        $this->viewWithLayout("usuarios/views/crear", [
            "title"  => "Crear Usuario",
            "user"   => $_SESSION["user"],
            "errors" => $errors,
            "old"    => $old,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdmin();

        $data = $_POST;

        // Validaciones
        $errors = $this->validarUsuario($data);
        if (!empty($errors)) {
            $_SESSION['usuarios_errors'] = $errors;
            $_SESSION['usuarios_old']    = $data;
            redirect('/admin/usuarios/crear');
            return;
        }

        // Payload base
        $payload = [
            "nombre"   => trim($data["nombre"]),
            "email"    => mb_strtolower(trim($data["email"])),
            "password" => $data["password"],
            "rol"      => $data["rol"] ?? "VENDEDOR",
            "activo"   => isset($data["activo"]) ? 1 : 0,
        ];

        // Foto
        if (!empty($data["foto_base64"])) {
            $fotoUrl = $this->procesarFoto($data["foto_base64"]);
            if ($fotoUrl) {
                $payload["foto"] = $fotoUrl;
            }
        }

        // Crear
        $nuevoId = $this->model->crear($payload);

        if ($nuevoId && is_numeric($nuevoId)) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
            redirect('/admin/usuarios/editar/' . $nuevoId);
        } else {
            $_SESSION['usuarios_errors'] = [
                'general' => 'Error al crear el usuario. Verifique que el correo no esté duplicado.'
            ];
            $_SESSION['usuarios_old'] = $data;
            redirect('/admin/usuarios/crear');
        }
    }

    // =========================
    // EDITAR
    // =========================
    public function editar($id)
    {
        RoleMiddleware::requireAdmin();

        $usuario = $this->model->obtenerPorId($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            redirect('/admin/usuarios');
            return;
        }

        $errors = $_SESSION['usuarios_errors'] ?? [];
        $old    = $_SESSION['usuarios_old'] ?? $usuario;

        unset($_SESSION['usuarios_errors'], $_SESSION['usuarios_old']);

        $this->viewWithLayout("usuarios/views/editar", [
            "title"   => "Editar Usuario",
            "user"    => $_SESSION["user"],
            "usuario" => $usuario,
            "errors"  => $errors,
            "old"     => $old,
        ]);
    }

    public function actualizar($id)
    {
        RoleMiddleware::requireAdmin();

        $data = $_POST;

        $errors = $this->validarUsuario($data, $id);
        if (!empty($errors)) {
            $_SESSION['usuarios_errors'] = $errors;
            $_SESSION['usuarios_old']    = $data;
            redirect("/admin/usuarios/editar/$id");
            return;
        }

        $payload = [
            "nombre" => trim($data["nombre"]),
            "email"  => mb_strtolower(trim($data["email"])),
            "rol"    => $data["rol"] ?? "VENDEDOR",
            "activo" => isset($data["activo"]) ? 1 : 0,
        ];

        if (!empty($data["password"])) {
            $payload["password"] = $data["password"];
        }

        // Foto
        if (!empty($data["foto_base64"])) {
            $fotoUrl = $this->procesarFoto($data["foto_base64"]);
            if ($fotoUrl) {
                $payload["foto"] = $fotoUrl;
            }
        } elseif (array_key_exists("foto_base64", $data) && empty($data["foto_base64"])) {
            // Limpiar foto
            $payload["foto"] = null;
        }

        if ($this->model->actualizar($id, $payload)) {
            $_SESSION['success'] = 'Usuario actualizado exitosamente';
            redirect('/admin/usuarios');
        } else {
            $_SESSION['usuarios_errors'] = ['general' => 'Error al actualizar el usuario'];
            $_SESSION['usuarios_old']    = $data;
            redirect("/admin/usuarios/editar/$id");
        }
    }

    // =========================
    // CAMBIAR ESTADO
    // =========================
    public function cambiarEstado($id)
    {
        RoleMiddleware::requireAdmin();

        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'No puede desactivar su propia cuenta';
            redirect('/admin/usuarios');
            return;
        }

        $usuario = $this->model->obtenerPorId($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            redirect('/admin/usuarios');
            return;
        }

        $nuevoEstado = $usuario['activo'] ? 0 : 1;

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $_SESSION['success'] = $nuevoEstado ? 'Usuario activado' : 'Usuario desactivado';
        } else {
            $_SESSION['error'] = 'Error al cambiar estado del usuario';
        }

        redirect('/admin/usuarios');
    }

    // =========================
    // VALIDACIONES
    // =========================
    private function validarUsuario($data, $id = null): array
    {
        $errors = [];

        if (empty($data['nombre']) || strlen($data['nombre']) < 3) {
            $errors['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } else {
            $email = mb_strtolower(trim($data['email']));
            if ($this->model->emailExiste($email, $id)) {
                $errors['email'] = 'El email ya está registrado';
            }
        }

        if (!$id) {
            if (empty($data['password']) || strlen($data['password']) < 6) {
                $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
            }
            if (($data['password'] ?? '') !== ($data['password_confirmacion'] ?? '')) {
                $errors['password_confirmacion'] = 'Las contraseñas no coinciden';
            }
        } elseif (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
            }
            if ($data['password'] !== ($data['password_confirmacion'] ?? '')) {
                $errors['password_confirmacion'] = 'Las contraseñas no coinciden';
            }
        }

        if (empty($data['rol']) || !in_array($data['rol'], ['ADMIN', 'VENDEDOR'])) {
            $errors['rol'] = 'Rol inválido';
        }

        return $errors;
    }

    // =========================
    // FOTO
    // =========================
    private function procesarFoto(string $base64Data): ?string
    {
        try {
            if (extension_loaded('gd')) {
                $base64Data = ImageUploadHelper::optimizeBase64Image($base64Data);
            }

            $result = ImageUploadHelper::processBase64Image($base64Data, 'usuarios');

            if ($result['success']) {
                return $result['url'];
            }

            error_log("Error imagen usuario: " . ($result['error'] ?? 'desconocido'));
            return null;
        } catch (Throwable $e) {
            error_log("UsuariosController::procesarFoto ERROR: " . $e->getMessage());
            return null;
        }
    }
}
