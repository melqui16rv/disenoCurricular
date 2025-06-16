<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php'; //este siempre va a ir de primero ya que con este es donde se inicializan las rutas para que funsionen correctamente las demas
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosGenerales.php'; //despus ya podemos llamar los demas archivos haciendolo mas facil gracias al config.php..
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/forms/puedes crear otro archivo y agregar mas metodos y llamarlos des esta manera.php';

?>
<!-- para lamar estilos css u otros archivos puedes llamarlos de esta manera: -->
<!-- <link rel="stylesheet" href="<?php //echo BASE_URL; ?>assets/css/presupuesto/index_presupuesto.css"> -->
<!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
 </head>
 <body>
    <h1>Prueba vista en cPanel</h1>
 </body>
 </html>