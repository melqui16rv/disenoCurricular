<?php
// Test directo del endpoint AJAX
echo "=== TEST ENDPOINT AJAX ===\n";

// Simular parámetros POST
$_POST['accion'] = 'obtener_comparacion_raps';
$_POST['codigoCompetencia'] = '220201503';
$_POST['disenoActual'] = '100240-1';

echo "Parámetros enviados:\n";
echo "- accion: " . $_POST['accion'] . "\n";
echo "- codigoCompetencia: " . $_POST['codigoCompetencia'] . "\n";
echo "- disenoActual: " . $_POST['disenoActual'] . "\n\n";

echo "=== INICIANDO LLAMADA AL ENDPOINT ===\n";

// Capturar la salida del endpoint
ob_start();
include 'ajax.php';
$output = ob_get_contents();
ob_end_clean();

echo "=== RESPUESTA DEL ENDPOINT ===\n";
echo "Longitud de respuesta: " . strlen($output) . " caracteres\n";
echo "Primeros 200 caracteres:\n";
echo substr($output, 0, 200) . "\n\n";

echo "=== ANÁLISIS DE CONTENT-TYPE ===\n";
$headers = headers_list();
foreach ($headers as $header) {
    if (stripos($header, 'content-type') !== false) {
        echo "Header encontrado: " . $header . "\n";
    }
}

echo "\n=== VALIDACIÓN JSON ===\n";
$json = json_decode($output, true);
if ($json === null) {
    echo "ERROR: No es JSON válido\n";
    echo "Error JSON: " . json_last_error_msg() . "\n";
    echo "Respuesta completa:\n" . $output . "\n";
} else {
    echo "✓ JSON válido detectado\n";
    echo "Estructura: " . json_encode(array_keys($json), JSON_PRETTY_PRINT) . "\n";
    if (isset($json['success'])) {
        echo "Success: " . ($json['success'] ? 'true' : 'false') . "\n";
        echo "Message: " . ($json['message'] ?? 'No message') . "\n";
    }
}
?>
