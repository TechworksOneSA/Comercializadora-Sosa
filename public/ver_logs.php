<?php
// TEMPORAL - ELIMINAR DESPUÉS DE DEBUGGEAR
// Muestra los últimos 100 líneas del log de errores de PHP

$logFile = ini_get('error_log');
if (!$logFile || !file_exists($logFile)) {
    // Intentar ubicaciones comunes
    $posibleLogs = [
        '/var/log/apache2/error.log',
        '/var/log/nginx/error.log',
        '/var/log/php-fpm/error.log',
        '/var/log/php/error.log',
    ];
    
    foreach ($posibleLogs as $log) {
        if (file_exists($log)) {
            $logFile = $log;
            break;
        }
    }
}

if (!$logFile || !file_exists($logFile)) {
    die("No se pudo encontrar el archivo de log. error_log configurado: " . ini_get('error_log'));
}

$lines = file($logFile);
$lastLines = array_slice($lines, -100);

header('Content-Type: text/plain; charset=utf-8');
echo "=== ÚLTIMOS 100 LOGS DE: $logFile ===\n\n";
echo implode('', $lastLines);
