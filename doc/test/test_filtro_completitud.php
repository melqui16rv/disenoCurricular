<?php
/**
 * Script de prueba para verificar el filtro de completitud
 */

// Incluir archivos necesarios
require_once __DIR__ . '/../../sql/conexion.php';
require_once __DIR__ . '/../../math/forms/metodosDisenos.php';

try {
    echo "<h2>Prueba del filtro de completitud</h2>\n\n";
    
    $metodosDisenos = new MetodosDisenos();
    
    // Probar obtener diseños con filtro de completos
    echo "<h3>1. Diseños con información COMPLETA:</h3>\n";
    $disenosCompletos = $metodosDisenos->obtenerDiseñosConPaginacion(1, 5, [], 'completos');
    
    if (!empty($disenosCompletos['datos'])) {
        foreach ($disenosCompletos['datos'] as $diseño) {
            echo "- Código: {$diseño['codigoDiseno']} | ";
            echo "Denominación: {$diseño['denominacionDiseno']} | ";
            echo "Línea: " . ($diseño['lineaTecnologica'] ?? 'Sin datos') . " | ";
            echo "Red: " . ($diseño['redTecnologica'] ?? 'Sin datos') . " | ";
            echo "Horas L: " . ($diseño['horasDesarrolloLectiva'] ?? '0') . " | ";
            echo "Horas P: " . ($diseño['horasDesarrolloProductiva'] ?? '0') . "\n";
        }
    } else {
        echo "No se encontraron diseños completos.\n";
    }
    
    echo "\n<h3>2. Diseños con información INCOMPLETA:</h3>\n";
    $disenosIncompletos = $metodosDisenos->obtenerDiseñosConPaginacion(1, 5, [], 'incompletos');
    
    if (!empty($disenosIncompletos['datos'])) {
        foreach ($disenosIncompletos['datos'] as $diseño) {
            echo "- Código: {$diseño['codigoDiseno']} | ";
            echo "Denominación: {$diseño['denominacionDiseno']} | ";
            echo "Línea: " . ($diseño['lineaTecnologica'] ?? 'Sin datos') . " | ";
            echo "Red: " . ($diseño['redTecnologica'] ?? 'Sin datos') . " | ";
            echo "Horas L: " . ($diseño['horasDesarrolloLectiva'] ?? '0') . " | ";
            echo "Horas P: " . ($diseño['horasDesarrolloProductiva'] ?? '0') . "\n";
        }
    } else {
        echo "No se encontraron diseños incompletos.\n";
    }
    
    echo "\n<h3>3. TODOS los diseños (sin filtro):</h3>\n";
    $todosDiseños = $metodosDisenos->obtenerDiseñosConPaginacion(1, 5, []);
    
    if (!empty($todosDiseños['datos'])) {
        foreach ($todosDiseños['datos'] as $diseño) {
            echo "- Código: {$diseño['codigoDiseno']} | ";
            echo "Denominación: {$diseño['denominacionDiseno']} | ";
            echo "Línea: " . ($diseño['lineaTecnologica'] ?? 'Sin datos') . " | ";
            echo "Red: " . ($diseño['redTecnologica'] ?? 'Sin datos') . " | ";
            echo "Horas L: " . ($diseño['horasDesarrolloLectiva'] ?? '0') . " | ";
            echo "Horas P: " . ($diseño['horasDesarrolloProductiva'] ?? '0') . "\n";
        }
    } else {
        echo "No se encontraron diseños.\n";
    }
    
    // Probar verificación individual
    echo "\n<h3>4. Verificación individual de completitud:</h3>\n";
    if (!empty($todosDiseños['datos'])) {
        foreach ($todosDiseños['datos'] as $diseño) {
            $esCompleto = $metodosDisenos->verificarCompletitudDiseño($diseño);
            echo "- {$diseño['codigoDiseno']}: " . ($esCompleto ? 'COMPLETO' : 'INCOMPLETO') . "\n";
        }
    }
    
    echo "\n<strong>Prueba completada exitosamente</strong>\n";
    
} catch (Exception $e) {
    echo "<strong>Error en la prueba:</strong> " . $e->getMessage() . "\n";
    echo "<strong>Traza:</strong>\n" . $e->getTraceAsString() . "\n";
}
?>
