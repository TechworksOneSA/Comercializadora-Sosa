<?php

// ========================
// DB (prioridad: Apache SetEnv / variables del sistema)
// ========================
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'comercializadora_sosa');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// ========================
// APP_URL (multi-entorno)
// En server: DocumentRoot ya es /public => base ""
// En XAMPP: probablemente /Comercializadora/ferreteria-pos/public
// ========================
define('APP_URL', getenv('APP_URL') ?: '');
