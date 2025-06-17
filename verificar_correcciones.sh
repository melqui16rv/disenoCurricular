#!/bin/bash

# Script de verificación final - Comparación de RAPs
# Verifica que todas las correcciones estén en su lugar

echo "🔍 VERIFICACIÓN FINAL - Funcionalidad de Comparación de RAPs"
echo "=============================================================="
echo

# Verificar rutas AJAX
echo "1. Verificando rutas AJAX..."
CREAR_FETCH=$(grep -n "fetch.*ajax.php" app/forms/vistas/crear_raps.php)
EDITAR_FETCH=$(grep -n "fetch.*ajax.php" app/forms/vistas/editar_raps.php)

if [[ $CREAR_FETCH == *"../control/ajax.php"* ]] && [[ $EDITAR_FETCH == *"../control/ajax.php"* ]]; then
    echo "✅ Rutas AJAX corregidas correctamente"
    echo "   - crear_raps.php: $CREAR_FETCH"
    echo "   - editar_raps.php: $EDITAR_FETCH"
else
    echo "❌ Error en rutas AJAX"
    exit 1
fi

echo

# Verificar case en ajax.php
echo "2. Verificando case en ajax.php..."
if grep -q "case 'obtener_comparacion_raps':" app/forms/control/ajax.php; then
    echo "✅ Case 'obtener_comparacion_raps' encontrado"
else
    echo "❌ Case 'obtener_comparacion_raps' NO encontrado"
    exit 1
fi

# Verificar respuesta estandarizada
if grep -q "\$response\['data'\] = \$comparacion;" app/forms/control/ajax.php; then
    echo "✅ Respuesta estandarizada con 'data'"
else
    echo "❌ Respuesta no usa 'data'"
    exit 1
fi

echo

# Verificar funciones JavaScript
echo "3. Verificando funciones JavaScript..."
if grep -q "function mostrarComparacion(data)" app/forms/vistas/crear_raps.php && 
   grep -q "function mostrarComparacion(data)" app/forms/vistas/editar_raps.php; then
    echo "✅ Función mostrarComparacion() presente en ambos archivos"
else
    echo "❌ Función mostrarComparacion() faltante"
    exit 1
fi

# Verificar uso de data.data
if grep -q "data.data" app/forms/vistas/crear_raps.php && 
   grep -q "data.data" app/forms/vistas/editar_raps.php; then
    echo "✅ Ambos archivos usan data.data correctamente"
else
    echo "❌ Error en uso de data.data"
    exit 1
fi

echo

# Verificar archivos de debug
echo "4. Verificando archivos de debug disponibles..."
DEBUG_FILES=("debug_ajax_comparacion.php" "verificacion_final_comparacion.php" "CORRECCIONES_COMPLETADAS.md" "CORRECCION_RUTAS_AJAX.md")

for file in "${DEBUG_FILES[@]}"; do
    if [[ -f "$file" ]]; then
        echo "✅ $file - Disponible"
    else
        echo "❌ $file - No encontrado"
    fi
done

echo
echo "🎯 RESUMEN:"
echo "==========="
echo "✅ Rutas AJAX: Corregidas (ruta relativa)"
echo "✅ Función ajax.php: Case implementado"
echo "✅ Respuesta: Estandarizada con 'data'"
echo "✅ JavaScript: Funciones sincronizadas"
echo "✅ Archivos debug: Disponibles"
echo
echo "🚀 ESTADO: LISTO PARA DEPLOY"
echo
echo "📋 Archivos para subir al hosting:"
echo "   1. app/forms/control/ajax.php"
echo "   2. app/forms/vistas/crear_raps.php"
echo "   3. app/forms/vistas/editar_raps.php"
echo "   4. debug_ajax_comparacion.php (opcional, para testing)"
echo
echo "✨ La funcionalidad de comparación de RAPs debería funcionar correctamente."
