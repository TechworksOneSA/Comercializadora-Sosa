<?php
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'comercializadora_sosa');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');

if (!defined('APP_URL')) define('APP_URL', getenv('APP_URL') ?: '');

if (!defined('APP_TIMEZONE')) define('APP_TIMEZONE', 'America/Guatemala');
date_default_timezone_set(APP_TIMEZONE);
