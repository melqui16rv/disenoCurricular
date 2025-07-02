<?php
// Debug para verificar la funcionalidad de comparación de RAPs
require_once __DIR__ . '/../conf/config.php';
require_once __DIR__ . '/../math/forms/metodosComparacion.php';

echo "<h1>Debug de Comparación de RAPs</h1>";

$comparacion = new comparacion();

// Test 1: Verificar conexión
echo "<h2>1. Test de Conexión</h2>";
try {
    $testConexion = $comparacion->extraerCodigoCompetencia('124101-1-220201501-1');
    echo "✅ Conexión exitosa<br>";
    echo "Código extraído: " . $testConexion . "<br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

// Test 2: Verificar datos en la base
echo "<h2>2. Verificar Datos en Base</h2>";
try {
    $conexion = new Conexion();
    $conn = $conexion->obtenerConexion();
    
    // Contar diseños
    $stmt = $conn->query("SELECT COUNT(*) as total FROM diseños");
    $totalDiseños = $stmt->fetch()['total'];
    echo "Total diseños: " . $totalDiseños . "<br>";
    
    // Contar competencias
    $stmt = $conn->query("SELECT COUNT(*) as total FROM competencias");
    $totalCompetencias = $stmt->fetch()['total'];
    echo "Total competencias: " . $totalCompetencias . "<br>";
    
    // Contar RAPs
    $stmt = $conn->query("SELECT COUNT(*) as total FROM raps");
    $totalRaps = $stmt->fetch()['total'];
    echo "Total RAPs: " . $totalRaps . "<br>";
    
    // Mostrar competencias comunes
    echo "<h3>Competencias por código:</h3>";
    $stmt = $conn->query("SELECT codigoCompetencia, COUNT(*) as cantidad FROM competencias GROUP BY codigoCompetencia ORDER BY cantidad DESC");
    while ($row = $stmt->fetch()) {
        echo "- Competencia " . $row['codigoCompetencia'] . ": " . $row['cantidad'] . " diseños<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error consultando datos: " . $e->getMessage() . "<br>";
}

// Test 3: Probar extracción de códigos
echo "<h2>3. Test Extracción de Códigos</h2>";
$ejemploCodigo = '124101-1-220201501-1';
echo "Código ejemplo: " . $ejemploCodigo . "<br>";
echo "Código competencia extraído: " . $comparacion->extraerCodigoCompetencia($ejemploCodigo) . "<br>";
echo "Código diseño extraído: " . $comparacion->extraerCodigoDiseno($ejemploCodigo) . "<br>";

// Test 4: Probar consulta de diseños con misma competencia
echo "<h2>4. Test Consulta Diseños con Misma Competencia</h2>";
$codigoCompetencia = '220201501';
$disenoActual = '124101-1';
echo "Buscando diseños con competencia: " . $codigoCompetencia . "<br>";
echo "Excluyendo diseño: " . $disenoActual . "<br>";

$disenosConMismaCompetencia = $comparacion->obtenerDisenosConMismaCompetencia($codigoCompetencia, $disenoActual);
echo "Diseños encontrados: " . count($disenosConMismaCompetencia) . "<br>";

if (count($disenosConMismaCompetencia) > 0) {
    echo "<ul>";
    foreach ($disenosConMismaCompetencia as $diseno) {
        echo "<li>" . $diseno['codigoDiseño'] . " - " . $diseno['nombrePrograma'] . " (v" . $diseno['versionPrograma'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "⚠️ No se encontraron diseños con la misma competencia<br>";
}

// Test 5: Probar consulta de RAPs por competencia diseño
echo "<h2>5. Test Consulta RAPs por Competencia-Diseño</h2>";
if (count($disenosConMismaCompetencia) > 0) {
    $primerDiseno = $disenosConMismaCompetencia[0];
    echo "Consultando RAPs para: " . $primerDiseno['codigoDiseñoCompetenciaReporte'] . "<br>";
    
    $raps = $comparacion->obtenerRapsPorCompetenciaDiseno($primerDiseno['codigoDiseñoCompetenciaReporte']);
    echo "RAPs encontrados: " . count($raps) . "<br>";
    
    if (count($raps) > 0) {
        echo "<ul>";
        foreach ($raps as $rap) {
            echo "<li>" . $rap['codigoRapDiseño'] . " - " . substr($rap['nombreRap'], 0, 50) . "... (" . $rap['horasDesarrolloRap'] . "h)</li>";
        }
        echo "</ul>";
    }
}

// Test 6: Probar comparación completa
echo "<h2>6. Test Comparación Completa</h2>";
$comparacionCompleta = $comparacion->obtenerComparacionRaps($codigoCompetencia, $disenoActual);
echo "Elementos en comparación: " . count($comparacionCompleta) . "<br>";

if (count($comparacionCompleta) > 0) {
    foreach ($comparacionCompleta as $item) {
        echo "<h4>" . $item['diseno']['nombrePrograma'] . " (v" . $item['diseno']['versionPrograma'] . ")</h4>";
        echo "RAPs: " . count($item['raps']) . "<br>";
        foreach ($item['raps'] as $rap) {
            echo "- " . $rap['codigoRapDiseño'] . ": " . substr($rap['nombreRap'], 0, 40) . "...<br>";
        }
    }
} else {
    echo "⚠️ No se encontraron datos para la comparación<br>";
}

// Test 7: Verificar consulta SQL directa
echo "<h2>7. Test Consulta SQL Directa</h2>";
try {
    $conexion = new Conexion();
    $conn = $conexion->obtenerConexion();
    
    $sql = "SELECT DISTINCT 
                d.codigoDiseño,
                d.nombrePrograma,
                d.versionPrograma,
                c.codigoDiseñoCompetenciaReporte,
                SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2) as extracted_code
            FROM competencias c
            INNER JOIN diseños d ON SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2) = d.codigoDiseño
            WHERE c.codigoCompetencia = '220201501'
            AND d.codigoDiseño != '124101-1'";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    echo "Resultados de consulta directa: " . count($results) . "<br>";
    if (count($results) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Código Diseño</th><th>Nombre Programa</th><th>Versión</th><th>Código Competencia</th><th>Extraído</th></tr>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . $row['codigoDiseño'] . "</td>";
            echo "<td>" . $row['nombrePrograma'] . "</td>";
            echo "<td>" . $row['versionPrograma'] . "</td>";
            echo "<td>" . $row['codigoDiseñoCompetenciaReporte'] . "</td>";
            echo "<td>" . $row['extracted_code'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "❌ Error en consulta directa: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Debug completado</strong>";
?>
