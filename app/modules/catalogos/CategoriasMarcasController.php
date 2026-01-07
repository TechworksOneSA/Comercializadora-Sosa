<?php

require_once __DIR__ . '/../categorias/CategoriasModel.php';
require_once __DIR__ . '/../marcas/MarcasModel.php';

class CategoriasMarcasController extends Controller
{
    private CategoriasModel $categoriasModel;
    private MarcasModel $marcasModel;

    public function __construct()
    {
        $this->categoriasModel = new CategoriasModel();
        $this->marcasModel     = new MarcasModel();
    }

    public function index()
    {
        RoleMiddleware::requireAdmin();

        $categorias = $this->categoriasModel->listarTodos();
        $marcas     = $this->marcasModel->listarTodos();

        $this->view("modules/dashboard/views/_admin_layout", [
            "title"      => "Categorías & marcas",
            "user"       => $_SESSION["user"],
            "content"    => "catalogos/views/index",
            "categorias" => $categorias,
            "marcas"     => $marcas,
        ]);
    }

    public function guardarCategoria()
    {
        RoleMiddleware::requireAdmin();

        // Si es petición AJAX (JSON), manejar diferente
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            // Petición AJAX/JSON
            header('Content-Type: application/json; charset=utf-8');

            $raw = file_get_contents('php://input') ?: '';
            $input = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'JSON inválido'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $nombre = trim($input['nombre'] ?? '');

            if ($nombre === '') {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'El nombre es requerido'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            try {
                // Verificar duplicado
                $categorias = $this->categoriasModel->listarTodos();
                foreach ($categorias as $cat) {
                    if (strtolower($cat['nombre']) === strtolower($nombre)) {
                        http_response_code(409);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Ya existe una categoría con ese nombre'
                        ], JSON_UNESCAPED_UNICODE);
                        return;
                    }
                }

                $categoriaId = $this->categoriasModel->crear($nombre);

                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'categoria' => [
                        'id' => $categoriaId,
                        'nombre' => $nombre,
                        'margen_porcentaje' => null
                    ],
                    'message' => 'Categoría creada exitosamente'
                ], JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
            return;
        }

        // Petición normal (formulario HTML)
        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre !== '') {
            $this->categoriasModel->crear($nombre);
        }

        header("Location: /admin/catalogos");
        exit;
    }

    public function guardarMarca()
    {
        RoleMiddleware::requireAdmin();

        // Si es petición AJAX (JSON), manejar diferente
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            // Petición AJAX/JSON
            header('Content-Type: application/json; charset=utf-8');

            $raw = file_get_contents('php://input') ?: '';
            $input = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'JSON inválido'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $nombre = trim($input['nombre'] ?? '');

            if ($nombre === '') {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'El nombre es requerido'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            try {
                // Verificar duplicado
                $marcas = $this->marcasModel->listarTodos();
                foreach ($marcas as $marca) {
                    if (strtolower($marca['nombre']) === strtolower($nombre)) {
                        http_response_code(409);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Ya existe una marca con ese nombre'
                        ], JSON_UNESCAPED_UNICODE);
                        return;
                    }
                }

                $marcaId = $this->marcasModel->crear($nombre);

                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'marca' => [
                        'id' => $marcaId,
                        'nombre' => $nombre
                    ],
                    'message' => 'Marca creada exitosamente'
                ], JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
            return;
        }

        // Petición normal (formulario HTML)
        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre !== '') {
            $this->marcasModel->crear($nombre);
        }

        header("Location: /admin/catalogos");
        exit;
    }

    public function cambiarEstadoCategoria($id)
    {
        RoleMiddleware::requireAdmin();

        $id     = (int)$id;
        $activo = (int)($_POST['activo'] ?? 0);

        $this->categoriasModel->cambiarEstado($id, $activo);

        header("Location: /admin/catalogos");
        exit;
    }

    public function cambiarEstadoMarca($id)
    {
        RoleMiddleware::requireAdmin();

        $id     = (int)$id;
        $activo = (int)($_POST['activo'] ?? 0);

        $this->marcasModel->cambiarEstado($id, $activo);

        header("Location: /admin/catalogos");
        exit;
    }
}
