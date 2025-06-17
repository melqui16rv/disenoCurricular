<?php
/**
 * TEST DE AJAX - Verificación de rutas y funcionalidad
 * Probar directamente el endpoint AJAX para debugging
 */

// Simulamos una llamada POST como lo haría JavaScript
$postData = [
    'accion' => 'obtener_comparacion_raps',
    'codigoCompetencia' => '220201501',
    'disenoActual' => '124101-1'
];

echo "<h1>🧪 TEST DIRECTO DE AJAX</h1>";
echo "<p><strong>Simulando llamada POST a ajax.php</strong></p>";

echo "<h2>1. Datos enviados:</h2>";
echo "<pre>";
print_r($postData);
echo "</pre>";

echo "<h2>2. Configuración de $_POST:</h2>";
// Simular $_POST
foreach ($postData as $key => $value) {
    $_POST[$key] = $value;
}

echo "<h2>3. Configuración de $_SERVER:</h2>";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['DOCUMENT_ROOT'] = '/home/appscide/public_html';

echo "<pre>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "</pre>";

echo "<h2>4. Incluir y ejecutar ajax.php:</h2>";
echo "<div style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd;'>";

// Capturar salida de ajax.php
ob_start();

try {
    // Incluir el archivo ajax.php
    include __DIR__ . '/app/forms/control/ajax.php';
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al ejecutar ajax.php: " . $e->getMessage() . "</p>";
}

$output = ob_get_clean();

echo "<h3>Respuesta de ajax.php:</h3>";
echo "<pre style='background: white; padding: 10px; border: 1px solid #ccc;'>";
echo htmlspecialchars($output);
echo "</pre>";

echo "</div>";

echo "<h2>5. Análisis de la respuesta:</h2>";

// Intentar decodificar JSON
$json = json_decode($output, true);
if ($json) {
    echo "<p style='color: green;'>✅ Respuesta JSON válida</p>";
    echo "<pre>";
    print_r($json);
    echo "</pre>";
    
    if (isset($json['success']) && $json['success']) {
        echo "<p style='color: green;'>✅ Operación exitosa</p>";
        if (isset($json['data']) && is_array($json['data'])) {
            echo "<p style='color: green;'>✅ Datos encontrados: " . count($json['data']) . " diseños</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Operación falló: " . ($json['message'] ?? 'Sin mensaje') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Respuesta NO es JSON válido</p>";
    echo "<p><strong>Error de JSON:</strong> " . json_last_error_msg() . "</p>";
    
    // Analizar si hay HTML mezclado
    if (strpos($output, '<br') !== false || strpos($output, '<b>') !== false) {
        echo "<p style='color: red;'>❌ Detectado HTML de error mezclado con JSON</p>";
        echo "<p><strong>Causa probable:</strong> Error de PHP antes de generar JSON</p>";
    }
}

echo "<h2>6. Verificación de archivos:</h2>";

$archivos = [
    '/home/appscide/public_html/disenoCurricular/conf/config.php',
    '/home/appscide/public_html/disenoCurricular/math/forms/metodosDisenos.php',
    '/home/appscide/public_html/disenoCurricular/sql/conexion.php',
    '/home/appscide/public_html/disenoCurricular/app/forms/control/ajax.php'
];

foreach ($archivos as $archivo) {
    $rutaLocal = str_replace('/home/appscide/public_html/disenoCurricular', __DIR__, $archivo);
    if (file_exists($rutaLocal)) {
        echo "<p style='color: green;'>✅ " . basename($archivo) . " - Existe localmente</p>";
    } else {
        echo "<p style='color: red;'>❌ " . basename($archivo) . " - NO existe localmente</p>";
    }
}

echo "<hr>";
echo "<h2>📋 Instrucciones para el hosting:</h2>";
echo "<ol>";
echo "<li><strong>Subir este archivo</strong> al directorio raíz: test_ajax_directo.php</li>";
echo "<li><strong>Ejecutar en el navegador:</strong> tu-dominio.com/test_ajax_directo.php</li>";
echo "<li><strong>Verificar que no hay errores</strong> de rutas o includes</li>";
echo "<li><strong>Confirmar respuesta JSON válida</strong> sin HTML mezclado</li>";
echo "</ol>";

echo "<p><strong>Estado del test:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
