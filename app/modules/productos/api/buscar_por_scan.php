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

$raw = file_get_contents('php://input') ?: '';
$body = json_decode($raw, true);
$q = trim((string)($body['q'] ?? ''));

if ($q === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parámetro q requerido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = Database::connect();
    $dbName = (string)$db->query('SELECT DATABASE()')->fetchColumn();
    if ($dbName === '') throw new Exception('No se pudo detectar la BD activa.');

    $hasColumn = function(string $table, string $column) use ($db, $dbName): bool {
        $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :t AND COLUMN_NAME = :c";
        $st = $db->prepare($sql);
        $st->execute([':db' => $dbName, ':t' => $table, ':c' => $column]);
        return (int)$st->fetchColumn() > 0;
    };

    $table = 'productos';

    $colSku          = $hasColumn($table, 'sku');
    $colCodigoBarras = $hasColumn($table, 'codigo_barras'); // si existe
    $colPrecioVenta  = $hasColumn($table, 'precio_venta');
    $colStock        = $hasColumn($table, 'stock');
    $colReqSerie     = $hasColumn($table, 'requiere_serie');

    $select = [
        "p.id",
        "p.nombre",
        ($colSku ? "p.sku" : "'' AS sku"),
        ($colCodigoBarras ? "p.codigo_barras" : "'' AS codigo_barras"),
        ($colPrecioVenta ? "p.precio_venta" : "0 AS precio_venta"),
        ($colStock ? "p.stock" : "0 AS stock"),
        ($colReqSerie ? "p.requiere_serie" : "0 AS requiere_serie"),
    ];

    $where = [];
    $params = [];

    if (ctype_digit($q)) {
        $where[] = "p.id = :id";
        $params[':id'] = (int)$q;
    }
    if ($colSku) {
        $where[] = "p.sku = :qsku";
        $params[':qsku'] = $q;
    }
    if ($colCodigoBarras) {
        $where[] = "p.codigo_barras = :qcb";
        $params[':qcb'] = $q;
    }

    if (empty($where)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No hay columnas sku/codigo_barras para buscar.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sql = "SELECT " . implode(", ", $select) . "
            FROM {$table} p
            WHERE (" . implode(" OR ", $where) . ")
            LIMIT 1";

    $st = $db->prepare($sql);
    $st->execute($params);
    $producto = $st->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $producto['id'] = (int)$producto['id'];
    $producto['precio_venta'] = (float)($producto['precio_venta'] ?? 0);
    $producto['stock'] = (int)($producto['stock'] ?? 0);
    $producto['requiere_serie'] = (int)($producto['requiere_serie'] ?? 0);

    echo json_encode(['success' => true, 'producto' => $producto], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log("buscar_por_scan ERROR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno'], JSON_UNESCAPED_UNICODE);
}
