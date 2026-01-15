<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

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
 * Base real del proyecto:
 * __DIR__ = /srv/apps/comercializadora/app/modules/productos/api
 * subir 4 niveles => /srv/apps/comercializadora/app
 */
$APP_BASE = realpath(__DIR__ . '/../../../..'); // .../app
if (!$APP_BASE) {
  $APP_BASE = '/srv/apps/comercializadora/app';
}

/**
 * env.php: en su server existe en dos lugares:
 * - /srv/apps/comercializadora/config/env.php
 * - /srv/apps/comercializadora/app/config/env.php
 */
$ENV_ROOT = dirname($APP_BASE) . '/config/env.php';
$ENV_APP  = $APP_BASE . '/config/env.php';

if (file_exists($ENV_ROOT)) {
  require_once $ENV_ROOT;
} elseif (file_exists($ENV_APP)) {
  require_once $ENV_APP;
} else {
  respond(500, ['success' => false, 'message' => 'env.php no encontrado']);
}

// Rutas correctas (según su find)
$DB_FILE   = $APP_BASE . '/config/database.php';
$AUTH_FILE = $APP_BASE . '/core/Auth.php';

if (!file_exists($DB_FILE)) {
  respond(500, ['success' => false, 'message' => 'database.php no encontrado en app/config']);
}
if (!file_exists($AUTH_FILE)) {
  respond(500, ['success' => false, 'message' => 'Auth.php no encontrado en app/core']);
}

require_once $DB_FILE;
require_once $AUTH_FILE;

// Seguridad (si su Auth maneja sesión, úselo; si no, dejamos fallback)
if (class_exists('Auth') && method_exists('Auth', 'check')) {
  if (!Auth::check()) {
    respond(401, ['success' => false, 'message' => 'No autorizado']);
  }
} else {
  if (empty($_SESSION['user'])) {
    respond(401, ['success' => false, 'message' => 'No autorizado']);
  }
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

  /**
   * IMPORTANTE:
   * PDO MySQL suele fallar con HY093 si usted reutiliza el mismo placeholder nombrado (:q) varias veces
   * cuando no hay emulación. Por eso usamos placeholders posicionales (?).
   */
  $sql = "
    SELECT
      id,
      sku,
      nombre,
      precio_venta,
      stock,
      requiere_serie,
      codigo_barra,
      numero_serie
    FROM productos
    WHERE sku = ?
       OR codigo_barra = ?
       OR numero_serie = ?
    LIMIT 1
  ";

  $st = $pdo->prepare($sql);
  $st->execute([$q, $q, $q]);

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
  // Log detallado al server (para auditoría técnica)
  error_log("buscar_por_scan ERROR: " . $e->getMessage() . " @ " . $e->getFile() . ":" . $e->getLine());
  respond(500, ['success' => false, 'message' => 'Error interno']);
}
