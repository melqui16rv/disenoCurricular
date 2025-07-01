<?php
// Script to test the application with the new database schema
// This will help identify specific errors and validate fixes

require_once 'sql/conexion.php';
require_once 'math/forms/metodosDisenos.php';

echo "<h1>Testing Application with New Database Schema</h1>\n";

try {
    $metodos = new MetodosDisenos();
    echo "<p>✅ Database connection successful</p>\n";
    
    // Test 1: Try to get all diseños (should work - table structure unchanged)
    echo "<h2>Test 1: Getting all diseños</h2>\n";
    try {
        $disenos = $metodos->obtenerTodosLosDiseños();
        echo "<p>✅ obtenerTodosLosDiseños() works - Found " . count($disenos) . " diseños</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ obtenerTodosLosDiseños() failed: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 2: Try to get competencias (should fail - column names changed)
    echo "<h2>Test 2: Getting competencias for a diseño</h2>\n";
    try {
        if (!empty($disenos)) {
            $codigoDiseño = $disenos[0]['codigoDiseño'];
            $competencias = $metodos->obtenerCompetenciasPorDiseño($codigoDiseño);
            echo "<p>✅ obtenerCompetenciasPorDiseño() works - Found " . count($competencias) . " competencias</p>\n";
        } else {
            echo "<p>⚠️ No diseños found to test competencias</p>\n";
        }
    } catch (Exception $e) {
        echo "<p>❌ obtenerCompetenciasPorDiseño() failed: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 3: Try to get RAPs (should fail - column names changed)
    echo "<h2>Test 3: Getting RAPs for a competencia</h2>\n";
    try {
        // Try with a sample competencia code
        $raps = $metodos->obtenerRapsPorCompetencia('sample-competencia-code');
        echo "<p>✅ obtenerRapsPorCompetencia() works - Found " . count($raps) . " RAPs</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ obtenerRapsPorCompetencia() failed: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 4: Check table structure
    echo "<h2>Test 4: Checking table structures</h2>\n";
    try {
        $conexion = $metodos->obtenerConexion();
        
        // Check competencias table structure
        $stmt = $conexion->prepare("DESCRIBE competencias");
        $stmt->execute();
        $competenciasColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Competencias table columns:</h3>\n<ul>\n";
        foreach ($competenciasColumns as $column) {
            echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>\n";
        }
        echo "</ul>\n";
        
        // Check raps table structure
        $stmt = $conexion->prepare("DESCRIBE raps");
        $stmt->execute();
        $rapsColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>RAPs table columns:</h3>\n<ul>\n";
        foreach ($rapsColumns as $column) {
            echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>\n";
        }
        echo "</ul>\n";
        
    } catch (Exception $e) {
        echo "<p>❌ Table structure check failed: " . $e->getMessage() . "</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>\n";
}

echo "<h2>Summary</h2>\n";
echo "<p>This script tests the current application code against the new database schema.</p>\n";
echo "<p>Expected results:</p>\n";
echo "<ul>\n";
echo "<li>✅ Database connection should work</li>\n";
echo "<li>✅ Getting diseños should work (table unchanged)</li>\n";
echo "<li>❌ Getting competencias should fail (column names changed)</li>\n";
echo "<li>❌ Getting RAPs should fail (column names changed)</li>\n";
echo "<li>ℹ️ Table structures should show new column names</li>\n";
echo "</ul>\n";
?>