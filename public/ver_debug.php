<?php
// TEMPORAL - Ver logs de debug

$logFile = __DIR__ . '/../debug.log';

if (!file_exists($logFile)) {
    die("No existe el archivo debug.log todavía. Crea un producto primero.");
}

$content = file_get_contents($logFile);

header('Content-Type: text/plain; charset=utf-8');
echo "=== DEBUG LOG ===\n\n";
echo $content;

// Botón para limpiar
if (isset($_GET['clear'])) {
    file_put_contents($logFile, '');
    echo "\n\n=== LOG LIMPIADO ===";
}

echo "\n\n=== Para limpiar el log, visita: ?clear ===";
