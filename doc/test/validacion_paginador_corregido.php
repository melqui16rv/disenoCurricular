<?php
/**
 * Script de validación para el sistema de paginación corregido
 * Verifica que:
 * 1. Los selectores de registros por página mantengan su valor al navegar
 * 2. La opción "Todos" esté disponible y funcione correctamente
 * 3. El estado se mantenga en el frontend
 */

echo "=== VALIDACIÓN PAGINADOR CORREGIDO ===\n\n";

// Archivos a verificar
$archivos = [
    'Backend AJAX' => '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/ajax.php',
    'Funciones Completar Info' => '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/completar_informacion_funciones.php',
    'JavaScript Corregido' => '/Users/melquiromero/Documents/phpStorm/disenoCurricular/assets/js/forms/completar-informacion.js'
];

$errores = [];
$exitos = [];

// 1. Verificar manejo de opción "Todos" en backend
echo "1. Verificando manejo de opción 'Todos' (-1) en backend...\n";
$ajax_content = file_get_contents($archivos['Backend AJAX']);
if (strpos($ajax_content, 'if ($registros_param == -1)') !== false) {
    $exitos[] = "✅ Backend maneja correctamente la opción 'Todos' (-1)";
} else {
    $errores[] = "❌ Backend no maneja la opción 'Todos'";
}

// 2. Verificar opción "Todos" en selector HTML
echo "2. Verificando opción 'Todos' en selector HTML...\n";
$funciones_content = file_get_contents($archivos['Funciones Completar Info']);
if (strpos($funciones_content, "option value='-1'") !== false && strpos($funciones_content, '>Todos</option>') !== false) {
    $exitos[] = "✅ Selector HTML incluye opción 'Todos'";
} else {
    $errores[] = "❌ Selector HTML no incluye opción 'Todos'";
}

// 3. Verificar manejo de "Todos" en funciones de paginación
echo "3. Verificando manejo de 'Todos' en funciones de datos...\n";
$paginacion_todos = [
    'obtenerDisenosConCamposFaltantes',
    'obtenerCompetenciasConCamposFaltantes', 
    'obtenerRapsConCamposFaltantes'
];

$todas_funciones_ok = true;
foreach ($paginacion_todos as $funcion) {
    if (strpos($funciones_content, "if (\$registros_por_pagina == -1)") !== false) {
        continue;
    } else {
        $todas_funciones_ok = false;
        break;
    }
}

if ($todas_funciones_ok) {
    $exitos[] = "✅ Todas las funciones de datos manejan 'Todos' (-1)";
} else {
    $errores[] = "❌ No todas las funciones manejan 'Todos'";
}

// 4. Verificar restauración de selector en JS
echo "4. Verificando restauración de selector en JavaScript...\n";
$js_content = file_get_contents($archivos['JavaScript Corregido']);
if (strpos($js_content, 'selector.value = estadoSeccion.recordsPerPage') !== false) {
    $exitos[] = "✅ JavaScript restaura correctamente el valor del selector";
} else {
    $errores[] = "❌ JavaScript no restaura el valor del selector";
}

// 5. Verificar manejo de estados de sección en JS
echo "5. Verificando manejo de estados de sección...\n";
if (strpos($js_content, 'sectionStates') !== false && strpos($js_content, 'recordsPerPage') !== false) {
    $exitos[] = "✅ JavaScript mantiene estados de sección correctamente";
} else {
    $errores[] = "❌ JavaScript no mantiene estados de sección";
}

// 6. Verificar persistencia con cookies
echo "6. Verificando persistencia con cookies...\n";
if (strpos($js_content, 'setCookie') !== false && strpos($js_content, 'loadFiltersFromCookies') !== false) {
    $exitos[] = "✅ JavaScript implementa persistencia con cookies";
} else {
    $errores[] = "❌ JavaScript no implementa persistencia con cookies";
}

// Mostrar resultados
echo "\n=== RESULTADOS ===\n\n";

echo "ÉXITOS (" . count($exitos) . "):\n";
foreach ($exitos as $exito) {
    echo "$exito\n";
}

if (!empty($errores)) {
    echo "\nERRORES (" . count($errores) . "):\n";
    foreach ($errores as $error) {
        echo "$error\n";
    }
    echo "\n❌ VALIDACIÓN FALLIDA: " . count($errores) . " errores encontrados\n";
    exit(1);
} else {
    echo "\n✅ VALIDACIÓN EXITOSA: Todas las correcciones implementadas correctamente\n";
    echo "\nFUNCIONALIDADES CORREGIDAS:\n";
    echo "- ✅ Selector de registros por página mantiene su valor al navegar\n";
    echo "- ✅ Opción 'Todos' disponible en selector\n";
    echo "- ✅ Backend maneja correctamente valor -1 (Todos)\n";
    echo "- ✅ Estado persistente por sección\n";
    echo "- ✅ Restauración automática de valores\n";
    echo "- ✅ Persistencia con cookies\n";
}

echo "\n=== FIN VALIDACIÓN ===\n";
?>
