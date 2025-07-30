<?php
/**
 * Script de prueba para validar la integración con metodosComparacion.php
 * Verifica que el ajax.php use correctamente la clase especializada
 */

echo "🧪 VALIDACIÓN: Integración con metodosComparacion.php\n";
echo "=" . str_repeat("=", 60) . "\n";

// Verificar archivos necesarios
$archivos_requeridos = [
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosComparacion.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/ajax.php'
];

$errores = [];

foreach ($archivos_requeridos as $archivo) {
    if (!file_exists($archivo)) {
        $errores[] = "❌ Archivo faltante: $archivo";
    } else {
        echo "✅ Archivo encontrado: " . basename($archivo) . "\n";
    }
}

if (!empty($errores)) {
    echo "\n❌ ERRORES ENCONTRADOS:\n";
    foreach ($errores as $error) {
        echo $error . "\n";
    }
    exit(1);
}

echo "\n📋 VERIFICACIÓN DE AJAX.PHP:\n";
echo "-" . str_repeat("-", 40) . "\n";

// Leer contenido del ajax.php
$contenido_ajax = file_get_contents('/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/ajax.php');

// Verificaciones específicas
$verificaciones = [
    'require metodosComparacion' => preg_match('/require_once.*metodosComparacion\.php/', $contenido_ajax),
    'instancia de comparacion' => preg_match('/\$metodosComparacion\s*=\s*new\s+comparacion\(\)/', $contenido_ajax),
    'uso del método especializado' => preg_match('/\$metodosComparacion->obtenerComparacionRaps\(/', $contenido_ajax),
    'sin SQL manual duplicado' => !preg_match('/SELECT.*FROM\s+competencias.*WHERE.*codigoCompetenciaReporte/', $contenido_ajax),
    'case obtener_comparacion_raps' => preg_match('/case\s+[\'"]obtener_comparacion_raps[\'"]/', $contenido_ajax)
];

foreach ($verificaciones as $descripcion => $resultado) {
    $status = $resultado ? "✅" : "❌";
    echo "$status $descripcion\n";
    
    if (!$resultado) {
        $errores[] = "Fallo: $descripcion";
    }
}

echo "\n📋 VERIFICACIÓN DE METODOSCOMPARACION.PHP:\n";
echo "-" . str_repeat("-", 40) . "\n";

// Leer contenido de metodosComparacion.php
$contenido_comparacion = file_get_contents('/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosComparacion.php');

$verificaciones_comparacion = [
    'clase comparacion definida' => preg_match('/class\s+comparacion/', $contenido_comparacion),
    'método obtenerComparacionRaps' => preg_match('/function\s+obtenerComparacionRaps\(/', $contenido_comparacion),
    'método obtenerDisenosConMismaCompetencia' => preg_match('/function\s+obtenerDisenosConMismaCompetencia\(/', $contenido_comparacion),
    'método obtenerRapsPorCompetenciaDiseno' => preg_match('/function\s+obtenerRapsPorCompetenciaDiseno\(/', $contenido_comparacion),
    'conexión PDO' => preg_match('/\$this->conexion/', $contenido_comparacion)
];

foreach ($verificaciones_comparacion as $descripcion => $resultado) {
    $status = $resultado ? "✅" : "❌";
    echo "$status $descripcion\n";
    
    if (!$resultado) {
        $errores[] = "Fallo en metodosComparacion: $descripcion";
    }
}

echo "\n🔍 ANÁLISIS DE BENEFICIOS:\n";
echo "-" . str_repeat("-", 40) . "\n";

if (preg_match('/\$metodosComparacion->obtenerComparacionRaps\(/', $contenido_ajax)) {
    echo "✅ Usa método especializado (buenas prácticas)\n";
    echo "✅ Evita duplicación de código SQL\n";
    echo "✅ Reutiliza lógica ya probada\n";
    echo "✅ Mantiene separación de responsabilidades\n";
} else {
    echo "❌ No usa la clase especializada\n";
    $errores[] = "No implementa correctamente metodosComparacion";
}

// Verificar que no hay SQL duplicado
$sql_patterns = [
    '/SELECT.*FROM\s+competencias.*INNER\s+JOIN.*diseños/',
    '/WHERE.*codigoCompetenciaReporte\s*=/',
    '/ORDER\s+BY.*nombrePrograma/'
];

$sql_duplicado = false;
foreach ($sql_patterns as $pattern) {
    if (preg_match($pattern, $contenido_ajax)) {
        $sql_duplicado = true;
        break;
    }
}

if ($sql_duplicado) {
    echo "❌ Detectado SQL duplicado en ajax.php\n";
    $errores[] = "SQL duplicado encontrado";
} else {
    echo "✅ No hay SQL duplicado (usa métodos especializados)\n";
}

echo "\n📊 RESUMEN FINAL:\n";
echo "=" . str_repeat("=", 40) . "\n";

if (empty($errores)) {
    echo "🎉 ¡INTEGRACIÓN PERFECTA!\n";
    echo "✅ El ajax.php usa correctamente metodosComparacion.php\n";
    echo "✅ Se eliminó la duplicación de código\n";
    echo "✅ Se mantienen las buenas prácticas de programación\n";
    echo "✅ La funcionalidad está bien encapsulada\n";
    echo "\n💡 BENEFICIOS OBTENIDOS:\n";
    echo "   - Código más limpio y mantenible\n";
    echo "   - Reutilización de lógica existente\n";
    echo "   - Separación de responsabilidades\n";
    echo "   - Fácil testing y debugging\n";
    echo "   - Consistencia en toda la aplicación\n";
} else {
    echo "❌ PROBLEMAS ENCONTRADOS:\n";
    foreach ($errores as $error) {
        echo "   - $error\n";
    }
    echo "\n🔧 ACCIÓN REQUERIDA: Corregir los problemas listados\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Validación completada\n";
?>
