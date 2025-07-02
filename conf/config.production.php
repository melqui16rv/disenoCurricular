<?php
$_SERVER['DOCUMENT_ROOT'] = '/Users/melquiromero/Documents/phpStorm/disenoCurricular';

if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = str_replace('/app/forms/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . '://' . $host . $path . '/');
} else {
    define('BASE_URL', '/disenoCurricular/');
}

// Solo mostrar errores en desarrollo local, no en AJAX
if (!isset($_GET['accion']) && !isset($_POST['accion'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Para AJAX mantener errores ocultos
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
}
?>
