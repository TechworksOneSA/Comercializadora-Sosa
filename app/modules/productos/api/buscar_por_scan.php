<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// ===== CORS (si el frontend y backend comparten dominio, igual no estorba) =====
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

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';

function respond(int $code, array $payload): void {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// ===== Seguridad =====
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

  /**
   * NOTA ARQUITECTURA:
   * - numero_serie está en productos_series, no en productos
   * - requiere_serie está en productos (ya lo corregimos al crear)
   */

  // Traer producto por:
  // 1) Serie (productos_series.numero_serie)
  // 2) Código de barras
  // 3) SKU
  // y devolver un "match" que indique qué coincidió
  $sql = "
    SELECT
      p.id,
      p.sku,
      p.nombre,
      p.precio_venta,
      p.stock,
      p.requiere_serie,
      p.codigo_barra,
      ps.numero_serie,
      CASE
        WHEN ps.numero_serie = :q THEN 'numero_serie'
        WHEN p.codigo_barra = :q THEN 'codigo_barra'
        WHEN p.sku = :q THEN 'sku'
        ELSE 'unknown'
      END AS match
    FROM productos p
    LEFT JOIN productos_series ps
      ON ps.producto_id = p.id
      AND ps.numero_serie = :q
    WHERE
      p.sku = :q
      OR p.codigo_barra = :q
      OR ps.numero_serie = :q
    ORDER BY
      (ps.numero_serie = :q) DESC,
      (p.codigo_barra = :q) DESC,
      (p.sku = :q) DESC
    LIMIT 1
  ";

  $st = $pdo->prepare($sql);
  $st->execute([':q' => $q]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    respond(404, ['success' => false, 'message' => 'No encontrado']);
  }

  // Limpieza: match lo devolvemos fuera del producto para el frontend
  $match = $row['match'] ?? 'unknown';
  unset($row['match']);

  respond(200, [
    'success' => true,
    'producto' => $row,
    'match' => $match
  ]);

} catch (Throwable $e) {
  error_log("buscar_por_scan ERROR: " . $e->getMessage());
  respond(500, ['success' => false, 'message' => 'Error interno']);
}
