<?php
// Archivo de debug para probar la funcionalidad de comparación de RAPs
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'sql/conexion.php';

echo "<h1>Debug - Comparación de RAPs</h1>";

try {
    $conexionObj = new Conexion();
    $conexion = $conexionObj->obtenerConexion();
    
    echo "<h2>1. Verificar estructura de las tablas</h2>";
    
    // Verificar tabla diseños
    $stmt = $conexion->query("DESCRIBE diseños");
    echo "<h3>Tabla diseños:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    // Verificar tabla competencias
    $stmt = $conexion->query("DESCRIBE competencias");
    echo "<h3>Tabla competencias:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    // Verificar tabla raps
    $stmt = $conexion->query("DESCRIBE raps");
    echo "<h3>Tabla raps:</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    echo "<h2>2. Probar consulta de competencias</h2>";
    $codigoCompetencia = '220201501'; // Usar un código existente
    
    $sql = "SELECT DISTINCT 
                d.codigoDiseño,
                d.nombrePrograma,
                d.versionPrograma,
                c.codigoDiseñoCompetenciaReporte
            FROM competencias c
            INNER JOIN diseños d ON SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2) = d.codigoDiseño
            WHERE c.codigoCompetencia = :codigoCompetencia
            ORDER BY d.nombrePrograma, d.versionPrograma";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':codigoCompetencia', $codigoCompetencia, PDO::PARAM_STR);
    $stmt->execute();
    
    $disenosConMismaCompetencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Diseños con competencia $codigoCompetencia:</h3>";
    echo "<pre>";
    print_r($disenosConMismaCompetencia);
    echo "</pre>";
    
    echo "<h2>3. Probar consulta de RAPs</h2>";
    
    foreach ($disenosConMismaCompetencia as $diseno) {
        echo "<h3>RAPs para diseño: " . $diseno['nombrePrograma'] . "</h3>";
        
        $sqlRaps = "SELECT 
                        codigoDiseñoCompetenciaReporteRap,
                        codigoRapDiseño,
                        nombreRap,
                        horasDesarrolloRap
                    FROM raps 
                    WHERE codigoDiseñoCompetenciaReporteRap LIKE :codigoDisenoCompetencia
                    ORDER BY codigoRapAutomatico";
        
        $stmtRaps = $conexion->prepare($sqlRaps);
        $patron = $diseno['codigoDiseñoCompetenciaReporte'] . '-%';
        $stmtRaps->bindParam(':codigoDisenoCompetencia', $patron, PDO::PARAM_STR);
        $stmtRaps->execute();
        
        $raps = $stmtRaps->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Patrón de búsqueda: $patron</p>";
        echo "<pre>";
        print_r($raps);
        echo "</pre>";
    }
    
    echo "<h2>4. Simulación completa del AJAX</h2>";
    
    // Simular el proceso completo
    $comparacion = [];
    
    foreach ($disenosConMismaCompetencia as $diseno) {
        $sqlRaps = "SELECT 
                        codigoDiseñoCompetenciaReporteRap,
                        codigoRapDiseño,
                        nombreRap,
                        horasDesarrolloRap
                    FROM raps 
                    WHERE codigoDiseñoCompetenciaReporteRap LIKE :codigoDisenoCompetencia
                    ORDER BY codigoRapAutomatico";
        
        $stmtRaps = $conexion->prepare($sqlRaps);
        $patron = $diseno['codigoDiseñoCompetenciaReporte'] . '-%';
        $stmtRaps->bindParam(':codigoDisenoCompetencia', $patron, PDO::PARAM_STR);
        $stmtRaps->execute();
        
        $raps = $stmtRaps->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($raps)) {
            $comparacion[] = [
                'diseno' => $diseno,
                'raps' => $raps
            ];
        }
    }
    
    echo "<h3>Resultado final de comparación:</h3>";
    echo "<pre>";
    print_r($comparacion);
    echo "</pre>";
    
    echo "<h3>JSON que se enviaría:</h3>";
    echo "<pre>";
    echo json_encode([
        'success' => true,
        'comparacion' => $comparacion,
        'message' => 'Comparación obtenida exitosamente',
        'debug' => [
            'codigoCompetencia' => $codigoCompetencia,
            'totalDisenos' => count($disenosConMismaCompetencia),
            'totalComparaciones' => count($comparacion)
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
