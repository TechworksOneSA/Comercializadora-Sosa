<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../clasificacion/ClasificacionModel.php';

$categoriaId = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

if ($categoriaId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'ID de categoría inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $model = new ClasificacionModel();
    $subcategorias = $model->listarSubcategoriasPorCategoria($categoriaId);

    echo json_encode([
        'success' => true,
        'categoria_id' => $categoriaId,
        'subcategorias' => $subcategorias
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
