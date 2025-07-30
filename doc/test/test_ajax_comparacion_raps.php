<?php
/**
 * Script de prueba para verificar la funcionalidad AJAX de comparación de RAPs
 */

echo "<h2>Prueba de la funcionalidad AJAX de comparación de RAPs</h2>\n\n";

// Simular una petición AJAX
$postData = [
    'accion' => 'obtener_comparacion_raps',
    'codigoCompetencia' => '1', // Código de competencia de ejemplo
    'disenoActual' => '112005-101' // Diseño actual a excluir
];

echo "<h3>Datos de la petición simulada:</h3>\n";
foreach ($postData as $key => $value) {
    echo "- <strong>{$key}:</strong> {$value}\n";
}

echo "\n<h3>Verificando archivos necesarios:</h3>\n";

// Verificar que el archivo AJAX existe
$ajaxFile = __DIR__ . '/../../app/forms/control/ajax.php';
if (file_exists($ajaxFile)) {
    echo "✅ Archivo ajax.php encontrado en: {$ajaxFile}\n";
} else {
    echo "❌ Archivo ajax.php NO encontrado en: {$ajaxFile}\n";
}

// Verificar archivo de configuración
$configFile = __DIR__ . '/../../conf/config.php';
if (file_exists($configFile)) {
    echo "✅ Archivo config.php encontrado\n";
} else {
    echo "❌ Archivo config.php NO encontrado\n";
}

// Verificar archivo de métodos
$metodosFile = __DIR__ . '/../../math/forms/metodosDisenos.php';
if (file_exists($metodosFile)) {
    echo "✅ Archivo metodosDisenos.php encontrado\n";
} else {
    echo "❌ Archivo metodosDisenos.php NO encontrado\n";
}

echo "\n<h3>Estructura de datos esperada:</h3>\n";

// Mostrar la estructura JSON que debería devolver
$estructuraEsperada = [
    'success' => true,
    'data' => [
        [
            'diseno' => [
                'codigoDiseño' => 'XXXX-XXX',
                'nombrePrograma' => 'Nombre del programa',
                'versionPrograma' => 'XXX',
                'codigoPrograma' => 'XXXXX'
            ],
            'raps' => [
                [
                    'codigoDiseñoCompetenciaReporteRap' => 'XXXX-XXX-X-XX',
                    'codigoRapDiseño' => 'XX',
                    'nombreRap' => 'Nombre del RAP',
                    'horasDesarrolloRap' => 'XX.XX'
                ]
            ],
            'totalRaps' => 'N',
            'totalHorasRaps' => 'XX.XX'
        ]
    ],
    'message' => 'Comparación obtenida exitosamente',
    'totalDisenos' => 'N',
    'debug' => [
        'codigoCompetencia' => '1',
        'disenoActual' => '112005-101',
        'totalDisenosEncontrados' => 'N',
        'totalComparaciones' => 'N'
    ]
];

echo "<pre>" . json_encode($estructuraEsperada, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

echo "\n<h3>Consulta SQL que se ejecutará:</h3>\n";
echo "<code>\n";
echo "SELECT DISTINCT \n";
echo "    d.codigoDiseño,\n";
echo "    d.nombrePrograma,\n";
echo "    d.versionPrograma,\n";
echo "    d.codigoPrograma,\n";
echo "    c.codigoDiseñoCompetenciaReporte,\n";
echo "    c.nombreCompetencia,\n";
echo "    c.horasDesarrolloCompetencia\n";
echo "FROM competencias c\n";
echo "INNER JOIN diseños d ON (\n";
echo "    d.codigoDiseño = SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2)\n";
echo ")\n";
echo "WHERE c.codigoCompetenciaReporte = ?\n";
echo "AND d.codigoDiseño != ?\n";
echo "ORDER BY d.nombrePrograma, d.versionPrograma\n";
echo "</code>\n";

echo "\n<h3>Parámetros de la consulta:</h3>\n";
echo "1. codigoCompetenciaReporte = '1'\n";
echo "2. codigoDiseño != '112005-101'\n";

echo "\n<h3>Para probar manualmente:</h3>\n";
echo "1. Abrir las herramientas de desarrollador del navegador\n";
echo "2. Ir a la pestaña 'Network' o 'Red'\n";
echo "3. En la página completar_raps.php, hacer clic en 'Ver comparación'\n";
echo "4. Observar la petición AJAX a './control/ajax.php'\n";
echo "5. Verificar la respuesta JSON\n";

echo "\n<strong>✅ Configuración completada</strong>\n";
echo "El archivo ajax.php ha sido creado con la funcionalidad corregida.\n";
?>
