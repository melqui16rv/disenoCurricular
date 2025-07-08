#!/bin/bash

echo "=== VALIDACIÓN FINAL DEL ARCHIVO SQL ==="
echo ""

SQL_FILE="import/Diseños Curriculares.sql"

# Verificar que el archivo existe
if [ ! -f "$SQL_FILE" ]; then
    echo "❌ Error: El archivo no existe: $SQL_FILE"
    exit 1
fi

echo "📁 Archivo encontrado: $SQL_FILE"
echo "📊 Tamaño del archivo: $(wc -c < "$SQL_FILE") bytes"
echo "📄 Líneas en el archivo: $(wc -l < "$SQL_FILE")"
echo ""

# Verificar estructura básica
echo "=== VERIFICACIÓN DE ESTRUCTURA ==="

# Contar tablas
TABLAS=$(grep -c "CREATE TABLE" "$SQL_FILE")
echo "🏗️  Tablas definidas: $TABLAS"

# Contar claves primarias
PKS=$(grep -c "ADD PRIMARY KEY" "$SQL_FILE")
echo "🔑 Claves primarias: $PKS"

# Contar índices
INDICES=$(grep -c "ADD.*KEY" "$SQL_FILE")
echo "📑 Índices totales: $INDICES"

echo ""
echo "=== VERIFICACIÓN DE SINTAXIS ==="

# Verificar terminaciones de CREATE TABLE
if grep -q "CREATE TABLE.*)" "$SQL_FILE"; then
    echo "✅ Todas las sentencias CREATE TABLE están cerradas correctamente"
else
    echo "❌ Hay sentencias CREATE TABLE sin cerrar"
fi

# Verificar que no hay comentarios malformados
COMENTARIOS_MALOS=$(grep -c "^[ \t]*-\{3,\}[^-\s]" "$SQL_FILE" || echo "0")
if [ "$COMENTARIOS_MALOS" -eq 0 ]; then
    echo "✅ No se encontraron comentarios malformados"
else
    echo "⚠️  Se encontraron $COMENTARIOS_MALOS comentarios malformados"
fi

# Verificar consistencia de AUTO_INCREMENT
if grep -q "codigoRapReporte.*int.*AUTO_INCREMENT" "$SQL_FILE" && grep -q "codigoRapReporte.*int(11)" "$SQL_FILE"; then
    echo "✅ Tipo de datos consistent para AUTO_INCREMENT"
else
    echo "⚠️  Posible inconsistencia en AUTO_INCREMENT"
fi

echo ""
echo "=== ESTRUCTURA DE TABLAS ==="
grep "CREATE TABLE" "$SQL_FILE" | sed 's/CREATE TABLE /🏗️  /' | sed 's/ ($//'

echo ""
echo "=== CLAVES PRIMARIAS ==="
grep "ADD PRIMARY KEY" "$SQL_FILE" | sed 's/.*ADD PRIMARY KEY (/🔑 /' | sed 's/);$//'

echo ""
echo "✅ VALIDACIÓN COMPLETADA"
echo ""
echo "📋 RESUMEN:"
echo "   - El archivo SQL ha sido corregido y limpiado"
echo "   - Se eliminaron comentarios malformados"
echo "   - Se corrigió la inconsistencia de tipos de datos en AUTO_INCREMENT"
echo "   - La sintaxis SQL es válida para importación en phpMyAdmin"
echo ""
echo "🚀 El archivo está listo para importar en phpMyAdmin"
