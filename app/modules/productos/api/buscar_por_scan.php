<?php
declare(strict_types=1);

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

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';

function respond(int $code, array $payload): void {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// Seguridad: requiere sesión (ajuste si usted quiere permitirlo sin login)
if (empty($_SESSION['user'])) {
  respond(401, ['success' => false, 'message' => 'No autorizado']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  respond(405, ['success' => false, 'message' => 'Método no permitido']);
}

// Leer JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '[]', true);

$q = trim((string)($data['q'] ?? ''));
if ($q === '') {
  respond(422, ['success' => false, 'message' => 'Parámetro q requerido']);
}

try {
  $pdo = Database::connect();

  // Buscamos por: SKU exacto, o si existe "codigo_barras", o si existe "numero_serie" en tabla series.
  // NOTA: como no tengo su esquema exacto, hago detección segura de columnas/tablas.
  $hasCodigoBarras = false;
  $hasRequiereSerie = false;

  $cols = $pdo->query("SHOW COLUMNS FROM productos")->fetchAll(PDO::FETCH_ASSOC);
  foreach ($cols as $c) {
    if (($c['Field'] ?? '') === 'codigo_barras') $hasCodigoBarras = true;
    if (($c['Field'] ?? '') === 'requiere_serie') $hasRequiereSerie = true;
  }

  // Detectar tabla de series (si existe) y su columna típica
  $hasSeriesTable = false;
  $seriesTableName = null;
  $seriesCol = null;
  $seriesProdCol = null;

  $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  $tableNames = array_map(fn($r) => (string)$r[0], $tables);

  // Nombres comunes: producto_series / productos_series / series_productos / series
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
      // Si la tabla existe pero no matchea, mejor no usarla para evitar SQL roto
      $hasSeriesTable = false;
    }
  }

  // 1) Intentar por SKU / codigo_barras
  $where = "p.sku = :q";
  if ($hasCodigoBarras) $where .= " OR p.codigo_barras = :q";

  // 2) Intentar por serie: si existe tabla de series, buscamos por serie y traemos el producto
  if ($hasSeriesTable && $seriesTableName) {
    $sql = "
      SELECT p.id, p.nombre, p.sku, p.precio_venta, p.stock" . ($hasRequiereSerie ? ", p.requiere_serie" : ", 0 AS requiere_serie") . "
      FROM `$seriesTableName` s
      INNER JOIN productos p ON p.id = s.`$seriesProdCol`
      WHERE s.`$seriesCol` = :q
      LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => $q]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
      respond(200, ['success' => true, 'producto' => $row, 'match' => 'serie']);
    }
  }

  // Si no fue serie, probamos productos directo
  $sql2 = "
    SELECT p.id, p.nombre, p.sku, p.precio_venta, p.stock" . ($hasRequiereSerie ? ", p.requiere_serie" : ", 0 AS requiere_serie") . "
    FROM productos p
    WHERE ($where)
    LIMIT 1
  ";
  $stmt2 = $pdo->prepare($sql2);
  $stmt2->execute([':q' => $q]);
  $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

  if ($row2) {
    respond(200, ['success' => true, 'producto' => $row2, 'match' => 'sku/codigo_barras']);
  }

  respond(404, ['success' => false, 'message' => 'No encontrado']);
} catch (Throwable $e) {
  // Log server-side real
  error_log("buscar_por_scan ERROR: " . $e->getMessage());
  respond(500, ['success' => false, 'message' => 'Error interno']);
}
