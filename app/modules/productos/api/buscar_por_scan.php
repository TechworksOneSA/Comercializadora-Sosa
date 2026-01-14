<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Requisitos del proyecto:
 * - env.php y database.php ya existen en /app/config/
 * - Database::connect() retorna PDO
 * - Auth.php existe en /app/core/ (si quiere protegerlo)
 */

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';

try {
    // Seguridad básica (opcional pero recomendado)
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
    $payload = json_decode($raw, true);

    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $q = trim((string)($payload['q'] ?? ''));

    if ($q === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parámetro q requerido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $db = Database::connect(); // PDO

    // Normalización: también intentamos variantes comunes
    $qLike = '%' . $q . '%';

    /**
     * Estrategia de búsqueda:
     * 1) SKU exacto
     * 2) Código de barras exacto (si existe columna barcode/codigo_barras)
     * 3) Número de serie exacto (si existe tabla/columna de series)
     *
     * Como no tengo su esquema exacto, dejo consultas "compatibles" usando COALESCE
     * y fallback seguro.
     */

    // 1) Buscar por SKU exacto o por campos alternos si existen
    // Ajuste: cambie nombres de columnas si su tabla difiere.
    $sqlProducto = "
        SELECT
            p.id,
            p.nombre,
            p.sku,
            p.precio_venta,
            p.stock,
            COALESCE(p.requiere_serie, 0) AS requiere_serie
        FROM productos p
        WHERE
            (p.sku = :q)
            OR (COALESCE(p.codigo_barras, '') = :q)
            OR (COALESCE(p.barcode, '') = :q)
        LIMIT 1
    ";

    $stmt = $db->prepare($sqlProducto);
    $stmt->execute([':q' => $q]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2) Si no encontró en producto, intentar por serie (si hay tabla de series)
    // Si su tabla se llama distinto, ajuste aquí.
    if (!$producto) {
        // Probables nombres: productos_series / producto_series / series_productos
        // Probable columna: numero_serie / serie / serial
        $sqlSerie = "
            SELECT
                p.id,
                p.nombre,
                p.sku,
                p.precio_venta,
                p.stock,
                1 AS requiere_serie
            FROM productos p
            INNER JOIN productos_series s ON s.producto_id = p.id
            WHERE s.numero_serie = :q
            LIMIT 1
        ";

        try {
            $stmt2 = $db->prepare($sqlSerie);
            $stmt2->execute([':q' => $q]);
            $producto = $stmt2->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            // Si esa tabla no existe, ignoramos este intento sin tumbar la API
            $producto = null;
        }
    }

    if (!$producto) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => true, 'producto' => $producto], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno',
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
