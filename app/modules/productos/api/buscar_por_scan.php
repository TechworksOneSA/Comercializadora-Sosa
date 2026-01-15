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
 * subir 3 niveles => /srv/apps/comercializadora/app
 */
$APP_BASE = realpath(__DIR__ . '/../../..'); // .../app
if (!$APP_BASE) {
  $APP_BASE = '/srv/apps/comercializadora/app';
}

/**
 * IMPORTANTE:
 * Este endpoint normalmente se ejecuta dentro del Router (public/index.php),
 * donde ya se cargan env.php, database.php y Auth.php.
 * Por eso: solo cargamos si NO existe ya (evita "Constant already defined").
 */
$ENV_ROOT = dirname($APP_BASE) . '/config/env.php';
$ENV_APP  = $APP_BASE . '/config/env.php';

if (!defined('DB_HOST')) {
  if (file_exists($ENV_ROOT)) {
    require_once $ENV_ROOT;
  } elseif (file_exists($ENV_APP)) {
    require_once $ENV_APP;
  } else {
    respond(500, ['success' => false, 'message' => 'env.php no encontrado']);
  }
}

$DB_FILE   = $APP_BASE . '/config/database.php';
$AUTH_FILE = $APP_BASE . '/core/Auth.php';

if (!class_exists('Database')) {
  if (!file_exists($DB_FILE)) {
    respond(500, ['success' => false, 'message' => 'database.php no encontrado en app/config']);
  }
  require_once $DB_FILE;
}

if (!class_exists('Auth')) {
  if (!file_exists($AUTH_FILE)) {
    respond(500, ['success' => false, 'message' => 'Auth.php no encontrado en app/core']);
  }
  require_once $AUTH_FILE;
}

/** =========================
 *  Seguridad / Sesión
 *  ========================= */
$autorizado = false;

// Si existe Auth::check(), úselo (es el estándar del proyecto)
if (class_exists('Auth') && method_exists('Auth', 'check')) {
  try {
    $autorizado = (bool) Auth::check();
  } catch (Throwable $e) {
    // fallback a sesión si algo raro pasa
    $autorizado = !empty($_SESSION['user']) || !empty($_SESSION['usuario']) || !empty($_SESSION['auth']);
  }
} else {
  // fallback
  $autorizado = !empty($_SESSION['user']) || !empty($_SESSION['usuario']) || !empty($_SESSION['auth']);
}

if (!$autorizado) {
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
      requiere_serie,
      codigo_barra,
      numero_serie
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
  error_log("buscar_por_scan ERROR: " . $e->getMessage() . " @ " . $e->getFile() . ":" . $e->getLine());
  respond(500, [
    'success' => false,
    'message' => 'Error interno'
  ]);
}
