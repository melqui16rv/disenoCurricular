<?php
/**
 * Script para verificar la estructura de la base de datos
 * y corregir los filtros según las columnas reales
 */

require_once 'sql/conexion.php';

try {
    $conexion = new Conexion();
    $pdo = $conexion->obtenerConexion();
    
    echo "<h2>Verificación de Estructura de Base de Datos</h2>";
    
    // Verificar estructura de la tabla diseños
    echo "<h3>📋 Tabla: diseños</h3>";
    $stmt = $pdo->prepare("DESCRIBE diseños");
    $stmt->execute();
    $columnas_disenos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columnas_disenos as $columna) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($columna['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($columna['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar algunos datos de ejemplo
    echo "<h4>🔍 Datos de ejemplo (primeros 3 registros):</h4>";
    $stmt = $pdo->prepare("SELECT * FROM diseños LIMIT 3");
    $stmt->execute();
    $ejemplos_disenos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($ejemplos_disenos) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; font-size: 12px;'>";
        echo "<tr>";
        foreach (array_keys($ejemplos_disenos[0]) as $campo) {
            echo "<th>" . htmlspecialchars($campo) . "</th>";
        }
        echo "</tr>";
        
        foreach ($ejemplos_disenos as $fila) {
            echo "<tr>";
            foreach ($fila as $valor) {
                echo "<td>" . htmlspecialchars(substr($valor ?? '', 0, 50)) . (strlen($valor ?? '') > 50 ? '...' : '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar estructura de la tabla competencias
    echo "<h3>🎯 Tabla: competencias</h3>";
    $stmt = $pdo->prepare("DESCRIBE competencias");
    $stmt->execute();
    $columnas_competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columnas_competencias as $columna) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($columna['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($columna['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar estructura de la tabla raps
    echo "<h3>🎯 Tabla: raps</h3>";
    $stmt = $pdo->prepare("DESCRIBE raps");
    $stmt->execute();
    $columnas_raps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columnas_raps as $columna) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($columna['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($columna['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($columna['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar valores únicos para filtros
    echo "<h3>📊 Análisis de Datos para Filtros</h3>";
    
    // Campos candidatos para filtros en diseños
    $campos_texto = [];
    $campos_numericos = [];
    
    foreach ($columnas_disenos as $columna) {
        $campo = $columna['Field'];
        $tipo = strtolower($columna['Type']);
        
        if (strpos($tipo, 'varchar') !== false || strpos($tipo, 'text') !== false) {
            $campos_texto[] = $campo;
        } elseif (strpos($tipo, 'int') !== false || strpos($tipo, 'decimal') !== false || strpos($tipo, 'float') !== false) {
            $campos_numericos[] = $campo;
        }
    }
    
    echo "<h4>📝 Campos de texto (candidatos para filtros SELECT):</h4>";
    foreach ($campos_texto as $campo) {
        if (strlen($campo) < 50) { // Solo campos con nombres razonables
            echo "<h5>Campo: $campo</h5>";
            $stmt = $pdo->prepare("SELECT DISTINCT $campo FROM diseños WHERE $campo IS NOT NULL AND $campo != '' LIMIT 10");
            $stmt->execute();
            $valores = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if ($valores) {
                echo "<ul>";
                foreach ($valores as $valor) {
                    if (strlen($valor) < 100) { // Solo valores razonables
                        echo "<li>" . htmlspecialchars($valor) . "</li>";
                    }
                }
                echo "</ul>";
            } else {
                echo "<p><em>Sin valores únicos encontrados</em></p>";
            }
        }
    }
    
    echo "<h4>🔢 Campos numéricos (candidatos para filtros de rango):</h4>";
    foreach ($campos_numericos as $campo) {
        echo "<h5>Campo: $campo</h5>";
        $stmt = $pdo->prepare("SELECT MIN($campo) as minimo, MAX($campo) as maximo, AVG($campo) as promedio FROM diseños WHERE $campo IS NOT NULL");
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stats) {
            echo "<ul>";
            echo "<li>Mínimo: " . ($stats['minimo'] ?? 'N/A') . "</li>";
            echo "<li>Máximo: " . ($stats['maximo'] ?? 'N/A') . "</li>";
            echo "<li>Promedio: " . number_format($stats['promedio'] ?? 0, 2) . "</li>";
            echo "</ul>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px; border: 1px solid red; margin: 10px;'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f5f5f5;
}

h2, h3, h4, h5 {
    color: #2c3e50;
}

table {
    background: white;
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
}

th {
    background-color: #3498db;
    color: white;
    padding: 10px;
    text-align: left;
}

td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

tr:nth-child(even) {
    background-color: #f8f9fa;
}

ul {
    background: white;
    padding: 15px;
    border-left: 4px solid #3498db;
    margin: 10px 0;
}

li {
    margin: 5px 0;
}
</style>
