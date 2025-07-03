<?php
// Para MAMP, el document root suele ser diferente
// No forzar DOCUMENT_ROOT, usar el que proporciona el servidor
// $_SERVER['DOCUMENT_ROOT'] = '/Users/melquiromero/Documents/phpStorm/disenoCurricular';

// Detectar automáticamente BASE_URL basado en el entorno
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Para MAMP/XAMPP con puerto específico
if (strpos($host, ':') !== false) {
    // Ya incluye el puerto (ej: localhost:8889)
    define('BASE_URL', $protocol . $host . '/');
} else {
    // Sin puerto específico
    define('BASE_URL', $protocol . $host . '/');
}

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'disenos_curriculares');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Mostrar errores generados por alguna acción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
