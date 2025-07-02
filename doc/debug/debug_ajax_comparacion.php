<?php
// Debug para la funcionalidad de comparación de RAPs - VERSIÓN MEJORADA
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug de Comparación de RAPs - Versión Mejorada</h1>";

// Datos de prueba específicos de tu base de datos
$codigoCompetencia = '220201501'; // Esta competencia está en múltiples diseños según tu SQL
$disenoActual = '124101-1'; // Este diseño debe ser excluido

echo "<p><strong>Competencia a buscar:</strong> $codigoCompetencia</p>";
echo "<p><strong>Diseño actual a excluir:</strong> $disenoActual</p>";

try {
    // Intentar conexión (funciona tanto en local como en hosting)
    $conexionPath = __DIR__ . '/sql/conexion.php';
    if (!file_exists($conexionPath)) {
        $conexionPath = $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';
    }
    
    require_once $conexionPath;
    $conexionObj = new Conexion();
    $conexion = $conexionObj->obtenerConexion();
    
    echo "<p style='color: green;'>✓ Conexión a la base de datos exitosa</p>";
    
    // Probar la consulta exacta que usa ajax.php
    $sql = "SELECT DISTINCT 
                d.codigoDiseño,
                d.nombrePrograma,
                d.versionPrograma,
                d.codigoPrograma,
                c.codigoDiseñoCompetenciaReporte,
                c.nombreCompetencia,
                c.horasDesarrolloCompetencia
            FROM competencias c
            INNER JOIN diseños d ON (
                CONCAT(d.codigoPrograma, '-', d.versionPrograma) = d.codigoDiseño 
                AND d.codigoDiseño = SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2)
            )
            WHERE c.codigoCompetencia = :codigoCompetencia";
    
    if ($disenoActual && trim($disenoActual) !== '') {
        $sql .= " AND d.codigoDiseño != :disenoActual";
    }
    
    $sql .= " ORDER BY d.nombrePrograma, d.versionPrograma";
    
    echo "<h3>Consulta SQL usada en ajax.php:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($sql) . "</pre>";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':codigoCompetencia', $codigoCompetencia, PDO::PARAM_STR);
    
    if ($disenoActual && trim($disenoActual) !== '') {
        $stmt->bindParam(':disenoActual', $disenoActual, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $disenosConMismaCompetencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Diseños encontrados con la competencia $codigoCompetencia:</h3>";
    echo "<p><strong>Total encontrados:</strong> " . count($disenosConMismaCompetencia) . "</p>";
    
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
            echo "<td>" . htmlspecialchars($diseno['codigoDiseñoCompetenciaReporte']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Ahora probar la búsqueda de RAPs
        echo "<h3>Probando búsqueda de RAPs para cada diseño:</h3>";
        
        $comparacion = [];
        
        foreach ($disenosConMismaCompetencia as $diseno) {
            echo "<h4>Diseño: " . htmlspecialchars($diseno['nombrePrograma']) . " (v" . htmlspecialchars($diseno['versionPrograma']) . ")</h4>";
            
            $sqlRaps = "SELECT 
                            codigoDiseñoCompetenciaReporteRap,
                            codigoRapAutomatico,
                            codigoRapDiseño,
                            nombreRap,
                            horasDesarrolloRap
                        FROM raps 
                        WHERE codigoDiseñoCompetenciaReporteRap LIKE :patronCompetencia
                        ORDER BY codigoRapAutomatico";
            
            $stmtRaps = $conexion->prepare($sqlRaps);
            $patronCompetencia = $diseno['codigoDiseñoCompetenciaReporte'] . '-%';
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
                    echo "<td><code>" . htmlspecialchars($rap['codigoDiseñoCompetenciaReporteRap']) . "</code></td>";
                    echo "<td>" . htmlspecialchars($rap['codigoRapDiseño']) . "</td>";
                    echo "<td>" . htmlspecialchars(substr($rap['nombreRap'], 0, 80)) . "...</td>";
                    echo "<td>" . htmlspecialchars($rap['horasDesarrolloRap']) . "h</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: #888;'>Sin RAPs para este diseño.</p>";
            }
            
            // Agregar a la comparación tal como lo hace ajax.php
            $comparacion[] = [
                'diseno' => $diseno,
                'raps' => $raps,
                'totalRaps' => count($raps),
                'totalHorasRaps' => array_sum(array_column($raps, 'horasDesarrolloRap'))
            ];
        }
        
        // Simular la respuesta de ajax.php
        echo "<hr>";
        echo "<h3>Simulación de respuesta AJAX (lo que debería recibir JavaScript):</h3>";
        
        $response = [
            'success' => true,
            'data' => $comparacion,
            'message' => 'Comparación obtenida exitosamente',
            'totalDisenos' => count($comparacion),
            'debug' => [
                'codigoCompetencia' => $codigoCompetencia,
                'disenoActual' => $disenoActual,
                'totalDisenosEncontrados' => count($disenosConMismaCompetencia),
                'totalComparaciones' => count($comparacion),
                'sqlUsado' => $sql
            ]
        ];
        
        echo "<pre style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>";
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
        
    } else {
        echo "<p style='color: orange;'>❌ No se encontraron diseños con la competencia especificada.</p>";
        echo "<p><strong>Posibles causas:</strong></p>";
        echo "<ul>";
        echo "<li>La competencia '$codigoCompetencia' no existe en la tabla competencias</li>";
        echo "<li>Problema con el JOIN entre las tablas competencias y diseños</li>";
        echo "<li>El diseño actual '$disenoActual' es el único que tiene esa competencia</li>";
        echo "</ul>";
        
        // Hacer consultas de verificación
        echo "<h4>Verificaciones:</h4>";
        
        // 1. ¿Existe la competencia?
        $checkComp = $conexion->prepare("SELECT COUNT(*) as total FROM competencias WHERE codigoCompetencia = ?");
        $checkComp->execute([$codigoCompetencia]);
        $totalComp = $checkComp->fetch()['total'];
        echo "<p>1. Competencias con código '$codigoCompetencia': <strong>$totalComp</strong></p>";
        
        // 2. ¿Existen diseños?
        $checkDisenos = $conexion->prepare("SELECT COUNT(*) as total FROM diseños");
        $checkDisenos->execute();
        $totalDisenos = $checkDisenos->fetch()['total'];
        echo "<p>2. Total de diseños en la base: <strong>$totalDisenos</strong></p>";
        
        // 3. ¿Qué competencias existen?
        $listComp = $conexion->prepare("SELECT DISTINCT codigoCompetencia FROM competencias LIMIT 5");
        $listComp->execute();
        $competencias = $listComp->fetchAll();
        echo "<p>3. Algunas competencias disponibles: ";
        foreach ($competencias as $comp) {
            echo "<code>" . $comp['codigoCompetencia'] . "</code> ";
        }
        echo "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error PDO: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>Instrucciones para resolver el problema:</h3>";
echo "<ol>";
echo "<li><strong>Subir este archivo</strong> al directorio raíz de tu hosting</li>";
echo "<li><strong>Ejecutarlo</strong> en el navegador: tu-dominio.com/debug_ajax_comparacion.php</li>";
echo "<li><strong>Si NO aparecen diseños:</strong>";
echo "<ul>";
echo "<li>Verificar que la tabla 'diseños' tenga el acento (no 'disenos')</li>";
echo "<li>Verificar que los datos existen en la base de datos</li>";
echo "<li>Probar con otra competencia de la lista mostrada</li>";
echo "</ul>";
echo "</li>";
echo "<li><strong>Si aparecen diseños pero NO RAPs:</strong>";
echo "<ul>";
echo "<li>Verificar que existen RAPs en la tabla 'raps'</li>";
echo "<li>Verificar el formato del código: debe ser 'diseño-competencia-numeroRap'</li>";
echo "</ul>";
echo "</li>";
echo "<li><strong>Si todo funciona aquí pero no en la aplicación:</strong>";
echo "<ul>";
echo "<li>El problema está en la llamada JavaScript o en ajax.php</li>";
echo "<li>Verificar la consola del navegador para errores JavaScript</li>";
echo "<li>Verificar que ajax.php devuelve el JSON correctamente</li>";
echo "</ul>";
echo "</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Estado del debug:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
