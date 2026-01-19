<?php
// TEMPORAL - Ver configuración de logs de PHP

echo "<h2>Configuración de Error Log</h2>";
echo "<pre>";
echo "error_log = " . ini_get('error_log') . "\n";
echo "log_errors = " . ini_get('log_errors') . "\n";
echo "display_errors = " . ini_get('display_errors') . "\n";
echo "error_reporting = " . ini_get('error_reporting') . "\n";
echo "</pre>";

echo "<h2>Intentar escribir en el log</h2>";
error_log("TEST: Este es un mensaje de prueba");
echo "Mensaje escrito. Revisa el archivo: " . ini_get('error_log');
