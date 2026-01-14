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

// Sesión + auth (antes de todo)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../core/Auth.php';

// Ajuste según su proyecto si ya maneja API_BASE
// const API_BASE = "/app/modules/productos/api";

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
    $db = Database::connect(); // Debe devolver PDO

    // =========================================================
    // Helpers: detectar si columnas existen (para no romper SQL)
    // =========================================================
    $dbNameStmt = $db->query('SELECT DATABASE()');
    $dbName = (string)($dbNameStmt ? $dbNameStmt->fetchColumn() : '');

    if ($dbName === '') {
        throw new Exception('No se pudo detectar la base de datos activa.');
    }

    $hasColumn = function (string $table, string $column) use ($db, $dbName): bool {
        $sql = "SELECT COUNT(*)
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = :db
                  AND TABLE_NAME = :t
                  AND COLUMN_NAME = :c";
        $st = $db->prepare($sql);
        $st->execute([':db' => $dbName, ':t' => $table, ':c' => $column]);
        return (int)$st->fetchColumn() > 0;
    };

    $hasTable = function (string $table) use ($db, $dbName): bool {
        $sql = "SELECT COUNT(*)
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = :db
                  AND TABLE_NAME = :t";
        $st = $db->prepare($sql);
        $st->execute([':db' => $dbName, ':t' => $table]);
        return (int)$st->fetchColumn() > 0;
    };

    // =========================================================
    // Definir columnas disponibles en "productos"
    // =========================================================
    $tableProductos = 'productos';

    $colSku          = $hasColumn($tableProductos, 'sku');
    $colCodigoBarras = $hasColumn($tableProductos, 'codigo_barras');   // si la tiene
    $colPrecioVenta  = $hasColumn($tableProductos, 'precio_venta');
    $colStock        = $hasColumn($tableProductos, 'stock');
    $colReqSerie     = $hasColumn($tableProductos, 'requiere_serie');  // si la tiene

    // Campos mínimos (id, nombre) asumimos que existen
    $select = [
        "p.id",
        "p.nombre",
        ($colSku ? "p.sku" : "'' AS sku"),
        ($colPrecioVenta ? "p.precio_venta" : "0 AS precio_venta"),
        ($colStock ? "p.stock" : "0 AS stock"),
        ($colReqSerie ? "p.requiere_serie" : "0 AS requiere_serie"),
    ];

    if ($colCodigoBarras) {
        $select[] = "p.codigo_barras";
    } else {
        $select[] = "'' AS codigo_barras";
    }

    // =========================================================
    // 1) Intento: buscar por SKU / código_barras / ID numérico
    // =========================================================
    $where = [];
    $params = [];

    // ID directo (si escanean un número exacto)
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

    // Si no hay ningún campo para buscar, devolvemos error claro
    if (empty($where)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'La tabla productos no tiene columnas para buscar (sku/codigo_barras).'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sql = "SELECT " . implode(", ", $select) . "
            FROM {$tableProductos} p
            WHERE (" . implode(" OR ", $where) . ")
            LIMIT 1";

    $st = $db->prepare($sql);
    $st->execute($params);
    $producto = $st->fetch(PDO::FETCH_ASSOC);

    // =========================================================
    // 2) Si no se encontró, intento “serie” (best effort)
    //    Esto depende de su modelo de datos. Soportamos dos escenarios:
    //    A) productos tiene columna numero_serie (poco común)
    //    B) existe tabla producto_series o series_productos (más común)
    // =========================================================
    if (!$producto) {
        // Escenario A: columna numero_serie en productos
        if ($hasColumn($tableProductos, 'numero_serie')) {
            $sqlSerie = "SELECT " . implode(", ", $select) . "
                         FROM {$tableProductos} p
                         WHERE p.numero_serie = :qs
                         LIMIT 1";
            $st2 = $db->prepare($sqlSerie);
            $st2->execute([':qs' => $q]);
            $producto = $st2->fetch(PDO::FETCH_ASSOC);
        }
    }

    if (!$producto) {
        // Escenario B: tabla puente típica
        // Ajuste automático entre nombres comunes
        $serieTables = [
            'producto_series',   // (producto_id, numero_serie, estado?)
            'productos_series',
            'series_productos',
            'series'
        ];

        foreach ($serieTables as $t) {
            if (!$hasTable($t)) continue;

            // Detectar columnas mínimas
            $hasProdId = $hasColumn($t, 'producto_id') || $hasColumn($t, 'productos_id');
            $hasSerie  = $hasColumn($t, 'numero_serie') || $hasColumn($t, 'serie');

            if (!$hasProdId || !$hasSerie) continue;

            $colProdIdName = $hasColumn($t, 'producto_id') ? 'producto_id' : 'productos_id';
            $colSerieName  = $hasColumn($t, 'numero_serie') ? 'numero_serie' : 'serie';

            $sqlJoin = "SELECT " . implode(", ", $select) . "
                        FROM {$t} s
                        INNER JOIN {$tableProductos} p ON p.id = s.{$colProdIdName}
                        WHERE s.{$colSerieName} = :qs
                        LIMIT 1";
            $st3 = $db->prepare($sqlJoin);
            $st3->execute([':qs' => $q]);
            $producto = $st3->fetch(PDO::FETCH_ASSOC);

            if ($producto) break;
        }
    }

    if (!$producto) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Normalizar tipos
    $producto['id'] = (int)$producto['id'];
    $producto['precio_venta'] = (float)($producto['precio_venta'] ?? 0);
    $producto['stock'] = (int)($producto['stock'] ?? 0);
    $producto['requiere_serie'] = (int)($producto['requiere_serie'] ?? 0);
    $producto['sku'] = (string)($producto['sku'] ?? '');
    $producto['codigo_barras'] = (string)($producto['codigo_barras'] ?? '');

    echo json_encode(['success' => true, 'producto' => $producto], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log("buscar_por_scan ERROR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno al buscar producto'
    ], JSON_UNESCAPED_UNICODE);
}
