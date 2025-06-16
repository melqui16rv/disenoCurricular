<?php
// Debug para la funcionalidad de comparación de RAPs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Datos de prueba
$codigoCompetencia = '220201501'; // Competencia que existe en múltiples diseños
$disenoActual = '124101-1'; // Diseño a excluir

echo "<h1>Debug de Comparación de RAPs</h1>";
echo "<p><strong>Competencia a buscar:</strong> $codigoCompetencia</p>";
echo "<p><strong>Diseño actual a excluir:</strong> $disenoActual</p>";

try {
    // Intentar conexión
    require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';
    $conexionObj = new Conexion();
    $conexion = $conexionObj->obtenerConexion();
    
    echo "<p style='color: green;'>✓ Conexión a la base de datos exitosa</p>";
    
    // Consulta principal - buscar diseños con la misma competencia
    $sql = "SELECT DISTINCT 
                d.codigoDiseño,
                d.nombrePrograma,
                d.versionPrograma,
                d.codigoPrograma,
                c.codigoDiseñoCompetencia,
                c.nombreCompetencia,
                c.horasDesarrolloCompetencia
            FROM competencias c
            INNER JOIN diseños d ON (
                CONCAT(d.codigoPrograma, '-', d.versionPrograma) = d.codigoDiseño 
                AND d.codigoDiseño = SUBSTRING_INDEX(c.codigoDiseñoCompetencia, '-', 2)
            )
            WHERE c.codigoCompetencia = :codigoCompetencia";
    
    if ($disenoActual && trim($disenoActual) !== '') {
        $sql .= " AND d.codigoDiseño != :disenoActual";
    }
    
    $sql .= " ORDER BY d.nombrePrograma, d.versionPrograma";
    
    echo "<h3>Consulta SQL:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($sql) . "</pre>";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':codigoCompetencia', $codigoCompetencia, PDO::PARAM_STR);
    
    if ($disenoActual && trim($disenoActual) !== '') {
        $stmt->bindParam(':disenoActual', $disenoActual, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $disenosConMismaCompetencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Diseños encontrados con la competencia $codigoCompetencia:</h3>";
    echo "<p><strong>Total:</strong> " . count($disenosConMismaCompetencia) . "</p>";
    
    if (count($disenosConMismaCompetencia) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>Código Diseño</th><th>Nombre Programa</th><th>Versión</th><th>Código Competencia</th>";
        echo "</tr>";
        
        foreach ($disenosConMismaCompetencia as $diseno) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($diseno['codigoDiseño']) . "</td>";
            echo "<td>" . htmlspecialchars($diseno['nombrePrograma']) . "</td>";
            echo "<td>" . htmlspecialchars($diseno['versionPrograma']) . "</td>";
            echo "<td>" . htmlspecialchars($diseno['codigoDiseñoCompetencia']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Ahora buscar RAPs para cada diseño
        echo "<h3>RAPs por cada diseño:</h3>";
        
        foreach ($disenosConMismaCompetencia as $diseno) {
            echo "<h4>Diseño: " . htmlspecialchars($diseno['nombrePrograma']) . " (v" . htmlspecialchars($diseno['versionPrograma']) . ")</h4>";
            
            $sqlRaps = "SELECT 
                            codigoDiseñoCompetenciaRap,
                            codigoRapAutomatico,
                            codigoRapDiseño,
                            nombreRap,
                            horasDesarrolloRap
                        FROM raps 
                        WHERE codigoDiseñoCompetenciaRap LIKE :patronCompetencia
                        ORDER BY codigoRapAutomatico";
            
            $stmtRaps = $conexion->prepare($sqlRaps);
            $patronCompetencia = $diseno['codigoDiseñoCompetencia'] . '-%';
            $stmtRaps->bindParam(':patronCompetencia', $patronCompetencia, PDO::PARAM_STR);
            $stmtRaps->execute();
            
            $raps = $stmtRaps->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p><strong>Patrón de búsqueda:</strong> " . htmlspecialchars($patronCompetencia) . "</p>";
            echo "<p><strong>RAPs encontrados:</strong> " . count($raps) . "</p>";
            
            if (count($raps) > 0) {
                echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
                echo "<tr style='background: #e8f4f8;'>";
                echo "<th>Código RAP Completo</th><th>Código Diseño</th><th>Nombre RAP</th><th>Horas</th>";
                echo "</tr>";
                
                foreach ($raps as $rap) {
                    echo "<tr>";
                    echo "<td><code>" . htmlspecialchars($rap['codigoDiseñoCompetenciaRap']) . "</code></td>";
                    echo "<td>" . htmlspecialchars($rap['codigoRapDiseño']) . "</td>";
                    echo "<td>" . htmlspecialchars(substr($rap['nombreRap'], 0, 80)) . "...</td>";
                    echo "<td>" . htmlspecialchars($rap['horasDesarrolloRap']) . "h</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: #888;'>Sin RAPs para este diseño.</p>";
            }
        }
    } else {
        echo "<p style='color: orange;'>No se encontraron diseños con la competencia especificada.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error PDO: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>Resumen de cambios realizados:</h3>";
echo "<ol>";
echo "<li>✓ Corregida la consulta SQL en ajax.php</li>";
echo "<li>✓ Simplificada la lógica del JOIN entre competencias y diseños</li>";
echo "<li>✓ Mejorado el manejo de errores y debug</li>";
echo "<li>✓ Actualizada la función JavaScript mostrarComparacion() en crear_raps.php</li>";
echo "<li>✓ Actualizada la función JavaScript mostrarComparacion() en editar_raps.php</li>";
echo "<li>✓ Cambiado 'data.comparacion' por 'data.data' para consistencia</li>";
echo "</ol>";

echo "<h3>Instrucciones:</h3>";
echo "<ol>";
echo "<li>Sube este archivo debug_ajax_comparacion.php al directorio raíz de tu hosting</li>";
echo "<li>Visita la URL de este archivo para ver si la consulta funciona</li>";
echo "<li>Si funciona aquí, entonces sube los archivos ajax.php, crear_raps.php y editar_raps.php actualizados</li>";
echo "<li>Prueba la funcionalidad real en el sistema</li>";
echo "</ol>";
?>
