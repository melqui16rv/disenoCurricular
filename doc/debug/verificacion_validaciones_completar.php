<?php
// Script para verificar que las validaciones de "Completar Información" funcionan correctamente
// con los datos de prueba insertados

echo "=== VERIFICACIÓN DE VALIDACIONES - COMPLETAR INFORMACIÓN ===\n\n";

// Simular los datos que se insertaron
$diseñosPrueba = [
    [
        'codigoDiseño' => '100001-1',
        'nombrePrograma' => 'Técnico en Desarrollo de Software',
        'lineaTecnologica' => null,
        'redTecnologica' => null,
        'redConocimiento' => null,
        'horasDesarrolloLectiva' => 1440.00,
        'horasDesarrolloProductiva' => 880.00,
        'mesesDesarrolloLectiva' => 0.00,
        'mesesDesarrolloProductiva' => 0.00,
        'nivelAcademicoIngreso' => 'Media',
        'gradoNivelAcademico' => 11,
        'formacionTrabajoDesarrolloHumano' => 'No',
        'edadMinima' => 16,
        'requisitosAdicionales' => 'Conocimientos básicos en programación'
    ],
    [
        'codigoDiseño' => '100002-1',
        'nombrePrograma' => 'Tecnólogo en Gestión de Redes',
        'lineaTecnologica' => 'Tecnologías de la Información',
        'redTecnologica' => 'Redes y Telecomunicaciones',
        'redConocimiento' => 'Infraestructura Tecnológica',
        'horasDesarrolloLectiva' => 0.00,
        'horasDesarrolloProductiva' => 0.00,
        'mesesDesarrolloLectiva' => 0.00,
        'mesesDesarrolloProductiva' => 0.00,
        'nivelAcademicoIngreso' => null,
        'gradoNivelAcademico' => 0,
        'formacionTrabajoDesarrolloHumano' => null,
        'edadMinima' => 0,
        'requisitosAdicionales' => null
    ],
    [
        'codigoDiseño' => '100003-1',
        'nombrePrograma' => 'Especialización en Seguridad Informática',
        'lineaTecnologica' => 'Tecnologías de la Información',
        'redTecnologica' => 'Seguridad Informática',
        'redConocimiento' => 'Ciberseguridad',
        'horasDesarrolloLectiva' => 0.00,
        'horasDesarrolloProductiva' => 0.00,
        'mesesDesarrolloLectiva' => 18.00,
        'mesesDesarrolloProductiva' => 6.00,
        'nivelAcademicoIngreso' => 'Técnico',
        'gradoNivelAcademico' => 0,
        'formacionTrabajoDesarrolloHumano' => 'Si',
        'edadMinima' => 0,
        'requisitosAdicionales' => ''
    ],
    [
        'codigoDiseño' => '100005-1',
        'nombrePrograma' => 'Tecnólogo en Administración de Empresas',
        'lineaTecnologica' => 'Gestión Empresarial',
        'redTecnologica' => 'Administración',
        'redConocimiento' => 'Gestión Organizacional',
        'horasDesarrolloLectiva' => 1600.00,
        'horasDesarrolloProductiva' => 800.00,
        'mesesDesarrolloLectiva' => 0.00,
        'mesesDesarrolloProductiva' => 0.00,
        'nivelAcademicoIngreso' => 'Media',
        'gradoNivelAcademico' => 11,
        'formacionTrabajoDesarrolloHumano' => 'No',
        'edadMinima' => 16,
        'requisitosAdicionales' => 'Conocimientos básicos en matemáticas y contabilidad'
    ]
];

// Función para simular la validación de diseños
function validarDiseño($diseño) {
    $camposFaltantes = [];
    
    // 1. Campos de tecnología y conocimiento
    if (empty($diseño['lineaTecnologica'])) $camposFaltantes[] = 'Línea Tecnológica';
    if (empty($diseño['redTecnologica'])) $camposFaltantes[] = 'Red Tecnológica';
    if (empty($diseño['redConocimiento'])) $camposFaltantes[] = 'Red de Conocimiento';
    
    // 2. Validación de horas y meses (solo si NINGUNO de los dos sistemas está completo)
    $horasLectiva = (float)($diseño['horasDesarrolloLectiva'] ?? 0);
    $horasProductiva = (float)($diseño['horasDesarrolloProductiva'] ?? 0);
    $mesesLectiva = (float)($diseño['mesesDesarrolloLectiva'] ?? 0);
    $mesesProductiva = (float)($diseño['mesesDesarrolloProductiva'] ?? 0);
    
    $tieneHorasCompletas = ($horasLectiva > 0 && $horasProductiva > 0);
    $tieneMesesCompletos = ($mesesLectiva > 0 && $mesesProductiva > 0);
    
    if (!$tieneHorasCompletas && !$tieneMesesCompletos) {
        $camposFaltantes[] = 'Debe completar HORAS (Lectivas + Productivas) O MESES (Lectivos + Productivos)';
    }
    
    // 3. Campos académicos y requisitos
    if (empty($diseño['nivelAcademicoIngreso'])) $camposFaltantes[] = 'Nivel Académico de Ingreso';
    if (empty($diseño['gradoNivelAcademico']) || $diseño['gradoNivelAcademico'] == 0) $camposFaltantes[] = 'Grado del Nivel Académico';
    if (empty($diseño['formacionTrabajoDesarrolloHumano'])) $camposFaltantes[] = 'Formación en Trabajo y Desarrollo Humano';
    if (empty($diseño['edadMinima']) || $diseño['edadMinima'] == 0) $camposFaltantes[] = 'Edad Mínima';
    if (empty($diseño['requisitosAdicionales'])) $camposFaltantes[] = 'Requisitos Adicionales';
    
    return $camposFaltantes;
}

echo "=== VERIFICANDO DISEÑOS ===\n";
foreach ($diseñosPrueba as $diseño) {
    $camposFaltantes = validarDiseño($diseño);
    $codigo = $diseño['codigoDiseño'];
    $nombre = $diseño['nombrePrograma'];
    
    if (!empty($camposFaltantes)) {
        echo "✅ DEBE APARECER - {$codigo}: {$nombre}\n";
        echo "   Campos faltantes: " . implode(', ', $camposFaltantes) . "\n\n";
    } else {
        echo "❌ NO DEBE APARECER - {$codigo}: {$nombre} (Completo)\n\n";
    }
}

// Simular datos de competencias
$competenciasPrueba = [
    [
        'codigoDiseñoCompetencia' => '100001-1-001',
        'nombreCompetencia' => '',
        'normaUnidadCompetencia' => 'Desarrollar software aplicando metodologías ágiles',
        'horasDesarrolloCompetencia' => 480.00,
        'requisitosAcademicosInstructor' => 'Título profesional en Ingeniería de Sistemas o afín',
        'experienciaLaboralInstructor' => 'Mínimo 2 años de experiencia en desarrollo de software'
    ],
    [
        'codigoDiseñoCompetencia' => '100001-1-002',
        'nombreCompetencia' => 'Gestionar bases de datos',
        'normaUnidadCompetencia' => 'Diseñar, implementar y administrar bases de datos',
        'horasDesarrolloCompetencia' => 0.00,
        'requisitosAcademicosInstructor' => '',
        'experienciaLaboralInstructor' => 'Experiencia en administración de bases de datos MySQL, PostgreSQL'
    ],
    [
        'codigoDiseñoCompetencia' => '100005-1-001',
        'nombreCompetencia' => 'Gestionar procesos administrativos',
        'normaUnidadCompetencia' => 'Planificar, organizar y controlar procesos administrativos empresariales',
        'horasDesarrolloCompetencia' => 320.00,
        'requisitosAcademicosInstructor' => 'Título profesional en Administración de Empresas',
        'experienciaLaboralInstructor' => 'Mínimo 3 años de experiencia en gestión empresarial'
    ]
];

// Función para simular la validación de competencias
function validarCompetencia($competencia) {
    $camposFaltantes = [];
    
    // 1. Nombre de la competencia
    if (empty($competencia['nombreCompetencia'])) $camposFaltantes[] = 'Nombre de la Competencia';
    
    // 2. Norma unidad competencia
    if (empty($competencia['normaUnidadCompetencia'])) $camposFaltantes[] = 'Norma Unidad Competencia';
    
    // 3. Horas de desarrollo (debe ser > 0)
    $horas = (float)($competencia['horasDesarrolloCompetencia'] ?? 0);
    if ($horas <= 0) $camposFaltantes[] = 'Horas de Desarrollo (debe ser > 0)';
    
    // 4. Requisitos académicos del instructor
    if (empty($competencia['requisitosAcademicosInstructor'])) $camposFaltantes[] = 'Requisitos Académicos del Instructor';
    
    // 5. Experiencia laboral del instructor
    if (empty($competencia['experienciaLaboralInstructor'])) $camposFaltantes[] = 'Experiencia Laboral del Instructor';
    
    return $camposFaltantes;
}

echo "=== VERIFICANDO COMPETENCIAS ===\n";
foreach ($competenciasPrueba as $competencia) {
    $camposFaltantes = validarCompetencia($competencia);
    $codigo = $competencia['codigoDiseñoCompetencia'];
    $nombre = $competencia['nombreCompetencia'] ?: 'Sin nombre';
    
    if (!empty($camposFaltantes)) {
        echo "✅ DEBE APARECER - {$codigo}: {$nombre}\n";
        echo "   Campos faltantes: " . implode(', ', $camposFaltantes) . "\n\n";
    } else {
        echo "❌ NO DEBE APARECER - {$codigo}: {$nombre} (Completo)\n\n";
    }
}

// Simular datos de RAPs
$rapsPrueba = [
    [
        'codigoDiseñoCompetenciaReporteRap' => '100001-1-001-1',
        'codigoRapDiseño' => '',
        'nombreRap' => 'Analizar requerimientos de software según especificaciones del cliente',
        'horasDesarrolloRap' => 80.00
    ],
    [
        'codigoDiseñoCompetenciaReporteRap' => '100001-1-001-2',
        'codigoRapDiseño' => 'RA2',
        'nombreRap' => '',
        'horasDesarrolloRap' => 120.00
    ],
    [
        'codigoDiseñoCompetenciaReporteRap' => '100001-1-001-3',
        'codigoRapDiseño' => 'RA3',
        'nombreRap' => 'Implementar funcionalidades del software según diseño técnico',
        'horasDesarrolloRap' => 0.00
    ],
    [
        'codigoDiseñoCompetenciaReporteRap' => '100005-1-001-1',
        'codigoRapDiseño' => 'RA1',
        'nombreRap' => 'Identificar procesos administrativos de la organización según estructura empresarial',
        'horasDesarrolloRap' => 80.00
    ]
];

// Función para simular la validación de RAPs
function validarRap($rap) {
    $camposFaltantes = [];
    
    // 1. Código RAP diseño
    if (empty($rap['codigoRapDiseño'])) $camposFaltantes[] = 'Código RAP Diseño';
    
    // 2. Nombre del RAP
    if (empty($rap['nombreRap'])) $camposFaltantes[] = 'Nombre del RAP';
    
    // 3. Horas de desarrollo (debe ser > 0)
    $horas = (float)($rap['horasDesarrolloRap'] ?? 0);
    if ($horas <= 0) $camposFaltantes[] = 'Horas de Desarrollo (debe ser > 0)';
    
    return $camposFaltantes;
}

echo "=== VERIFICANDO RAPS ===\n";
foreach ($rapsPrueba as $rap) {
    $camposFaltantes = validarRap($rap);
    $codigo = $rap['codigoDiseñoCompetenciaReporteRap'];
    $nombre = $rap['nombreRap'] ?: 'Sin nombre';
    
    if (!empty($camposFaltantes)) {
        echo "✅ DEBE APARECER - {$codigo}: " . substr($nombre, 0, 50) . "...\n";
        echo "   Campos faltantes: " . implode(', ', $camposFaltantes) . "\n\n";
    } else {
        echo "❌ NO DEBE APARECER - {$codigo}: " . substr($nombre, 0, 50) . "... (Completo)\n\n";
    }
}

echo "=== RESUMEN ESPERADO ===\n";
echo "📊 DISEÑOS que deben aparecer: 3 de 4\n";
echo "📊 COMPETENCIAS que deben aparecer: 2 de 3\n";
echo "📊 RAPS que deben aparecer: 3 de 4\n\n";

echo "🎯 Si los resultados coinciden con este resumen, las validaciones están funcionando correctamente.\n";
echo "🎯 Ejecuta este script después de importar los datos de prueba para verificar.\n";
?>
