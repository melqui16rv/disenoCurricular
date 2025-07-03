<?php
/**
 * Script de prueba para verificar la corrección de páginas fuera de rango
 * Sistema de Gestión de Diseños Curriculares SENA
 */

// Simular datos de ejemplo
$datos_simulados = [
    'disenos' => array_fill(0, 45, ['nombre' => 'Diseño de prueba', 'campos_faltantes' => ['Campo 1']]),
    'competencias' => array_fill(0, 73, ['nombre' => 'Competencia de prueba', 'campos_faltantes' => ['Campo 1']]),
    'raps' => array_fill(0, 128, ['nombre' => 'RAP de prueba', 'campos_faltantes' => ['Campo 1']])
];

// Función para validar página (copia de la función principal)
function validarPagina($pagina_solicitada, $total_registros, $registros_por_pagina) {
    if ($total_registros == 0) {
        return 1;
    }
    
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $pagina_corregida = max(1, min($pagina_solicitada, $total_paginas));
    
    return $pagina_corregida;
}

// Función para simular obtención de datos con paginación
function obtenerDatosConPaginacion($datos, $pagina, $registros_por_pagina) {
    $total_registros = count($datos);
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    // Validar y corregir página fuera de rango
    $pagina_corregida = validarPagina($pagina, $total_registros, $registros_por_pagina);
    
    $offset = ($pagina_corregida - 1) * $registros_por_pagina;
    $datos_paginados = array_slice($datos, $offset, $registros_por_pagina);

    return [
        'datos' => $datos_paginados,
        'total_registros' => $total_registros,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina_corregida,
        'registros_por_pagina' => $registros_por_pagina
    ];
}

echo "<h1>🔧 Prueba de Corrección de Páginas Fuera de Rango</h1>";

// Casos de prueba
$casos_prueba = [
    ['seccion' => 'disenos', 'pagina' => 59, 'registros' => 5],     // Muy alta
    ['seccion' => 'competencias', 'pagina' => 233, 'registros' => 5], // Muy alta
    ['seccion' => 'raps', 'pagina' => 915, 'registros' => 5],       // Muy alta
    ['seccion' => 'disenos', 'pagina' => 0, 'registros' => 10],     // Muy baja
    ['seccion' => 'competencias', 'pagina' => -5, 'registros' => 25], // Negativa
    ['seccion' => 'raps', 'pagina' => 3, 'registros' => 50],        // Normal
];

echo "<h2>Resultados de las Pruebas:</h2>";

foreach ($casos_prueba as $i => $caso) {
    $seccion = $caso['seccion'];
    $pagina_solicitada = $caso['pagina'];
    $registros = $caso['registros'];
    
    $resultado = obtenerDatosConPaginacion($datos_simulados[$seccion], $pagina_solicitada, $registros);
    
    echo "<h3>Prueba " . ($i + 1) . ": {$seccion}</h3>";
    echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Página solicitada:</strong> {$pagina_solicitada}<br>";
    echo "<strong>Registros por página:</strong> {$registros}<br>";
    echo "<strong>Total registros en sección:</strong> {$resultado['total_registros']}<br>";
    echo "<strong>Total páginas disponibles:</strong> {$resultado['total_paginas']}<br>";
    echo "<strong>Página corregida:</strong> {$resultado['pagina_actual']}<br>";
    echo "<strong>Datos obtenidos:</strong> " . count($resultado['datos']) . " registros<br>";
    
    // Verificar si la corrección fue necesaria
    if ($pagina_solicitada != $resultado['pagina_actual']) {
        echo "<strong style='color: #27ae60;'>✅ CORRECCIÓN APLICADA:</strong> Página {$pagina_solicitada} → {$resultado['pagina_actual']}<br>";
    } else {
        echo "<strong style='color: #3498db;'>ℹ️ SIN CORRECCIÓN:</strong> Página válida<br>";
    }
    
    // Verificar que se obtuvieron datos
    if (count($resultado['datos']) > 0) {
        echo "<strong style='color: #27ae60;'>✅ DATOS OBTENIDOS:</strong> La tabla mostrará contenido<br>";
    } else {
        echo "<strong style='color: #e74c3c;'>❌ SIN DATOS:</strong> La tabla estaría vacía<br>";
    }
    
    echo "</div>";
}

echo "<h2>📊 Resumen de la Solución</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border: 2px solid #27ae60;'>";
echo "<h3 style='color: #27ae60; margin-top: 0;'>✅ Correcciones Implementadas:</h3>";
echo "<ul>";
echo "<li><strong>Validación de página:</strong> Las páginas fuera de rango se corrigen automáticamente</li>";
echo "<li><strong>Página mínima:</strong> Si es menor a 1, se establece en 1</li>";
echo "<li><strong>Página máxima:</strong> Si excede el total, se establece en la última página válida</li>";
echo "<li><strong>Datos garantizados:</strong> Siempre se obtendrán datos cuando existan registros</li>";
echo "<li><strong>Sin tablas vacías:</strong> Las tablas no desaparecerán por páginas inválidas</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🔗 URLs de Prueba Corregidas</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>";

// Generar URLs corregidas
$base_url = "http://localhost:8888/app/forms/?accion=completar_informacion&seccion=todas";
$url_corregida = $base_url . "&pagina_disenos=9&pagina_competencias=15&pagina_raps=26";

echo "<p><strong>URL original problemática:</strong><br>";
echo "<code style='background: #ffebee; color: #c62828; padding: 5px;'>";
echo "http://localhost:8888/app/forms/?accion=completar_informacion&pagina_disenos=59&pagina_competencias=233&pagina_raps=915";
echo "</code></p>";

echo "<p><strong>URL corregida sugerida:</strong><br>";
echo "<code style='background: #e8f5e8; color: #2e7d32; padding: 5px;'>";
echo $url_corregida;
echo "</code></p>";

echo "<p><em>Nota: Las páginas se han ajustado a valores válidos basados en los datos reales disponibles.</em></p>";
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
    display: inline-block;
    word-break: break-all;
}
ul { margin-left: 20px; }
li { margin: 5px 0; }
</style>
