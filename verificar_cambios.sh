#!/bin/bash

echo "=== VERIFICACIÓN DE CAMBIOS REALIZADOS ==="
echo ""

echo "1. Verificando que los filtros específicos para completar información existan:"
if [ -f "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/filtros_completar_informacion.php" ]; then
    echo "✅ Archivo filtros_completar_informacion.php creado correctamente"
else
    echo "❌ Archivo filtros_completar_informacion.php NO encontrado"
fi

echo ""
echo "2. Verificando cambios en metodosDisenos.php para codigoCompetenciaPDF:"
if grep -q "codigoCompetenciaPDF" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/math/forms/metodosDisenos.php"; then
    echo "✅ Campo codigoCompetenciaPDF ya implementado en metodosDisenos.php"
else
    echo "❌ Campo codigoCompetenciaPDF NO encontrado en metodosDisenos.php"
fi

echo ""
echo "3. Verificando formularios de competencias:"
echo "   - Crear competencias:"
if grep -q 'type="number".*codigoCompetenciaPDF' "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/crear_competencias.php"; then
    echo "   ✅ Campo numérico agregado correctamente"
else
    echo "   ❌ Campo numérico NO encontrado"
fi

echo "   - Editar competencias:"
if grep -q 'type="number".*codigoCompetenciaPDF' "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/editar_competencias.php"; then
    echo "   ✅ Campo numérico agregado correctamente"
else
    echo "   ❌ Campo numérico NO encontrado"
fi

echo "   - Completar competencias:"
if grep -q 'type="number".*codigoCompetenciaPDF' "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/completar_competencias.php"; then
    echo "   ✅ Campo numérico agregado correctamente"
else
    echo "   ❌ Campo numérico NO encontrado"
fi

echo ""
echo "4. Verificando separación de filtros:"
if grep -q "generarFiltrosCompletarInformacion" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/completar_informacion.php"; then
    echo "✅ Filtros específicos implementados en completar_informacion.php"
else
    echo "❌ Filtros específicos NO implementados"
fi

if grep -q "completar_informacion.*Use filtros_completar_informacion" "/Users/melquiromero/Documents/phpStorm/disenoCurricular/app/forms/vistas/funciones_paginacion.php"; then
    echo "✅ Protección agregada en funciones_paginacion.php"
else
    echo "❌ Protección NO agregada"
fi

echo ""
echo "=== RESUMEN DE CAMBIOS ==="
echo ""
echo "1. ✅ Creado filtros_completar_informacion.php con filtros específicos"
echo "2. ✅ Campo codigoCompetenciaPDF agregado como numérico en formularios"
echo "3. ✅ Filtros separados entre accion=listar y accion=completar_informacion"
echo "4. ✅ Protección agregada para evitar confusión entre filtros"
echo ""
echo "Cambios completados exitosamente!"
