<?php
/**
 * Script de migración para adaptar la aplicación a la nueva estructura de base de datos
 * 
 * CAMBIOS PRINCIPALES IDENTIFICADOS:
 * 
 * TABLA competencias:
 * - codigoDiseñoCompetencia → codigoDiseñoCompetenciaReporte (PRIMARY KEY)
 * - codigoCompetencia → codigoCompetenciaReporte  
 * - Nuevo campo: codigoCompetenciaPDF
 * 
 * TABLA raps:
 * - codigoDiseñoCompetenciaRap → codigoDiseñoCompetenciaReporteRap (PRIMARY KEY)
 * - codigoRapAutomatico → ELIMINADO (ya no existe auto-increment)
 * - codigoRapDiseño → ELIMINADO
 * - Nuevo campo: codigoRapReporte
 * 
 * TABLA diseños:
 * - Sin cambios estructurales significativos
 */

echo "=== SCRIPT DE MIGRACIÓN PARA NUEVA ESTRUCTURA DB ===\n";
echo "Este script identifica los archivos que necesitan actualización\n\n";

// Archivos que requieren actualización
$archivos_a_revisar = [
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosDisenos.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosComparacion.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/control/ajax.php',
    '/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/index.php',
    // Agregar más archivos según sea necesario
];

// Patrones de búsqueda para identificar código obsoleto
$patrones_obsoletos = [
    'codigoDiseñoCompetencia' => 'codigoDiseñoCompetenciaReporte',
    'codigoCompetencia' => 'codigoCompetenciaReporte', 
    'codigoDiseñoCompetenciaRap' => 'codigoDiseñoCompetenciaReporteRap',
    'codigoRapAutomatico' => '[ELIMINAR - ya no existe]',
    'codigoRapDiseño' => '[ELIMINAR - reemplazar con codigoRapReporte]'
];

echo "PATRONES A ACTUALIZAR:\n";
foreach ($patrones_obsoletos as $viejo => $nuevo) {
    echo "  $viejo → $nuevo\n";
}

echo "\nARCHIVOS PRINCIPALES A REVISAR:\n";
foreach ($archivos_a_revisar as $archivo) {
    if (file_exists($archivo)) {
        echo "  ✓ $archivo\n";
    } else {
        echo "  ✗ $archivo (no encontrado)\n";
    }
}

echo "\n=== RECOMENDACIONES ===\n";
echo "1. Crear backup de la aplicación actual\n";
echo "2. Actualizar metodosDisenos.php paso a paso\n";
echo "3. Actualizar formularios HTML para nuevos campos\n";
echo "4. Ajustar consultas AJAX para nueva estructura\n";
echo "5. Probar funcionalidad completa en entorno local\n";
echo "6. Desplegar a producción solo después de pruebas exitosas\n";
?>
