<?php
// Test rápido para verificar que la aplicación funciona
echo "=== TEST DE FUNCIONALIDAD LOCAL ===\n\n";

echo "1. Probando includes...\n";
try {
    require_once __DIR__ . '/conf/config.php';
    echo "   ✅ Config cargado\n";
    
    require_once __DIR__ . '/math/forms/metodosDisenos.php';
    echo "   ✅ Métodos cargados\n";
    
    $metodos = new MetodosDisenos();
    echo "   ✅ Clase instanciada\n";
    
    $diseños = $metodos->obtenerTodosLosDiseños();
    echo "   ✅ Diseños obtenidos: " . count($diseños) . "\n";
    
    echo "\n2. Variables de entorno:\n";
    echo "   DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "   BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'No definida') . "\n";
    
    echo "\n3. Primera competencia de prueba:\n";
    if (count($diseños) > 0) {
        $primer_diseño = $diseños[0];
        $competencias = $metodos->obtenerCompetenciasPorDiseño($primer_diseño['codigoDiseño']);
        echo "   Diseño: " . $primer_diseño['codigoDiseño'] . "\n";
        echo "   Competencias: " . count($competencias) . "\n";
        
        if (count($competencias) > 0) {
            $primera_competencia = $competencias[0];
            echo "   Primera competencia: " . $primera_competencia['codigoDiseñoCompetenciaReporte'] . "\n";
        }
    }
    
    echo "\n✅ TODO FUNCIONANDO CORRECTAMENTE\n";
    echo "🚀 La aplicación está lista para usar en: http://localhost:8888/app/forms/\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Pila: " . $e->getTraceAsString() . "\n";
}
?>
