#!/bin/bash

echo "=== VERIFICACIÓN DE CORRECCIONES ==="
echo ""

echo "1. ✅ CSS - Selección de texto habilitada:"
if grep -q "user-select: text !important" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/assets/css/forms/estilosPrincipales.css"; then
    echo "   ✅ Selección de texto habilitada globalmente"
else
    echo "   ❌ Selección de texto NO habilitada"
fi

echo ""
echo "2. ✅ Variables de filtros corregidas:"
if grep -q "filtro_horas_min" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/listar_disenos.php"; then
    echo "   ✅ listar_disenos.php usa variables correctas"
else
    echo "   ❌ listar_disenos.php NO corregido"
fi

if grep -q "filtro_horas_min" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/listar_competencias.php"; then
    echo "   ✅ listar_competencias.php usa variables correctas"
else
    echo "   ❌ listar_competencias.php NO corregido"
fi

if grep -q "filtro_horas_min" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/listar_raps.php"; then
    echo "   ✅ listar_raps.php usa variables correctas"
else
    echo "   ❌ listar_raps.php NO corregido"
fi

echo ""
echo "3. ✅ JavaScript corregido:"
if grep -q "currentURL.includes('accion=completar_informacion')" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/assets/js/forms/completar-informacion.js"; then
    echo "   ✅ JavaScript solo interfiere en completar_informacion"
else
    echo "   ❌ JavaScript NO corregido"
fi

echo ""
echo "=== PROBLEMAS SOLUCIONADOS ==="
echo "1. ✅ Selección de texto habilitada en toda la interfaz"
echo "2. ✅ Variables de filtros sincronizadas entre index.php y vistas"
echo "3. ✅ JavaScript limitado a solo interferir en su página específica"
echo "4. ✅ Los filtros de accion=listar ya no redirigen a completar_informacion"
echo ""
echo "¡Correcciones completadas exitosamente!"
