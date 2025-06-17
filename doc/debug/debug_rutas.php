<?php
echo "<h1>🔍 Debug de Rutas - Sistema de Diseños Curriculares</h1>";
echo "<hr>";

echo "<h2>📂 Variables del Servidor</h2>";
echo "<strong>DOCUMENT_ROOT original:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NO DEFINIDO') . "<br>";
echo "<strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'NO DEFINIDO') . "<br>";
echo "<strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NO DEFINIDO') . "<br>";
echo "<strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'NO DEFINIDO') . "<br>";
echo "<strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'NO DEFINIDO') . "<br>";

echo "<h2>📍 Rutas PHP</h2>";
echo "<strong>__FILE__:</strong> " . __FILE__ . "<br>";
echo "<strong>__DIR__:</strong> " . __DIR__ . "<br>";
echo "<strong>dirname(__DIR__):</strong> " . dirname(__DIR__) . "<br>";
echo "<strong>dirname(dirname(__DIR__)):</strong> " . dirname(dirname(__DIR__)) . "<br>";

echo "<h2>🔧 Rutas Calculadas</h2>";
$projectRoot = dirname(__DIR__);
echo "<strong>Project Root (dirname(__DIR__)):</strong> " . $projectRoot . "<br>";

// Simular las rutas que intentaría cargar index.php
$configPath = $projectRoot . '/conf/config.php';
$metodosPath = $projectRoot . '/math/forms/metodosDiseños.php';

echo "<strong>Ruta config.php:</strong> " . $configPath . "<br>";
echo "<strong>Existe config.php:</strong> " . (file_exists($configPath) ? '✅ SÍ' : '❌ NO') . "<br>";

echo "<strong>Ruta metodosDiseños.php:</strong> " . $metodosPath . "<br>";
echo "<strong>Existe metodosDiseños.php:</strong> " . (file_exists($metodosPath) ? '✅ SÍ' : '❌ NO') . "<br>";

echo "<h2>⚙️ Configuración Actual</h2>";
// Cargar config para ver qué hace
echo "<p>Cargando config.php...</p>";
if (file_exists($configPath)) {
    require_once $configPath;
    echo "<strong>DOCUMENT_ROOT después de config:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NO DEFINIDO') . "<br>";
    echo "<strong>BASE_URL definida:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NO DEFINIDA') . "<br>";
} else {
    echo "❌ No se pudo cargar config.php<br>";
}

echo "<h2>🎯 Rutas que usaría index.php original</h2>";
$oldConfigPath = $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
$oldMetodosPath = $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosDiseños.php';

echo "<strong>Ruta config (método original):</strong> " . $oldConfigPath . "<br>";
echo "<strong>Existe (método original):</strong> " . (file_exists($oldConfigPath) ? '✅ SÍ' : '❌ NO') . "<br>";

echo "<strong>Ruta métodos (método original):</strong> " . $oldMetodosPath . "<br>";
echo "<strong>Existe (método original):</strong> " . (file_exists($oldMetodosPath) ? '✅ SÍ' : '❌ NO') . "<br>";

echo "<h2>📋 Lista de Archivos en Directorios Clave</h2>";

// Listar archivos en conf/
$confDir = $projectRoot . '/conf';
echo "<strong>Contenido de /conf/:</strong><br>";
if (is_dir($confDir)) {
    $files = scandir($confDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "&nbsp;&nbsp;• " . $file . "<br>";
        }
    }
} else {
    echo "&nbsp;&nbsp;❌ Directorio no existe<br>";
}

// Listar archivos en math/forms/
$mathFormsDir = $projectRoot . '/math/forms';
echo "<strong>Contenido de /math/forms/:</strong><br>";
if (is_dir($mathFormsDir)) {
    $files = scandir($mathFormsDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "&nbsp;&nbsp;• " . $file . "<br>";
        }
    }
} else {
    echo "&nbsp;&nbsp;❌ Directorio no existe<br>";
}

echo "<hr>";
echo "<p><em>Debug completado: " . date('Y-m-d H:i:s') . "</em></p>";
?>

<style>
body { 
    font-family: 'Courier New', monospace; 
    max-width: 1000px; 
    margin: 0 auto; 
    padding: 20px; 
    background: #f8f9fa; 
}
h1, h2 { 
    color: #2c3e50; 
    border-bottom: 2px solid #3498db; 
    padding-bottom: 5px; 
}
strong { 
    color: #e74c3c; 
}
</style>
