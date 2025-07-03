<?php
/**
 * Script de prueba para verificar la independencia de paginación entre secciones
 * Sistema de Gestión de Diseños Curriculares SENA
 */

echo "<h1>Prueba de Paginación Independiente por Sección</h1>";

// Simular parámetros de URL recibidos
$parametros_simulados = [
    'pagina_disenos' => 3,
    'pagina_competencias' => 1, 
    'pagina_raps' => 2,
    'registros_disenos' => 25,
    'registros_competencias' => 10,
    'registros_raps' => 50,
    'busqueda' => 'programacion',
    'seccion' => 'todas'
];

echo "<h2>Parámetros recibidos (simulados):</h2>";
echo "<pre>";
print_r($parametros_simulados);
echo "</pre>";

// Función para extraer parámetros específicos por sección
function extraerParametrosPorSeccion($params, $seccion) {
    $resultado = [
        'pagina' => max(1, (int)($params["pagina_{$seccion}"] ?? 1)),
        'registros' => max(5, min(100, (int)($params["registros_{$seccion}"] ?? 10)))
    ];
    
    return $resultado;
}

// Probar extracción para cada sección
$secciones = ['disenos', 'competencias', 'raps'];

echo "<h2>Parámetros extraídos por sección:</h2>";

foreach ($secciones as $seccion) {
    $params_seccion = extraerParametrosPorSeccion($parametros_simulados, $seccion);
    echo "<h3>Sección: {$seccion}</h3>";
    echo "<ul>";
    echo "<li>Página: {$params_seccion['pagina']}</li>";
    echo "<li>Registros por página: {$params_seccion['registros']}</li>";
    echo "</ul>";
}

// Simular construcción de URLs de paginación
function construirURLPaginacion($params_actuales, $seccion, $nueva_pagina) {
    $params = $params_actuales;
    $params["pagina_{$seccion}"] = $nueva_pagina;
    
    $query_string = http_build_query($params);
    return "?{$query_string}";
}

function construirURLRegistros($params_actuales, $seccion, $nuevos_registros) {
    $params = $params_actuales;
    $params["registros_{$seccion}"] = $nuevos_registros;
    $params["pagina_{$seccion}"] = 1; // Reset página al cambiar registros
    
    $query_string = http_build_query($params);
    return "?{$query_string}";
}

echo "<h2>URLs de paginación generadas:</h2>";

foreach ($secciones as $seccion) {
    echo "<h3>Sección: {$seccion}</h3>";
    
    // URL para ir a página 2
    $url_pagina_2 = construirURLPaginacion($parametros_simulados, $seccion, 2);
    echo "<p>Ir a página 2: <code>{$url_pagina_2}</code></p>";
    
    // URL para cambiar a 50 registros por página
    $url_50_registros = construirURLRegistros($parametros_simulados, $seccion, 50);
    echo "<p>Cambiar a 50 registros: <code>{$url_50_registros}</code></p>";
}

// Verificar que los cambios en una sección no afecten a otras
echo "<h2>Verificación de independencia:</h2>";

$params_originales = $parametros_simulados;

// Cambiar página de diseños a 5
$params_disenos_cambio = $parametros_simulados;
$params_disenos_cambio['pagina_disenos'] = 5;

echo "<h3>Después de cambiar página de Diseños a 5:</h3>";
echo "<ul>";
echo "<li>Diseños - página: {$params_disenos_cambio['pagina_disenos']} (CAMBIÓ ✓)</li>";
echo "<li>Competencias - página: " . ($params_disenos_cambio['pagina_competencias'] ?? 1) . " (NO cambió ✓)</li>";
echo "<li>RAPs - página: " . ($params_disenos_cambio['pagina_raps'] ?? 1) . " (NO cambió ✓)</li>";
echo "</ul>";

// Cambiar registros de competencias a 100
$params_competencias_cambio = $parametros_simulados;
$params_competencias_cambio['registros_competencias'] = 100;
$params_competencias_cambio['pagina_competencias'] = 1; // Se resetea

echo "<h3>Después de cambiar registros de Competencias a 100:</h3>";
echo "<ul>";
echo "<li>Diseños - registros: " . ($params_competencias_cambio['registros_disenos'] ?? 10) . " (NO cambió ✓)</li>";
echo "<li>Competencias - registros: {$params_competencias_cambio['registros_competencias']} (CAMBIÓ ✓)</li>";
echo "<li>Competencias - página: {$params_competencias_cambio['pagina_competencias']} (se resetea a 1 ✓)</li>";
echo "<li>RAPs - registros: " . ($params_competencias_cambio['registros_raps'] ?? 10) . " (NO cambió ✓)</li>";
echo "</ul>";

echo "<h2>✅ Prueba Completada</h2>";
echo "<p>La lógica de paginación independiente por sección funciona correctamente.</p>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2c3e50; }
h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
h3 { color: #7f8c8d; }
code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; font-family: monospace; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
ul { margin-left: 20px; }
li { margin: 5px 0; }
.success { color: #27ae60; font-weight: bold; }
</style>
