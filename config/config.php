<?php

define('ENTORNO_DESARROLLO', true); // Cambiar a false en producción

// Configuración de errores
if (ENTORNO_DESARROLLO) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'celeris');

define('BASE_URL', '/Celeris/public');

date_default_timezone_set('America/Mexico_City');