<?php

require_once __DIR__ . "/ClientesModel.php";

class ClientesController extends Controller
{
    private ClientesModel $model;

    public function __construct()
    {
        $this->model = new ClientesModel();
    }

    // =========================
    // LISTADO
    // =========================
    public function index()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $clientes = $this->model->listar();
        $stats = $this->model->obtenerEstadisticas();

        $this->viewWithLayout("clientes/views/index", [
            "title"    => "Clientes",
            "user"     => $_SESSION["user"],
            "clientes" => $clientes,
            "stats"    => $stats,
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function crear()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $errors = $_SESSION['clientes_errors'] ?? [];
        $old    = $_SESSION['clientes_old'] ?? [];

        unset($_SESSION['clientes_errors'], $_SESSION['clientes_old']);

        $this->viewWithLayout("clientes/views/crear", [
            "title"   => "Crear Cliente",
            "user"    => $_SESSION["user"],
            "errors"  => $errors,
            "old"     => $old,
        ]);
    }

    public function guardar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;

        // Validaciones
        $errors = $this->validarCliente($data);

        if (!empty($errors)) {
            $_SESSION['clientes_errors'] = $errors;
            $_SESSION['clientes_old'] = $data;
            redirect('/admin/clientes/crear');
            return;
        }

        // Preparar payload
        $payload = [
            "nombre"       => trim($data["nombre"] ?? ""),
            "apellido"     => trim($data["apellido"] ?? ""),
            "telefono"     => trim($data["telefono"] ?? ""),
            "direccion"    => trim($data["direccion"] ?? ""),
            "preferencia_metodo_pago"  => trim($data["metodo_pago"] ?? ""),
            "nit"          => trim($data["nit"] ?? ""),
            "total_gastado" => 0.00, // Inicializado para futura lógica
        ];

        $this->model->crear($payload);

        // PRG Pattern: Redirect después de POST
        redirect('/admin/clientes?ok=creado');
    }

    // =========================
    // EDITAR
    // =========================
    public function editar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $cliente = $this->model->obtenerPorId((int)$id);
        if (!$cliente) {
            $_SESSION['clientes_error'] = 'Cliente no encontrado.';
            redirect('/admin/clientes');
            return;
        }

        $errors = $_SESSION['clientes_errors'] ?? [];
        $old    = $_SESSION['clientes_old'] ?? [];

        unset($_SESSION['clientes_errors'], $_SESSION['clientes_old']);

        $this->viewWithLayout("clientes/views/editar", [
            "title"   => "Editar Cliente",
            "user"    => $_SESSION["user"],
            "cliente" => $cliente,
            "errors"  => $errors,
            "old"     => $old,
        ]);
    }

    public function actualizar($id)
    {
        RoleMiddleware::requireAdminOrVendedor();

        $data = $_POST;
        $clienteId = (int)$id;

        // Verificar que el cliente existe
        $cliente = $this->model->obtenerPorId($clienteId);
        if (!$cliente) {
            $_SESSION['clientes_error'] = 'Cliente no encontrado.';
            redirect('/admin/clientes');
            return;
        }

        // Validaciones
        $errors = $this->validarCliente($data);

        if (!empty($errors)) {
            $_SESSION['clientes_errors'] = $errors;
            $_SESSION['clientes_old'] = $data;
            redirect('/admin/clientes/editar/' . $clienteId);
            return;
        }

        // Preparar payload
        $payload = [
            "nombre"       => trim($data["nombre"] ?? ""),
            "apellido"     => trim($data["apellido"] ?? ""),
            "telefono"     => trim($data["telefono"] ?? ""),
            "direccion"    => trim($data["direccion"] ?? ""),
            "preferencia_metodo_pago"  => trim($data["metodo_pago"] ?? ""),
            "nit"          => trim($data["nit"] ?? ""),
        ];

        $success = $this->model->actualizar($clienteId, $payload);

        if ($success) {
            $_SESSION['clientes_success'] = 'Cliente actualizado exitosamente.';
        } else {
            $_SESSION['clientes_error'] = 'Error al actualizar el cliente.';
        }

        redirect('/admin/clientes');
    }

    // =========================
    // BUSCAR (AJAX para ventas)
    // =========================
    public function buscar()
    {
        RoleMiddleware::requireAdminOrVendedor();

        $q = $_GET["q"] ?? "";
        $clientes = $this->model->buscar($q);

        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($clientes);
    }

    // =========================
    // VALIDACIONES
    // =========================
    private function validarCliente(array $data): array
    {
        $errors = [];

        if (empty(trim($data["nombre"] ?? ""))) {
            $errors[] = "Nombre es obligatorio.";
        }
        if (empty(trim($data["apellido"] ?? ""))) {
            $errors[] = "Apellido es obligatorio.";
        }
        if (empty(trim($data["telefono"] ?? ""))) {
            $errors[] = "Teléfono es obligatorio.";
        }
        if (empty(trim($data["metodo_pago"] ?? ""))) {
            $errors[] = "Método de pago favorito es obligatorio.";
        }

        return $errors;
    }
}
