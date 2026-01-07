<?php
/**
 * Health Check - Verificación del Sistema
 * URL: http://localhost/comercializadora-sosa/health.php
 * 
 * Este script verifica el estado general del sistema:
 * - Versión de PHP
 * - Extensiones requeridas
 * - Archivos críticos del proyecto
 */

header('Content-Type: application/json; charset=utf-8');

$health = [
    'status' => 'OK',
    'timestamp' => date('Y-m-d H:i:s'),
    'project' => 'Comercializadora Sosa - POS',
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'extensions' => [
        'pdo_mysql' => extension_loaded('pdo_mysql') ? '✅' : '❌',
        'mysqli' => extension_loaded('mysqli') ? '✅' : '❌',
        'mbstring' => extension_loaded('mbstring') ? '✅' : '❌',
        'intl' => extension_loaded('intl') ? '✅' : '❌',
        'gd' => extension_loaded('gd') ? '✅' : '❌',
        'openssl' => extension_loaded('openssl') ? '✅' : '❌',
        'curl' => extension_loaded('curl') ? '✅' : '❌',
        'json' => extension_loaded('json') ? '✅' : '❌',
        'fileinfo' => extension_loaded('fileinfo') ? '✅' : '❌',
    ],
    'paths' => [
        'project_root' => __DIR__,
        'app_config' => file_exists(__DIR__ . '/app/config/env.php') ? '✅ Exists' : '❌ Missing',
        'app_database' => file_exists(__DIR__ . '/app/config/database.php') ? '✅ Exists' : '❌ Missing',
        'public_index' => file_exists(__DIR__ . '/public/index.php') ? '✅ Exists' : '❌ Missing',
        'core_router' => file_exists(__DIR__ . '/app/core/Router.php') ? '✅ Exists' : '❌ Missing',
    ],
    'php_settings' => [
        'max_execution_time' => ini_get('max_execution_time') . 's',
        'memory_limit' => ini_get('memory_limit'),
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'timezone' => ini_get('date.timezone') ?: 'Not set',
    ]
];

// Verificar si todas las extensiones críticas están activas
$critical_extensions = ['pdo_mysql', 'mbstring', 'json'];
$all_ok = true;
$missing_extensions = [];

foreach ($critical_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $health['status'] = 'WARNING';
        $all_ok = false;
        $missing_extensions[] = $ext;
    }
}

if (!$all_ok) {
    $health['message'] = 'Extensiones críticas faltantes: ' . implode(', ', $missing_extensions);
    $health['action'] = 'Habilitar extensiones en php.ini y reiniciar Apache';
}

// Verificar archivos críticos
$critical_files = ['app/config/env.php', 'app/config/database.php', 'public/index.php'];
$missing_files = [];

foreach ($critical_files as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $health['status'] = 'ERROR';
        $missing_files[] = $file;
    }
}

if (!empty($missing_files)) {
    $health['message'] = 'Archivos críticos faltantes: ' . implode(', ', $missing_files);
    $health['action'] = 'Verificar que el proyecto esté completo';
}

echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
