<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// CORS / preflight
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
  http_response_code(204);
  exit;
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function respond(int $code, array $payload): void {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

/**
 * Estructura real detectada por usted:
 * /srv/apps/comercializadora/config/env.php
 * /srv/apps/comercializadora/app/config/database.php
 * /srv/apps/comercializadora/app/core/Auth.php
 *
 * Este archivo está en:
 * /srv/apps/comercializadora/app/modules/productos/api/buscar_por_scan.php
 *
 * Subiendo 4 niveles desde /api llegamos a /srv/apps/comercializadora
 */
$ROOT = realpath(__DIR__ . '/../../../..'); // => /srv/apps/comercializadora
if (!$ROOT) {
  respond(500, ['success' => false, 'message' => 'ROOT no resolvible']);
}

$envPath  = $ROOT . '/config/env.php';
$dbPath   = $ROOT . '/app/config/database.php';
$authPath = $ROOT . '/app/core/Auth.php';

if (!file_exists($envPath) || !file_exists($dbPath) || !file_exists($authPath)) {
  respond(500, [
    'success' => false,
    'message' => 'Dependencias no encontradas',
    'debug' => [
      'ROOT' => $ROOT,
      'env'  => file_exists($envPath) ? $envPath : null,
      'db'   => file_exists($dbPath) ? $dbPath : null,
      'auth' => file_exists($authPath) ? $authPath : null,
    ]
  ]);
}

require_once $envPath;
require_once $dbPath;
require_once $authPath;

// Seguridad
if (empty($_SESSION['user'])) {
  respond(401, ['success' => false, 'message' => 'No autorizado']);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  respond(405, ['success' => false, 'message' => 'Método no permitido']);
}

$raw  = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);

if (!is_array($data)) {
  respond(400, ['success' => false, 'message' => 'JSON inválido']);
}

$q = trim((string)($data['q'] ?? ''));
if ($q === '') {
  respond(422, ['success' => false, 'message' => 'Parámetro q requerido']);
}

try {
  $pdo = Database::connect();

  $sql = "
    SELECT
      id,
      sku,
      nombre,
      precio_venta,
      stock,
      codigo_barra,
      numero_serie,
      requiere_serie
    FROM productos
    WHERE sku = :q
       OR codigo_barra = :q
       OR numero_serie = :q
    LIMIT 1
  ";

  $st = $pdo->prepare($sql);
  $st->execute([':q' => $q]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    respond(404, ['success' => false, 'message' => 'No encontrado']);
  }

  respond(200, [
    'success' => true,
    'producto' => $row,
    'match' => 'sku/codigo_barra/numero_serie'
  ]);

} catch (Throwable $e) {
  error_log("buscar_por_scan ERROR: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
  respond(500, ['success' => false, 'message' => 'Error interno']);
}
