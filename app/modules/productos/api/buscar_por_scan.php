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

/**
 * ✅ RUTAS CORRECTAS SEGÚN SU SERVIDOR:
 * /srv/apps/comercializadora/app/config/database.php
 * /srv/apps/comercializadora/app/core/Auth.php
 * env.php puede estar en /config o /app/config; aquí cargamos primero el root y si no, el app.
 */
$envRoot = __DIR__ . '/../../../config/env.php';
$envApp  = __DIR__ . '/../../../app/config/env.php';

if (file_exists($envRoot)) {
  require_once $envRoot;
} elseif (file_exists($envApp)) {
  require_once $envApp;
}

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/core/Auth.php';

function respond(int $code, array $payload): void {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// Seguridad
if (empty($_SESSION['user'])) {
  respond(401, ['success' => false, 'message' => 'No autorizado']);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  respond(405, ['success' => false, 'message' => 'Método no permitido']);
}

$raw = file_get_contents('php://input') ?: '';
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
  error_log("buscar_por_scan ERROR: " . $e->getMessage());
  respond(500, ['success' => false, 'message' => 'Error interno']);
}
