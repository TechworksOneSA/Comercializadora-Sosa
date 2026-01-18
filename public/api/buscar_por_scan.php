<?php
// API pública para buscar producto por scan
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/config/env.php';
require_once __DIR__ . '/../../app/config/database.php';

// Seguridad: requiere sesión
if (empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Leer JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '[]', true);

$q = trim((string)($data['q'] ?? '')); // ✅ Forzar como STRING
if ($q === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Parámetro q requerido']);
    exit;
}

// Log para debug de números largos
error_log("🔍 [buscar_por_scan] Búsqueda: '{$q}' (longitud: " . strlen($q) . ")");

try {
    $pdo = Database::connect();

    // Detectar columnas
    $hasCodigoBarras = false;
    $hasRequiereSerie = false;
    $cols = $pdo->query("SHOW COLUMNS FROM productos")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        if (($c['Field'] ?? '') === 'codigo_barras') $hasCodigoBarras = true;
        if (($c['Field'] ?? '') === 'requiere_serie') $hasRequiereSerie = true;
    }

    // Detectar tabla de series
    $hasSeriesTable = false;
    $seriesTableName = null;
    $seriesCol = null;
    $seriesProdCol = null;
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
    $tableNames = array_map(fn($r) => (string)$r[0], $tables);
    foreach (['producto_series', 'productos_series', 'series_productos', 'series'] as $t) {
        if (in_array($t, $tableNames, true)) {
            $hasSeriesTable = true;
            $seriesTableName = $t;
            break;
        }
    }
    if ($hasSeriesTable && $seriesTableName) {
        $scols = $pdo->query("SHOW COLUMNS FROM `$seriesTableName`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($scols as $c) {
            $f = (string)($c['Field'] ?? '');
            if (in_array($f, ['numero_serie', 'serie', 'serial'], true)) $seriesCol = $f;
            if (in_array($f, ['producto_id', 'id_producto'], true)) $seriesProdCol = $f;
        }
        if (!$seriesCol || !$seriesProdCol) {
            $hasSeriesTable = false;
        }
    }

    // Buscar por serie
    if ($hasSeriesTable && $seriesTableName) {
        $sql = "SELECT p.id, p.nombre, p.sku, p.precio_venta, p.stock" . ($hasRequiereSerie ? ", p.requiere_serie" : ", 0 AS requiere_serie") . " FROM `$seriesTableName` s INNER JOIN productos p ON p.id = s.`$seriesProdCol` WHERE s.`$seriesCol` = :q LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q', $q, PDO::PARAM_STR); // ✅ Bind como STRING
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            error_log("✅ [buscar_por_scan] Producto encontrado por SERIE: '{$q}' -> Producto #{$row['id']}");
            echo json_encode(['success' => true, 'producto' => $row, 'match' => 'serie'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Buscar por SKU/codigo_barras
    $where = "p.sku = :q";
    if ($hasCodigoBarras) $where .= " OR p.codigo_barras = :q";
    $sql2 = "SELECT p.id, p.nombre, p.sku, p.precio_venta, p.stock" . ($hasRequiereSerie ? ", p.requiere_serie" : ", 0 AS requiere_serie") . " FROM productos p WHERE ($where) LIMIT 1";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(':q', $q, PDO::PARAM_STR); // ✅ Bind como STRING
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row2) {
        error_log("✅ [buscar_por_scan] Producto encontrado por SKU/CODIGO: '{$q}' -> Producto #{$row2['id']}");
        echo json_encode(['success' => true, 'producto' => $row2, 'match' => 'sku/codigo_barras'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No encontrado']);
} catch (Throwable $e) {
    http_response_code(500);
    error_log("buscar_por_scan ERROR: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno']);
}
