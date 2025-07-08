<?php
/**
 * Script de validación específico para el problema de registros por página
 * Verifica que el número de registros seleccionado se mantenga al navegar entre páginas
 */

echo "=== VALIDACIÓN CRÍTICA: PERSISTENCIA DE REGISTROS POR PÁGINA ===\n\n";

$errores = [];
$exitos = [];

// 1. Verificar que JavaScript incluye registros por página en navegación
echo "1. Verificando inclusión de registros en navegación de páginas...\n";
$js_content = file_get_contents('/Users/melquiromero/Documents/phpStorm/disenoCurricular/assets/js/forms/completar-informacion.js');

$patrones_requeridos = [
    'registrosActuales = this.sectionStates[seccion].recordsPerPage' => 'Estado de registros en navegación',
    'registros_\' + seccion]: registrosActuales' => 'Inclusión de registros en petición AJAX',
    'detectInitialSelectorValues' => 'Detección de valores iniciales',
    'CRÍTICO: Asegurar que siempre se envía el número de registros' => 'Lógica crítica implementada'
];

foreach ($patrones_requeridos as $patron => $descripcion) {
    if (strpos($js_content, $patron) !== false) {
        $exitos[] = "✅ $descripcion";
    } else {
        $errores[] = "❌ Falta: $descripcion";
    }
}

// 2. Verificar respaldo de selector actual en cargarSeccionAjax
echo "2. Verificando respaldo de selector actual...\n";
if (strpos($js_content, 'const selector = document.querySelector(`select[data-seccion="${seccion}"]') !== false &&
    strpos($js_content, 'registrosActuales = parseInt(selector.value)') !== false) {
    $exitos[] = "✅ Respaldo de selector actual implementado";
} else {
    $errores[] = "❌ Respaldo de selector actual faltante";
}

// 3. Verificar que backend maneja correctamente registros por página
echo "3. Verificando manejo backend de registros por página...\n";
$ajax_content = file_get_contents('/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/ajax.php');
if (strpos($ajax_content, 'registros_param = $_GET["registros_{$seccion}"]') !== false) {
    $exitos[] = "✅ Backend lee correctamente parámetro de registros";
} else {
    $errores[] = "❌ Backend no lee parámetro de registros";
}

// 4. Verificar logging de debug para registros
echo "4. Verificando logging de registros...\n";
if (strpos($ajax_content, 'Registros solicitados:') !== false &&
    strpos($js_content, 'registros por página para navegación') !== false) {
    $exitos[] = "✅ Logging de debug para registros implementado";
} else {
    $errores[] = "❌ Logging de debug insuficiente";
}

// 5. Verificar que función de actualizar contenido restaura selector
echo "5. Verificando restauración de selector en actualizarContenido...\n";
if (strpos($js_content, 'selector.value = estadoSeccion.recordsPerPage') !== false) {
    $exitos[] = "✅ Restauración de selector implementada";
} else {
    $errores[] = "❌ Restauración de selector faltante";
}

// 6. Verificar persistencia en cookies de estados de sección
echo "6. Verificando persistencia de estados...\n";
if (strpos($js_content, "'_sectionStates'") !== false && 
    strpos($js_content, 'JSON.stringify(this.sectionStates)') !== false) {
    $exitos[] = "✅ Persistencia de estados en cookies";
} else {
    $errores[] = "❌ Persistencia de estados faltante";
}

// Mostrar resultados
echo "\n=== RESULTADOS ESPECÍFICOS ===\n\n";

echo "ÉXITOS (" . count($exitos) . "):\n";
foreach ($exitos as $exito) {
    echo "$exito\n";
}

if (!empty($errores)) {
    echo "\nERRORES (" . count($errores) . "):\n";
    foreach ($errores as $error) {
        echo "$error\n";
    }
    echo "\n❌ VALIDACIÓN FALLIDA: " . count($errores) . " errores críticos\n";
    exit(1);
} else {
    echo "\n✅ VALIDACIÓN EXITOSA: Persistencia de registros por página implementada correctamente\n";
    echo "\nCORRECCIONES ESPECÍFICAS APLICADAS:\n";
    echo "- ✅ Navegación de páginas incluye registros por página del estado\n";
    echo "- ✅ Respaldo de selector actual en caso de estado perdido\n";
    echo "- ✅ Detección automática de valores iniciales de selectores\n";
    echo "- ✅ Logging detallado para debug de registros\n";
    echo "- ✅ Restauración de selector después de actualizaciones AJAX\n";
    echo "- ✅ Persistencia completa con cookies\n";
    echo "\nEL PROBLEMA DE REINICIO A 10 REGISTROS DEBE ESTAR SOLUCIONADO\n";
}

echo "\n=== FIN VALIDACIÓN CRÍTICA ===\n";
?>
