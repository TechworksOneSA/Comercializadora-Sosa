<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../clasificacion/ClasificacionModel.php';

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nombre = trim((string)($input['nombre'] ?? ''));
$tipo   = strtoupper(trim((string)($input['tipo'] ?? '')));
$descripcion = trim((string)($input['descripcion'] ?? ''));

if ($nombre === '' || $tipo === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Nombre y tipo son requeridos'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($tipo !== 'CATEGORIA') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Tipo inválido. Solo CATEGORIA.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $model = new ClasificacionModel();

    if ($model->categoriaExiste($nombre)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Ya existe una categoría con ese nombre'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = $model->crearCategoria($nombre, $descripcion);

    if (!$id) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No se pudo crear la categoría'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'type' => 'CATEGORIA',
        'item' => [
            'id' => (int)$id,
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ],
        'message' => 'Categoría creada exitosamente'
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
