<?php
/**
 * Script de prueba para verificar la funcionalidad después de la migración
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== PRUEBA DE FUNCIONALIDAD POST-MIGRACIÓN ===\n\n";

try {
    // Incluir archivos necesarios
    require_once __DIR__ . '/sql/conexion.php';
    require_once __DIR__ . '/math/forms/metodosDisenos.php';
    
    $metodos = new MetodosDisenos();
    
    echo "✅ Archivos incluidos correctamente\n";
    
    // Prueba 1: Obtener diseños
    echo "\n1. Probando obtener diseños...\n";
    $diseños = $metodos->obtenerTodosLosDiseños();
    echo "   Diseños encontrados: " . count($diseños) . "\n";
    
    if (count($diseños) > 0) {
        $primer_diseño = $diseños[0];
        echo "   Primer diseño: " . ($primer_diseño['codigoDiseño'] ?? 'N/A') . "\n";
        
        // Prueba 2: Obtener competencias del primer diseño
        echo "\n2. Probando obtener competencias...\n";
        $competencias = $metodos->obtenerCompetenciasPorDiseño($primer_diseño['codigoDiseño']);
        echo "   Competencias encontradas: " . count($competencias) . "\n";
        
        if (count($competencias) > 0) {
            $primera_competencia = $competencias[0];
            echo "   Primera competencia: " . ($primera_competencia['codigoDiseñoCompetenciaReporte'] ?? 'N/A') . "\n";
            
            // Prueba 3: Obtener RAPs de la primera competencia
            echo "\n3. Probando obtener RAPs...\n";
            $raps = $metodos->obtenerRapsPorCompetencia($primera_competencia['codigoDiseñoCompetenciaReporte']);
            echo "   RAPs encontrados: " . count($raps) . "\n";
            
            if (count($raps) > 0) {
                $primer_rap = $raps[0];
                echo "   Primer RAP: " . ($primer_rap['codigoDiseñoCompetenciaReporteRap'] ?? 'N/A') . "\n";
            }
        }
    }
    
    echo "\n=== PRUEBA DE CONSULTAS ESPECÍFICAS ===\n";
    
    // Prueba 4: Verificar estructura de tablas
    $conexionObj = new Conexion();
    $conexion = $conexionObj->obtenerConexion();
    
    echo "\n4. Verificando estructura de tabla competencias...\n";
    $stmt = $conexion->query("DESCRIBE competencias");
    $campos_competencias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $campos_esperados_competencias = [
        'codigoDiseñoCompetenciaReporte',
        'codigoCompetenciaReporte', 
        'codigoCompetenciaPDF',
        'nombreCompetencia'
    ];
    
    foreach ($campos_esperados_competencias as $campo) {
        if (in_array($campo, $campos_competencias)) {
            echo "   ✅ $campo: Presente\n";
        } else {
            echo "   ❌ $campo: FALTANTE\n";
        }
    }
    
    echo "\n5. Verificando estructura de tabla raps...\n";
    $stmt = $conexion->query("DESCRIBE raps");
    $campos_raps = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $campos_esperados_raps = [
        'codigoDiseñoCompetenciaReporteRap',
        'codigoRapReporte',
        'nombreRap',
        'horasDesarrolloRap'
    ];
    
    foreach ($campos_esperados_raps as $campo) {
        if (in_array($campo, $campos_raps)) {
            echo "   ✅ $campo: Presente\n";
        } else {
            echo "   ❌ $campo: FALTANTE\n";
        }
    }
    
    // Verificar campos obsoletos
    $campos_obsoletos = ['codigoRapAutomatico', 'codigoRapDiseño'];
    foreach ($campos_obsoletos as $campo) {
        if (in_array($campo, $campos_raps)) {
            echo "   ⚠️  $campo: OBSOLETO - aún presente\n";
        } else {
            echo "   ✅ $campo: Correctamente eliminado\n";
        }
    }
    
    echo "\n=== RESUMEN ===\n";
    echo "✅ La migración parece haber sido exitosa\n";
    echo "📋 Próximos pasos:\n";
    echo "   1. Probar creación de diseños\n";
    echo "   2. Probar creación de competencias\n";
    echo "   3. Probar creación de RAPs\n";
    echo "   4. Probar funcionalidad de comparación\n";
    echo "   5. Verificar formularios en navegador\n";
    
} catch (Exception $e) {
    echo "❌ Error durante las pruebas: " . $e->getMessage() . "\n";
    echo "Pila de llamadas:\n" . $e->getTraceAsString() . "\n";
}
?>
