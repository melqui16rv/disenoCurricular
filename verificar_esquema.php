<?php
// Script para verificar el esquema real de la base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== VERIFICACIÓN DEL ESQUEMA DE BASE DE DATOS ===\n";

try {
    require_once __DIR__ . '/sql/conexion.php';
    
    $conexionObj = new Conexion();
    $conexion = $conexionObj->obtenerConexion();
    
    echo "✓ Conexión establecida\n\n";
    
    // Verificar estructura de tabla diseños
    echo "=== ESTRUCTURA TABLA 'diseños' ===\n";
    $stmt = $conexion->query("DESCRIBE diseños");
    $campos_disenos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($campos_disenos as $campo) {
        echo "  {$campo['Field']} - {$campo['Type']} - {$campo['Null']} - {$campo['Key']}\n";
    }
    
    // Verificar estructura de tabla competencias
    echo "\n=== ESTRUCTURA TABLA 'competencias' ===\n";
    $stmt = $conexion->query("DESCRIBE competencias");
    $campos_competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($campos_competencias as $campo) {
        echo "  {$campo['Field']} - {$campo['Type']} - {$campo['Null']} - {$campo['Key']}\n";
    }
    
    // Verificar estructura de tabla raps
    echo "\n=== ESTRUCTURA TABLA 'raps' ===\n";
    $stmt = $conexion->query("DESCRIBE raps");
    $campos_raps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($campos_raps as $campo) {
        echo "  {$campo['Field']} - {$campo['Type']} - {$campo['Null']} - {$campo['Key']}\n";
    }
    
    // Mostrar algunos datos de ejemplo
    echo "\n=== DATOS DE EJEMPLO ===\n";
    
    // Competencias
    $stmt = $conexion->query("SELECT * FROM competencias LIMIT 2");
    $competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($competencias)) {
        echo "\nEjemplo competencia:\n";
        foreach ($competencias[0] as $campo => $valor) {
            echo "  $campo: $valor\n";
        }
    }
    
    // RAPs
    $stmt = $conexion->query("SELECT * FROM raps LIMIT 2");
    $raps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($raps)) {
        echo "\nEjemplo RAP:\n";
        foreach ($raps[0] as $campo => $valor) {
            echo "  $campo: $valor\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VERIFICACIÓN ===\n";
?>
