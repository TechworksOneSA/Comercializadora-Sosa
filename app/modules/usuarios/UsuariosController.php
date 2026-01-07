<?php

require_once __DIR__ . "/UsuariosModel.php";

class UsuariosController extends Controller {
    private UsuariosModel $model;

    public function __construct() {
        $this->model = new UsuariosModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index() {
        RoleMiddleware::requireAdmin();

        $usuarios = $this->model->listar();
        $stats = $this->model->obtenerEstadisticas();

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
    public function crear() {
        RoleMiddleware::requireAdmin();

        $errors = $_SESSION['usuarios_errors'] ?? [];
        $old    = $_SESSION['usuarios_old'] ?? [];

        unset($_SESSION['usuarios_errors'], $_SESSION['usuarios_old']);

        $this->viewWithLayout("usuarios/views/crear", [
            "title"   => "Crear Usuario",
            "user"    => $_SESSION["user"],
            "errors"  => $errors,
            "old"     => $old,
        ]);
    }

    public function guardar() {
        RoleMiddleware::requireAdmin();

        $data = $_POST;

        // Validaciones
        $errors = $this->validarUsuario($data);

        if (!empty($errors)) {
            $_SESSION['usuarios_errors'] = $errors;
            $_SESSION['usuarios_old'] = $data;
            redirect('/admin/usuarios/crear');
            return;
        }

        // Preparar payload
        $payload = [
            "nombre"   => trim($data["nombre"] ?? ""),
            "email"    => trim($data["email"] ?? ""),
            "password" => $data["password"],
            "rol"      => $data["rol"] ?? "VENDEDOR",
            "activo"   => isset($data["activo"]) ? 1 : 0,
        ];

        // Crear usuario
        if ($this->model->crear($payload)) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
            redirect('/admin/usuarios');
        } else {
            $_SESSION['usuarios_errors'] = ['general' => 'Error al crear el usuario'];
            $_SESSION['usuarios_old'] = $data;
            redirect('/admin/usuarios/crear');
        }
    }

    // =========================
    // EDITAR
    // =========================
    public function editar($id) {
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

    public function actualizar($id) {
        RoleMiddleware::requireAdmin();

        $data = $_POST;

        // Validaciones
        $errors = $this->validarUsuario($data, $id);

        if (!empty($errors)) {
            $_SESSION['usuarios_errors'] = $errors;
            $_SESSION['usuarios_old'] = $data;
            redirect("/admin/usuarios/editar/$id");
            return;
        }

        // Preparar payload
        $payload = [
            "nombre"   => trim($data["nombre"] ?? ""),
            "email"    => trim($data["email"] ?? ""),
            "rol"      => $data["rol"] ?? "VENDEDOR",
            "activo"   => isset($data["activo"]) ? 1 : 0,
        ];

        // Solo incluir password si se proporciona
        if (!empty($data["password"])) {
            $payload["password"] = $data["password"];
        }

        // Actualizar usuario
        if ($this->model->actualizar($id, $payload)) {
            $_SESSION['success'] = 'Usuario actualizado exitosamente';
            redirect('/admin/usuarios');
        } else {
            $_SESSION['usuarios_errors'] = ['general' => 'Error al actualizar el usuario'];
            $_SESSION['usuarios_old'] = $data;
            redirect("/admin/usuarios/editar/$id");
        }
    }

    // =========================
    // CAMBIAR ESTADO
    // =========================
    public function cambiarEstado($id) {
        RoleMiddleware::requireAdmin();

        // No permitir desactivar al usuario actual
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'No puedes desactivar tu propia cuenta';
            redirect('/admin/usuarios');
            return;
        }

        $usuario = $this->model->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            redirect('/admin/usuarios');
            return;
        }

        $nuevoEstado = $usuario['activo'] == 1 ? 0 : 1;

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $mensaje = $nuevoEstado == 1 ? 'Usuario activado' : 'Usuario desactivado';
            $_SESSION['success'] = $mensaje;
        } else {
            $_SESSION['error'] = 'Error al cambiar el estado del usuario';
        }

        redirect('/admin/usuarios');
    }

    // =========================
    // VALIDACIONES
    // =========================
    private function validarUsuario($data, $id = null) {
        $errors = [];

        // Validar nombre
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        } elseif (strlen($data['nombre']) < 3) {
            $errors['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        } elseif ($this->model->emailExiste($data['email'], $id)) {
            $errors['email'] = 'Este email ya está registrado';
        }

        // Validar password (solo si es nuevo usuario o si se está cambiando)
        if (!$id) {
            // Usuario nuevo - password obligatorio
            if (empty($data['password'])) {
                $errors['password'] = 'La contraseña es requerida';
            } elseif (strlen($data['password']) < 6) {
                $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
            }

            if (!empty($data['password']) && $data['password'] !== ($data['password_confirmacion'] ?? '')) {
                $errors['password_confirmacion'] = 'Las contraseñas no coinciden';
            }
        } else {
            // Usuario existente - password opcional
            if (!empty($data['password'])) {
                if (strlen($data['password']) < 6) {
                    $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
                }

                if ($data['password'] !== ($data['password_confirmacion'] ?? '')) {
                    $errors['password_confirmacion'] = 'Las contraseñas no coinciden';
                }
            }
        }

        // Validar rol
        if (empty($data['rol']) || !in_array($data['rol'], ['ADMIN', 'VENDEDOR'])) {
            $errors['rol'] = 'El rol es requerido y debe ser ADMIN o VENDEDOR';
        }

        return $errors;
    }
}
