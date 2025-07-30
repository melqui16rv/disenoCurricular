<?php
/**
 * Script de prueba simplificado para verificar la lógica de completitud
 * (Sin conexión a base de datos)
 */

// Mock de datos de prueba
$disenosEjemplo = [
    // Diseño COMPLETO
    [
        'codigoDiseno' => 'TEST001',
        'denominacionDiseno' => 'Diseño Completo',
        'lineaTecnologica' => 'Línea A',
        'redTecnologica' => 'Red Tecnológica A',
        'redConocimiento' => 'Red Conocimiento A',
        'horasDesarrolloLectiva' => 800,
        'horasDesarrolloProductiva' => 400,
        'mesesDesarrolloLectiva' => 12,
        'mesesDesarrolloProductiva' => 6,
        'nivelAcademicoIngreso' => 'Básica Secundaria',
        'gradoNivelAcademico' => 9,
        'formacionTrabajoDesarrolloHumano' => 'Sí aplica',
        'edadMinima' => 16,
        'requisitosAdicionales' => 'Ninguno'
    ],
    // Diseño INCOMPLETO (faltan campos obligatorios)
    [
        'codigoDiseno' => 'TEST002',
        'denominacionDiseno' => 'Diseño Incompleto',
        'lineaTecnologica' => '', // FALTA
        'redTecnologica' => 'Red Tecnológica B',
        'redConocimiento' => 'Red Conocimiento B',
        'horasDesarrolloLectiva' => 0, // FALTA
        'horasDesarrolloProductiva' => 0, // FALTA
        'mesesDesarrolloLectiva' => 0, // FALTA
        'mesesDesarrolloProductiva' => 0, // FALTA
        'nivelAcademicoIngreso' => 'Básica Secundaria',
        'gradoNivelAcademico' => 9,
        'formacionTrabajoDesarrolloHumano' => 'Sí aplica',
        'edadMinima' => 16,
        'requisitosAdicionales' => 'Ninguno'
    ],
    // Diseño PARCIALMENTE COMPLETO (tiene horas pero no meses)
    [
        'codigoDiseno' => 'TEST003',
        'denominacionDiseno' => 'Diseño Parcial',
        'lineaTecnologica' => 'Línea C',
        'redTecnologica' => 'Red Tecnológica C',
        'redConocimiento' => 'Red Conocimiento C',
        'horasDesarrolloLectiva' => 600,
        'horasDesarrolloProductiva' => 300,
        'mesesDesarrolloLectiva' => 0, // Sin meses
        'mesesDesarrolloProductiva' => 0, // Sin meses
        'nivelAcademicoIngreso' => 'Básica Secundaria',
        'gradoNivelAcademico' => 9,
        'formacionTrabajoDesarrolloHumano' => 'Sí aplica',
        'edadMinima' => 16,
        'requisitosAdicionales' => 'Ninguno'
    ]
];

// Definir la lógica de verificación (copiada del método)
function verificarCompletitudDiseño($diseño) {
    // 1. Campos de tecnología y conocimiento
    if (empty($diseño['lineaTecnologica']) || 
        empty($diseño['redTecnologica']) || 
        empty($diseño['redConocimiento'])) {
        return false;
    }
    
    // 2. Validación de horas y meses (debe tener AL MENOS uno de los dos sistemas completo)
    $horasLectiva = (float)($diseño['horasDesarrolloLectiva'] ?? 0);
    $horasProductiva = (float)($diseño['horasDesarrolloProductiva'] ?? 0);
    $mesesLectiva = (float)($diseño['mesesDesarrolloLectiva'] ?? 0);
    $mesesProductiva = (float)($diseño['mesesDesarrolloProductiva'] ?? 0);

    $tieneHorasCompletas = ($horasLectiva > 0 && $horasProductiva > 0);
    $tieneMesesCompletos = ($mesesLectiva > 0 && $mesesProductiva > 0);

    if (!$tieneHorasCompletas && !$tieneMesesCompletos) {
        return false;
    }
    
    // 3. Campos académicos y requisitos
    if (empty($diseño['nivelAcademicoIngreso']) || 
        empty($diseño['gradoNivelAcademico']) || $diseño['gradoNivelAcademico'] == 0 ||
        empty($diseño['formacionTrabajoDesarrolloHumano']) || 
        empty($diseño['edadMinima']) || $diseño['edadMinima'] == 0 ||
        empty($diseño['requisitosAdicionales'])) {
        return false;
    }
    
    // Si llegamos aquí, el diseño tiene información completa
    return true;
}

echo "<h2>Prueba de lógica de completitud (Sin DB)</h2>\n\n";

foreach ($disenosEjemplo as $diseño) {
    $esCompleto = verificarCompletitudDiseño($diseño);
    $estado = $esCompleto ? 'COMPLETO' : 'INCOMPLETO';
    
    echo "<h3>Diseño: {$diseño['codigoDiseno']} - {$diseño['denominacionDiseno']}</h3>\n";
    echo "<strong>Estado: {$estado}</strong>\n";
    
    // Mostrar detalles relevantes para debugging
    echo "- Línea Tecnológica: '" . ($diseño['lineaTecnologica'] ?? 'NULL') . "'\n";
    echo "- Red Tecnológica: '" . ($diseño['redTecnologica'] ?? 'NULL') . "'\n";
    echo "- Red Conocimiento: '" . ($diseño['redConocimiento'] ?? 'NULL') . "'\n";
    echo "- Horas Lectiva: " . ($diseño['horasDesarrolloLectiva'] ?? 0) . "\n";
    echo "- Horas Productiva: " . ($diseño['horasDesarrolloProductiva'] ?? 0) . "\n";
    echo "- Meses Lectiva: " . ($diseño['mesesDesarrolloLectiva'] ?? 0) . "\n";
    echo "- Meses Productiva: " . ($diseño['mesesDesarrolloProductiva'] ?? 0) . "\n";
    echo "- Nivel Académico: '" . ($diseño['nivelAcademicoIngreso'] ?? 'NULL') . "'\n";
    echo "- Grado: " . ($diseño['gradoNivelAcademico'] ?? 0) . "\n";
    echo "- Edad Mínima: " . ($diseño['edadMinima'] ?? 0) . "\n";
    echo "- Requisitos: '" . ($diseño['requisitosAdicionales'] ?? 'NULL') . "'\n";
    echo "\n";
}

echo "<h3>Resumen de resultados esperados:</h3>\n";
echo "- TEST001 (Completo): Debe ser COMPLETO ✓\n";
echo "- TEST002 (Incompleto): Debe ser INCOMPLETO ✓\n";
echo "- TEST003 (Parcial): Debe ser COMPLETO (tiene horas completas) ✓\n";

echo "\n<strong>Prueba de lógica completada</strong>\n";
?>
