<?php
/**
 * Script de verificación después de la migración
 * Verifica que todos los archivos estén usando la nomenclatura correcta
 */

echo "=== VERIFICACIÓN POST-MIGRACIÓN ===\n\n";

// Archivos principales a verificar
$archivos = [
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosDisenos.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosComparacion.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/control/ajax.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/index.php'
];

// Patrones obsoletos que no deberían existir
$patrones_obsoletos = [
    'codigoDiseñoCompetencia[^R]' => 'Debería ser codigoDiseñoCompetenciaReporte',
    'codigoCompetencia[^R]' => 'Debería ser codigoCompetenciaReporte',
    'codigoDiseñoCompetenciaRap[^o]' => 'Debería ser codigoDiseñoCompetenciaReporteRap',
    'codigoRapAutomatico' => 'Campo eliminado - ya no existe',
    'codigoRapDiseño' => 'Campo eliminado - usar codigoRapReporte'
];

foreach ($archivos as $archivo) {
    if (!file_exists($archivo)) {
        echo "❌ Archivo no encontrado: $archivo\n";
        continue;
    }
    
    echo "📁 Verificando: " . basename($archivo) . "\n";
    $contenido = file_get_contents($archivo);
    
    $problemas = 0;
    foreach ($patrones_obsoletos as $patron => $descripcion) {
        if (preg_match("/$patron/", $contenido)) {
            echo "  ⚠️  Encontrado patrón obsoleto: $patron - $descripcion\n";
            $problemas++;
        }
    }
    
    if ($problemas === 0) {
        echo "  ✅ Sin patrones obsoletos detectados\n";
    }
    
    echo "\n";
}

echo "=== FORMULARIOS HTML ===\n";
echo "Los siguientes archivos también pueden necesitar actualización:\n";

$archivos_html = [
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/crear_competencias.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/editar_competencias.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/crear_raps.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/editar_raps.php'
];

foreach ($archivos_html as $archivo) {
    if (file_exists($archivo)) {
        echo "  📝 " . basename($archivo) . " (REVISAR)\n";
    }
}

echo "\n=== NUEVOS CAMPOS A CONSIDERAR ===\n";
echo "✨ codigoCompetenciaPDF - Campo nuevo en tabla competencias\n";
echo "✨ codigoRapReporte - Campo nuevo en tabla raps\n";

echo "\n=== SIGUIENTES PASOS ===\n";
echo "1. Verificar formularios HTML para nuevos campos\n";
echo "2. Probar funcionalidad de creación/edición\n";
echo "3. Probar funcionalidad de comparación de RAPs\n";
echo "4. Verificar que no hay errores en logs\n";
echo "5. Hacer backup antes de despliegue a producción\n";
?>
