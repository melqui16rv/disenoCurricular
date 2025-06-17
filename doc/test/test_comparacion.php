<?php
// Test de la funcionalidad de comparación
require_once 'math/forms/metodosComparacion.php';

// Habilitar errores para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Comparación de RAPs</h1>";

try {
    $comparacion = new comparacion();
    
    // Test 1: Verificar conexión
    echo "<h2>Test 1: Verificar conexión</h2>";
    echo "Conexión establecida correctamente<br>";
    
    // Test 2: Buscar diseños con competencia específica
    echo "<h2>Test 2: Buscar diseños con misma competencia</h2>";
    $codigoCompetencia = '220201501'; // Usar uno de los códigos de la exportación
    $disenos = $comparacion->obtenerDisenosConMismaCompetencia($codigoCompetencia);
    
    echo "Código de competencia: $codigoCompetencia<br>";
    echo "Diseños encontrados: " . count($disenos) . "<br>";
    
    if (!empty($disenos)) {
        echo "<table border='1'>";
        echo "<tr><th>Código Diseño</th><th>Nombre Programa</th><th>Versión</th><th>Código Diseño Competencia</th></tr>";
        foreach ($disenos as $diseno) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($diseno['codigoDiseño']) . "</td>";
            echo "<td>" . htmlspecialchars($diseno['nombrePrograma']) . "</td>";
            echo "<td>" . htmlspecialchars($diseno['versionPrograma']) . "</td>";
            echo "<td>" . htmlspecialchars($diseno['codigoDiseñoCompetencia']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron diseños<br>";
    }
    
    // Test 3: Buscar RAPs de una competencia específica
    echo "<h2>Test 3: Buscar RAPs</h2>";
    if (!empty($disenos)) {
        $primeraCompetencia = $disenos[0]['codigoDiseñoCompetencia'];
        echo "Buscando RAPs para: $primeraCompetencia<br>";
        
        $raps = $comparacion->obtenerRapsPorCompetenciaDiseno($primeraCompetencia);
        echo "RAPs encontrados: " . count($raps) . "<br>";
        
        if (!empty($raps)) {
            echo "<table border='1'>";
            echo "<tr><th>Código RAP</th><th>Nombre RAP</th><th>Horas</th></tr>";
            foreach ($raps as $rap) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($rap['codigoRapDiseño'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($rap['nombreRap'] ?? 'Sin nombre') . "</td>";
                echo "<td>" . htmlspecialchars($rap['horasDesarrolloRap'] ?? '0') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // Test 4: Comparación completa
    echo "<h2>Test 4: Comparación completa</h2>";
    $comparacionCompleta = $comparacion->obtenerComparacionRaps($codigoCompetencia);
    echo "Grupos de comparación: " . count($comparacionCompleta) . "<br>";
    
    if (!empty($comparacionCompleta)) {
        echo "<h3>Datos de comparación en formato JSON:</h3>";
        echo "<pre>" . json_encode($comparacionCompleta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
