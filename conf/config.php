<?php
// Configuración de rutas del proyecto
$_SERVER['DOCUMENT_ROOT'] = '/home/appscide/public_html/disenoCurricular';

// Configurar BASE_URL para desarrollo local y producción
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = str_replace('/app/forms/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . '://' . $host . $path . '/');
} else {
    define('BASE_URL', '/disenoCurricular/');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
