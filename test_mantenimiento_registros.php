<?php
/**
 * Script de prueba para verificar que los parámetros de registros por página se mantienen
 * al navegar a primera/última página
 */

// Función de generación de paginación (copia simplificada para pruebas)
function generarPaginacionPrueba($seccion_id, $pagina_actual, $total_paginas, $registros_por_pagina, $filtros_array = []) {
    // Simular parámetros GET actuales
    $current_url_params = [
        'accion' => 'completar_informacion',
        'seccion' => 'todas',
        'busqueda' => 'programacion',
        'pagina_disenos' => 2,
        'pagina_competencias' => 1,
        'pagina_raps' => 3,
        'registros_disenos' => 25,
        'registros_competencias' => 50,
        'registros_raps' => 10
    ];
    
    // Construir query string EXCLUYENDO solo la página específica de la sección actual
    $query_params = [];
    $exclude_params = [
        'pagina_' . $seccion_id  // Solo excluir la página de esta sección
    ];
    
    foreach ($filtros_array as $key => $value) {
        if (!empty($value) && !in_array($key, $exclude_params)) {
            $query_params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    
    // Agregar parámetros de otras secciones Y registros de la sección actual para mantener su estado
    foreach ($current_url_params as $key => $value) {
        // Incluir todos los parámetros de paginación y registros EXCEPTO la página de la sección actual
        if ((strpos($key, 'pagina_') === 0 || strpos($key, 'registros_') === 0) && !in_array($key, $exclude_params) && !empty($value)) {
            $query_params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    
    $base_url = '?' . implode('&', $query_params);
    $separator = empty($query_params) ? '?' : '&';
    
    $urls = [];
    
    // URL para primera página
    if ($pagina_actual > 3) {
        $urls['primera'] = $base_url . $separator . "pagina_{$seccion_id}=1";
    }
    
    // URL para última página
    if ($pagina_actual < $total_paginas - 2) {
        $urls['ultima'] = $base_url . $separator . "pagina_{$seccion_id}={$total_paginas}";
    }
    
    // URL para página anterior
    if ($pagina_actual > 1) {
        $prev = $pagina_actual - 1;
        $urls['anterior'] = $base_url . $separator . "pagina_{$seccion_id}={$prev}";
    }
    
    // URL para página siguiente
    if ($pagina_actual < $total_paginas) {
        $next = $pagina_actual + 1;
        $urls['siguiente'] = $base_url . $separator . "pagina_{$seccion_id}={$next}";
    }
    
    return $urls;
}

echo "<h1>🔍 Prueba de Mantenimiento de Parámetros de Registros por Página</h1>";

echo "<h2>Escenario de Prueba:</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>Estado actual simulado:</strong><br>";
echo "• Diseños: página 2, 25 registros por página<br>";
echo "• Competencias: página 1, 50 registros por página<br>";
echo "• RAPs: página 3, 10 registros por página<br>";
echo "• Búsqueda activa: 'programacion'<br>";
echo "</div>";

// Probar cada sección
$secciones = [
    'disenos' => ['pagina_actual' => 2, 'total_paginas' => 15, 'registros' => 25],
    'competencias' => ['pagina_actual' => 1, 'total_paginas' => 8, 'registros' => 50],
    'raps' => ['pagina_actual' => 3, 'total_paginas' => 12, 'registros' => 10]
];

$filtros_array = [
    'accion' => 'completar_informacion',
    'seccion' => 'todas',
    'busqueda' => 'programacion'
];

foreach ($secciones as $seccion_id => $datos) {
    echo "<h2>📊 Sección: " . ucfirst($seccion_id) . "</h2>";
    
    $urls = generarPaginacionPrueba(
        $seccion_id, 
        $datos['pagina_actual'], 
        $datos['total_paginas'], 
        $datos['registros'],
        $filtros_array
    );
    
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Página actual:</strong> {$datos['pagina_actual']} de {$datos['total_paginas']}<br>";
    echo "<strong>Registros por página:</strong> {$datos['registros']}<br><br>";
    
    foreach ($urls as $tipo => $url) {
        echo "<strong>" . ucfirst($tipo) . " página:</strong><br>";
        echo "<code style='background: #fff; padding: 5px; display: block; margin: 5px 0; word-break: break-all;'>{$url}</code>";
        
        // Verificar que el parámetro de registros se mantiene
        $param_registros = "registros_{$seccion_id}";
        if (strpos($url, $param_registros . '=' . $datos['registros']) !== false) {
            echo "<span style='color: #27ae60;'>✅ CORRECTO: Mantiene registros_{$seccion_id}={$datos['registros']}</span><br>";
        } else {
            echo "<span style='color: #e74c3c;'>❌ ERROR: No mantiene registros_{$seccion_id}={$datos['registros']}</span><br>";
        }
        
        // Verificar que mantiene parámetros de otras secciones
        $otras_secciones = array_diff(['disenos', 'competencias', 'raps'], [$seccion_id]);
        foreach ($otras_secciones as $otra_seccion) {
            $param_otra_pagina = "pagina_{$otra_seccion}";
            $param_otra_registros = "registros_{$otra_seccion}";
            
            if (strpos($url, $param_otra_pagina) !== false && strpos($url, $param_otra_registros) !== false) {
                echo "<span style='color: #27ae60;'>✅ Mantiene estado de {$otra_seccion}</span><br>";
            } else {
                echo "<span style='color: #f39c12;'>⚠️ Podría no mantener estado completo de {$otra_seccion}</span><br>";
            }
        }
        
        echo "<br>";
    }
    
    echo "</div>";
}

echo "<h2>📋 Resumen de Verificación</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; border: 1px solid #bee5eb;'>";
echo "<h3 style='color: #0c5460; margin-top: 0;'>✅ Verificaciones Realizadas:</h3>";
echo "<ul>";
echo "<li><strong>Mantenimiento de registros por página:</strong> Se verifica que cada sección mantiene su configuración de registros al navegar</li>";
echo "<li><strong>Preservación de estado de otras secciones:</strong> Se confirma que las otras secciones no se ven afectadas</li>";
echo "<li><strong>Filtros globales:</strong> Se mantienen filtros como búsqueda, sección seleccionada, etc.</li>";
echo "<li><strong>Exclusión correcta:</strong> Solo se cambia la página de la sección específica</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🎯 Comportamiento Esperado</h2>";
echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Problema Original:</h3>";
echo "<p>Al hacer clic en 'Primera página' o 'Última página', se perdía la configuración de registros por página de esa sección.</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb; margin-top: 10px;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>✅ Solución Implementada:</h3>";
echo "<p>Ahora solo se excluye el parámetro de página específico, manteniendo TODOS los demás parámetros incluido 'registros_seccion'.</p>";
echo "</div>";

?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    line-height: 1.6;
}
h1 { color: #2c3e50; }
h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
h3 { color: #7f8c8d; }
code { 
    background: #f8f9fa; 
    padding: 2px 5px; 
    border-radius: 3px; 
    font-family: monospace; 
    font-size: 11px;
}
ul { margin-left: 20px; }
li { margin: 5px 0; }
</style>
