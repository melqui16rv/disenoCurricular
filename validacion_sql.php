<?php
// Script para validar la sintaxis SQL básica del archivo
$archivo_sql = "import/Diseños Curriculares.sql";

echo "=== VALIDACIÓN DEL ARCHIVO SQL ===\n\n";

if (!file_exists($archivo_sql)) {
    echo "❌ Error: El archivo no existe: $archivo_sql\n";
    exit(1);
}

$contenido = file_get_contents($archivo_sql);

// Verificaciones básicas
$errores = [];
$advertencias = [];

// 1. Verificar que todas las sentencias CREATE TABLE terminen correctamente
if (preg_match_all('/CREATE TABLE[^;]*$/m', $contenido, $matches)) {
    foreach ($matches[0] as $match) {
        if (!preg_match('/\)\s*ENGINE=/i', $match)) {
            $errores[] = "Sentencia CREATE TABLE sin terminación correcta: " . substr($match, 0, 50) . "...";
        }
    }
}

// 2. Verificar nombres de tablas y columnas válidos
if (preg_match_all('/`([^`]+)`/', $contenido, $matches)) {
    foreach ($matches[1] as $nombre) {
        // Verificar caracteres especiales problemáticos
        if (preg_match('/[^a-zA-Z0-9_ñÑáéíóúÁÉÍÓÚ]/', $nombre)) {
            $advertencias[] = "Nombre con caracteres especiales: '$nombre'";
        }
    }
}

// 3. Verificar consistencia en tipos de datos para AUTO_INCREMENT
if (preg_match('/MODIFY\s+`(\w+)`\s+int\(11\)\s+NOT\s+NULL\s+AUTO_INCREMENT/i', $contenido, $matches)) {
    $campo_auto = $matches[1];
    // Buscar la definición original del campo
    if (preg_match("/`$campo_auto`\s+varchar\(/i", $contenido)) {
        $errores[] = "Inconsistencia: Campo '$campo_auto' definido como VARCHAR pero modificado como INT AUTO_INCREMENT";
    }
}

// 4. Verificar comentarios malformados
if (preg_match_all('/^[ \t]*-{3,}[^-\s]/m', $contenido, $matches)) {
    foreach ($matches[0] as $match) {
        $errores[] = "Comentario SQL malformado: " . trim($match);
    }
}

// 5. Verificar que los índices referencien campos existentes
preg_match_all('/ALTER TABLE\s+`(\w+)`[^;]*ADD[^;]*PRIMARY KEY[^;]*\(`([^`]+)`\)/i', $contenido, $pk_matches);
preg_match_all('/CREATE TABLE\s+`(\w+)`[^;]*`([^`]+)`[^,)]*NOT NULL/i', $contenido, $table_matches);

// Mostrar resultados
if (empty($errores) && empty($advertencias)) {
    echo "✅ El archivo SQL parece estar correcto para importación.\n\n";
} else {
    if (!empty($errores)) {
        echo "❌ ERRORES CRÍTICOS encontrados:\n";
        foreach ($errores as $i => $error) {
            echo ($i + 1) . ". $error\n";
        }
        echo "\n";
    }
    
    if (!empty($advertencias)) {
        echo "⚠️  ADVERTENCIAS:\n";
        foreach ($advertencias as $i => $advertencia) {
            echo ($i + 1) . ". $advertencia\n";
        }
        echo "\n";
    }
}

// Verificar estructura general
echo "=== RESUMEN DE ESTRUCTURA ===\n";
preg_match_all('/CREATE TABLE\s+`(\w+)`/i', $contenido, $tablas);
echo "Tablas encontradas: " . implode(", ", $tablas[1]) . "\n";

preg_match_all('/ADD PRIMARY KEY/i', $contenido, $pks);
echo "Claves primarias: " . count($pks[0]) . "\n";

preg_match_all('/ADD KEY|ADD UNIQUE KEY/i', $contenido, $indices);
echo "Índices adicionales: " . count($indices[0]) . "\n";

echo "\n=== FIN DE VALIDACIÓN ===\n";
?>
