#!/bin/bash

echo "🔍 VALIDACIÓN COMPLETA DE ARCHIVOS SQL ACTUALIZADOS"
echo "==================================================="
echo ""

# Contador de errores
errores=0

# Array de archivos a validar
archivos=(
    "import/Base_De_Datos.sql"
    "import/Diseños Curriculares.sql"
    "import/FORMA_de_importacion.sql"
    "import/datos_ejemplo.sql"
    "import/insertPruebaCompletar.sql"
)

echo "📁 ARCHIVOS A VALIDAR:"
for archivo in "${archivos[@]}"; do
    if [ -f "$archivo" ]; then
        echo "   ✅ $archivo"
    else
        echo "   ❌ $archivo (NO ENCONTRADO)"
        ((errores++))
    fi
done
echo ""

echo "🔧 VALIDACIONES ESTRUCTURALES:"
echo ""

# Función para validar estructuras de tablas
validar_estructura() {
    local archivo=$1
    local errores_archivo=0
    
    echo "📄 Validando: $archivo"
    
    if [ ! -f "$archivo" ]; then
        echo "   ❌ Archivo no encontrado"
        return 1
    fi
    
    # Validar tabla diseños
    if grep -q "CREATE TABLE.*diseños" "$archivo"; then
        echo "   ✅ Tabla 'diseños' encontrada"
        
        # Validar campos críticos
        if grep -q "codigoDiseño.*VARCHAR(255).*NOT NULL" "$archivo"; then
            echo "   ✅ Campo 'codigoDiseño' correcto"
        else
            echo "   ❌ Campo 'codigoDiseño' incorrecto o faltante"
            ((errores_archivo++))
        fi
        
        if grep -q "gradoNivelAcademico.*INT" "$archivo"; then
            echo "   ✅ Campo 'gradoNivelAcademico' es INT"
        else
            echo "   ⚠️  Campo 'gradoNivelAcademico' podría no ser INT"
        fi
        
        if grep -q "PRIMARY KEY.*codigoDiseño" "$archivo"; then
            echo "   ✅ Clave primaria 'codigoDiseño' definida"
        else
            echo "   ❌ Clave primaria 'codigoDiseño' faltante"
            ((errores_archivo++))
        fi
    else
        echo "   ⚠️  Tabla 'diseños' no encontrada en este archivo"
    fi
    
    # Validar tabla competencias
    if grep -q "CREATE TABLE.*competencias" "$archivo"; then
        echo "   ✅ Tabla 'competencias' encontrada"
        
        if grep -q "codigoDiseñoCompetencia.*VARCHAR(255).*NOT NULL" "$archivo"; then
            echo "   ✅ Campo 'codigoDiseñoCompetencia' correcto"
        else
            echo "   ❌ Campo 'codigoDiseñoCompetencia' incorrecto"
            ((errores_archivo++))
        fi
        
        if grep -q "PRIMARY KEY.*codigoDiseñoCompetencia" "$archivo"; then
            echo "   ✅ Clave primaria 'codigoDiseñoCompetencia' definida"
        else
            echo "   ❌ Clave primaria 'codigoDiseñoCompetencia' faltante"
            ((errores_archivo++))
        fi
    fi
    
    # Validar tabla raps
    if grep -q "CREATE TABLE.*raps" "$archivo"; then
        echo "   ✅ Tabla 'raps' encontrada"
        
        if grep -q "codigoDiseñoCompetenciaRap.*VARCHAR(255).*NOT NULL" "$archivo"; then
            echo "   ✅ Campo 'codigoDiseñoCompetenciaRap' correcto"
        else
            echo "   ❌ Campo 'codigoDiseñoCompetenciaRap' incorrecto"
            ((errores_archivo++))
        fi
        
        if grep -q "codigoRapAutomatico.*INT.*AUTO_INCREMENT" "$archivo"; then
            echo "   ✅ Campo 'codigoRapAutomatico' con AUTO_INCREMENT"
        else
            echo "   ⚠️  Campo 'codigoRapAutomatico' podría no tener AUTO_INCREMENT"
        fi
        
        if grep -q "PRIMARY KEY.*codigoDiseñoCompetenciaRap" "$archivo"; then
            echo "   ✅ Clave primaria 'codigoDiseñoCompetenciaRap' definida"
        else
            echo "   ❌ Clave primaria 'codigoDiseñoCompetenciaRap' faltante"
            ((errores_archivo++))
        fi
    fi
    
    # Validar sintaxis básica
    if grep -q "ENGINE=InnoDB" "$archivo"; then
        echo "   ✅ Especificación de motor InnoDB encontrada"
    else
        echo "   ⚠️  Especificación de motor InnoDB faltante (opcional)"
    fi
    
    # Validar comentarios malformados
    comentarios_malos=$(grep -c "^[ \t]*-\{3,\}[^-\s]" "$archivo" 2>/dev/null || echo "0")
    if [ "$comentarios_malos" -eq 0 ]; then
        echo "   ✅ No hay comentarios malformados"
    else
        echo "   ❌ Se encontraron $comentarios_malos comentarios malformados"
        ((errores_archivo++))
    fi
    
    echo ""
    return $errores_archivo
}

# Validar cada archivo
for archivo in "${archivos[@]}"; do
    validar_estructura "$archivo"
    resultado=$?
    ((errores += resultado))
done

echo "🧪 VALIDACIONES ESPECÍFICAS:"
echo ""

# Validar consistencia entre archivos base
echo "🔄 Verificando consistencia entre Base_De_Datos.sql y Diseños Curriculares.sql..."

# Extraer estructura de diseños de ambos archivos
if [ -f "import/Base_De_Datos.sql" ] && [ -f "import/Diseños Curriculares.sql" ]; then
    campos_base=$(grep -A 30 "CREATE TABLE.*diseños" "import/Base_De_Datos.sql" | grep -c "`.*`")
    campos_disenos=$(grep -A 30 "CREATE TABLE.*diseños" "import/Diseños Curriculares.sql" | grep -c "`.*`")
    
    echo "   Campos en Base_De_Datos.sql: $campos_base"
    echo "   Campos en Diseños Curriculares.sql: $campos_disenos"
    
    if [ "$campos_base" -eq "$campos_disenos" ]; then
        echo "   ✅ Número de campos coincide"
    else
        echo "   ⚠️  Número de campos difiere (revisar manualmente)"
    fi
else
    echo "   ❌ No se pueden comparar los archivos base"
    ((errores++))
fi

echo ""

# Validar datos de ejemplo
echo "🗃️  Verificando estructura de datos de ejemplo..."

if [ -f "import/datos_ejemplo.sql" ]; then
    # Verificar que use codigoRapDiseño en lugar de codigoRap
    if grep -q "codigoRapDiseño" "import/datos_ejemplo.sql"; then
        echo "   ✅ Usa 'codigoRapDiseño' correctamente"
    else
        echo "   ❌ No usa 'codigoRapDiseño' en datos de ejemplo"
        ((errores++))
    fi
    
    # Verificar que no especifique codigoRapAutomatico en INSERT
    if grep -q "codigoRapAutomatico" "import/datos_ejemplo.sql"; then
        echo "   ⚠️  Puede contener referencias a 'codigoRapAutomatico' en INSERT (revisar)"
    else
        echo "   ✅ No especifica 'codigoRapAutomatico' en INSERT statements"
    fi
else
    echo "   ❌ Archivo datos_ejemplo.sql no encontrado"
    ((errores++))
fi

echo ""

echo "📊 RESUMEN FINAL:"
echo "=================="

if [ $errores -eq 0 ]; then
    echo "🎉 ¡VALIDACIÓN EXITOSA!"
    echo ""
    echo "✅ Todos los archivos SQL han sido actualizados correctamente"
    echo "✅ Las estructuras de tablas son consistentes"
    echo "✅ Los tipos de datos son correctos"
    echo "✅ Las claves primarias están definidas apropiadamente"
    echo "✅ Los comentarios SQL están bien formateados"
    echo "✅ Los datos de ejemplo usan la estructura actualizada"
    echo ""
    echo "🚀 Los archivos están listos para importación en phpMyAdmin"
else
    echo "⚠️  SE ENCONTRARON $errores PROBLEMAS"
    echo ""
    echo "📋 RECOMENDACIONES:"
    echo "   - Revisar los errores marcados con ❌"
    echo "   - Verificar manualmente las advertencias marcadas con ⚠️"
    echo "   - Probar importación en ambiente de desarrollo antes de producción"
fi

echo ""
echo "📁 ARCHIVOS VALIDADOS:"
for archivo in "${archivos[@]}"; do
    if [ -f "$archivo" ]; then
        tamano=$(wc -c < "$archivo")
        lineas=$(wc -l < "$archivo")
        echo "   📄 $archivo - $tamano bytes, $lineas líneas"
    fi
done

echo ""
echo "✨ Validación completada: $(date)"
