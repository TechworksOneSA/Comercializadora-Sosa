<?php
// app/modules/productos/api/buscar_por_scan.php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../core/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$q = isset($input['q']) ? trim($input['q']) : '';

if ($q === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El valor de búsqueda es requerido']);
    exit;
}

try {
    $db = Database::connect();
    $sql = "SELECT id, sku, nombre, precio_venta, stock, requiere_serie, numero_serie, codigo_barra
            FROM productos
            WHERE activo=1 AND estado='ACTIVO'
              AND (numero_serie = :q OR codigo_barra = :q OR sku = :q)
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([':q' => $q]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($producto) {
        echo json_encode(['success' => true, 'producto' => $producto]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No encontrado']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno']);
}
