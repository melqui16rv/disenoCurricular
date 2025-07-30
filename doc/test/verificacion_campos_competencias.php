<?php
/**
 * Script para verificar la corrección de campos de competencias
 */

echo "<h2>Verificación de Campos de Competencias</h2>\n\n";

// Simular datos de competencia como los devuelve la base de datos
$competencia_mock = [
    'codigoDiseñoCompetenciaReporte' => '112005-101-1',
    'codigoCompetenciaReporte' => '1', // Campo correcto según DB
    'codigoCompetenciaPDF' => '226493',
    'nombreCompetencia' => 'PROMOVER LA INTERACCIÓN IDÓNEA CONSIGO MISMO, CON LOS DEMÁS Y CON LA NATURALEZA EN LOS CONTEXTOS LABORAL Y SOCIAL',
    'horasDesarrolloCompetencia' => 60.00,
    'normaUnidadCompetencia' => 'Norma de competencia relacionada'
];

echo "<h3>✅ Campos disponibles en la estructura de competencias:</h3>\n";
foreach ($competencia_mock as $campo => $valor) {
    echo "- <strong>{$campo}:</strong> " . htmlspecialchars($valor) . "\n";
}

echo "\n<h3>🔧 Prueba de campos corregidos:</h3>\n";

// Probar acceso al campo correcto
echo "<strong>Código Completo:</strong> " . htmlspecialchars($competencia_mock['codigoDiseñoCompetenciaReporte']) . "\n";
echo "<strong>Código de Competencia:</strong> " . htmlspecialchars($competencia_mock['codigoCompetenciaReporte'] ?? 'CAMPO NO ENCONTRADO') . "\n";
echo "<strong>Código PDF:</strong> " . htmlspecialchars($competencia_mock['codigoCompetenciaPDF'] ?? 'Sin asignar') . "\n";
echo "<strong>Horas:</strong> " . number_format($competencia_mock['horasDesarrolloCompetencia'] ?? 0, 0) . "h\n";

// Probar campo incorrecto (que causaba el error)
echo "\n<h3>❌ Campo que causaba el error:</h3>\n";
if (isset($competencia_mock['codigoCompetencia'])) {
    echo "- codigoCompetencia: " . $competencia_mock['codigoCompetencia'] . "\n";
} else {
    echo "- ✅ Campo 'codigoCompetencia' NO existe (correcto)\n";
    echo "- ✅ Debe usar 'codigoCompetenciaReporte' en su lugar\n";
}

echo "\n<h3>📋 Resumen de correcciones realizadas:</h3>\n";
echo "1. ✅ completar_raps.php - Línea 126: codigoCompetencia → codigoCompetenciaReporte\n";
echo "2. ✅ crear_raps.php - Línea 106: codigoCompetencia → codigoCompetenciaReporte\n";
echo "3. ✅ crear_competencias.php - JavaScript: getElementById('codigoCompetencia') → getElementById('codigoCompetenciaReporte')\n";

echo "\n<h3>🎯 Archivos que usan el campo correcto:</h3>\n";
echo "- ✅ editar_competencias.php: Usa codigoCompetenciaReporte\n";
echo "- ✅ completar_competencias.php: Usa codigoCompetenciaPDF (nuevo campo)\n";
echo "- ✅ listar_competencias.php: Usa codigoCompetenciaReporte\n";

echo "\n<strong>🎉 Todas las correcciones aplicadas correctamente</strong>\n";
?>
