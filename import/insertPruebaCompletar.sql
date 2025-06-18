-- ========================================
-- DATOS DE PRUEBA PARA "COMPLETAR INFORMACIÓN FALTANTE"
-- Estos registros tienen campos específicos vacíos para probar
-- la funcionalidad de detección de información faltante
-- ========================================

-- Limpiar datos existentes (opcional - comentar si no se desea)
-- DELETE FROM raps;
-- DELETE FROM competencias;
-- DELETE FROM diseños;

-- ========================================
-- TABLA: diseños
-- ========================================

-- CASO 1: Diseño con campos de tecnología faltantes
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`,
    `lineaTecnologica`, `redTecnologica`, `redConocimiento`,
    `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, 
    `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`,
    `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`,
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, 
    `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`
) VALUES (
    '100001-1', '100001', '1', 'Técnico en Desarrollo de Software',
    NULL, NULL, NULL, -- lineaTecnologica, redTecnologica, redConocimiento FALTANTES
    1440.00, 880.00, -- Horas completas (no debe aparecer para completar)
    0.00, 0.00, -- Meses vacíos (pero como tiene horas, está bien)
    2320.00, 0.00,
    'Media', 11, 'No', 16, 'Conocimientos básicos en programación'
);

-- CASO 2: Diseño sin horas NI meses (debe aparecer para completar)
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`,
    `lineaTecnologica`, `redTecnologica`, `redConocimiento`,
    `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, 
    `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`,
    `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`,
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, 
    `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`
) VALUES (
    '100002-1', '100002', '1', 'Tecnólogo en Gestión de Redes',
    'Tecnologías de la Información', 'Redes y Telecomunicaciones', 'Infraestructura Tecnológica',
    0.00, 0.00, -- Horas vacías
    0.00, 0.00, -- Meses vacíos también - DEBE APARECER PARA COMPLETAR
    0.00, 0.00,
    NULL, 0, NULL, 0, NULL -- Campos académicos FALTANTES
);

-- CASO 3: Diseño solo con meses (no debe aparecer para completar)
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`,
    `lineaTecnologica`, `redTecnologica`, `redConocimiento`,
    `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, 
    `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`,
    `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`,
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, 
    `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`
) VALUES (
    '100003-1', '100003', '1', 'Especialización en Seguridad Informática',
    'Tecnologías de la Información', 'Seguridad Informática', 'Ciberseguridad',
    0.00, 0.00, -- Horas vacías
    18.00, 6.00, -- Pero tiene meses completos (no debe aparecer para completar)
    0.00, 24.00,
    'Técnico', 0, 'Si', 0, '' -- gradoNivelAcademico, edadMinima, requisitosAdicionales FALTANTES
);

-- CASO 4: Diseño con campos académicos faltantes
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`,
    `lineaTecnologica`, `redTecnologica`, `redConocimiento`,
    `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, 
    `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`,
    `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`,
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, 
    `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`
) VALUES (
    '100004-1', '100004', '1', 'Técnico en Diseño Gráfico',
    'Artes y Comunicación', 'Diseño Visual', 'Comunicación Gráfica',
    1200.00, 600.00, -- Horas completas
    0.00, 0.00,
    1800.00, 0.00,
    '', 0, '', 0, '' -- TODOS los campos académicos FALTANTES
);

-- CASO 5: Diseño completo (NO debe aparecer para completar)
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`,
    `lineaTecnologica`, `redTecnologica`, `redConocimiento`,
    `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, 
    `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`,
    `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`,
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, 
    `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`
) VALUES (
    '100005-1', '100005', '1', 'Tecnólogo en Administración de Empresas',
    'Gestión Empresarial', 'Administración', 'Gestión Organizacional',
    1600.00, 800.00, -- Horas completas
    0.00, 0.00,
    2400.00, 0.00,
    'Media', 11, 'No', 16, 'Conocimientos básicos en matemáticas y contabilidad'
);

-- ========================================
-- TABLA: competencias
-- ========================================

-- Competencias para el diseño 100001-1 (con campos faltantes)
INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
-- CASO 1: Competencia con nombre faltante
(
    '100001-1-001', '001', '', -- nombreCompetencia FALTANTE
    'Desarrollar software aplicando metodologías ágiles',
    480.00,
    'Título profesional en Ingeniería de Sistemas o afín',
    'Mínimo 2 años de experiencia en desarrollo de software'
),
-- CASO 2: Competencia con horas = 0 (faltante)
(
    '100001-1-002', '002', 'Gestionar bases de datos',
    'Diseñar, implementar y administrar bases de datos',
    0.00, -- horasDesarrolloCompetencia FALTANTE (= 0)
    '', -- requisitosAcademicosInstructor FALTANTE
    'Experiencia en administración de bases de datos MySQL, PostgreSQL'
),
-- CASO 3: Competencia con instructor faltante
(
    '100001-1-003', '003', 'Implementar seguridad en aplicaciones',
    '', -- normaUnidadCompetencia FALTANTE
    240.00,
    'Especialización en Seguridad Informática',
    '' -- experienciaLaboralInstructor FALTANTE
);

-- Competencias para el diseño 100002-1 (con campos faltantes)
INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
-- CASO 4: Competencia con múltiples campos faltantes
(
    '100002-1-001', '001', '', -- nombreCompetencia FALTANTE
    '', -- normaUnidadCompetencia FALTANTE
    0.00, -- horasDesarrolloCompetencia FALTANTE
    '', -- requisitosAcademicosInstructor FALTANTE
    '' -- experienciaLaboralInstructor FALTANTE
);

-- Competencia para el diseño 100005-1 (COMPLETA - no debe aparecer)
INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
(
    '100005-1-001', '001', 'Gestionar procesos administrativos',
    'Planificar, organizar y controlar procesos administrativos empresariales',
    320.00,
    'Título profesional en Administración de Empresas',
    'Mínimo 3 años de experiencia en gestión empresarial'
);

-- ========================================
-- TABLA: raps
-- ========================================

-- RAPs para la competencia 100001-1-001 (con campos faltantes)
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRapAutomatico`, `codigoRapDiseño`,
    `nombreRap`, `horasDesarrolloRap`
) VALUES 
-- CASO 1: RAP con codigoRapDiseño faltante
(
    '100001-1-001-1', 1, '', -- codigoRapDiseño FALTANTE
    'Analizar requerimientos de software según especificaciones del cliente',
    80.00
),
-- CASO 2: RAP con nombreRap faltante
(
    '100001-1-001-2', 2, 'RA2',
    '', -- nombreRap FALTANTE
    120.00
),
-- CASO 3: RAP con horas = 0 (faltante)
(
    '100001-1-001-3', 3, 'RA3',
    'Implementar funcionalidades del software según diseño técnico',
    0.00 -- horasDesarrolloRap FALTANTE (= 0)
);

-- RAPs para la competencia 100002-1-001 (con múltiples campos faltantes)
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRapAutomatico`, `codigoRapDiseño`,
    `nombreRap`, `horasDesarrolloRap`
) VALUES 
-- CASO 4: RAP con todos los campos principales faltantes
(
    '100002-1-001-1', 4, '', -- codigoRapDiseño FALTANTE
    '', -- nombreRap FALTANTE
    0.00 -- horasDesarrolloRap FALTANTE
);

-- RAP para la competencia 100005-1-001 (COMPLETO - no debe aparecer)
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRapAutomatico`, `codigoRapDiseño`,
    `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '100005-1-001-1', 5, 'RA1',
    'Identificar procesos administrativos de la organización según estructura empresarial',
    80.00
);

-- ========================================
-- RESUMEN DE CASOS DE PRUEBA
-- ========================================

/*
DISEÑOS QUE DEBEN APARECER PARA COMPLETAR:
- 100001-1: Falta lineaTecnologica, redTecnologica, redConocimiento
- 100002-1: Falta horas/meses + nivelAcademicoIngreso, gradoNivelAcademico, formacionTrabajoDesarrolloHumano, edadMinima, requisitosAdicionales
- 100003-1: Falta gradoNivelAcademico, edadMinima, requisitosAdicionales
- 100004-1: Falta nivelAcademicoIngreso, gradoNivelAcademico, formacionTrabajoDesarrolloHumano, edadMinima, requisitosAdicionales

DISEÑOS QUE NO DEBEN APARECER:
- 100005-1: Está completo

COMPETENCIAS QUE DEBEN APARECER PARA COMPLETAR:
- 100001-1-001: Falta nombreCompetencia
- 100001-1-002: Falta horasDesarrolloCompetencia, requisitosAcademicosInstructor
- 100001-1-003: Falta normaUnidadCompetencia, experienciaLaboralInstructor
- 100002-1-001: Faltan TODOS los campos

COMPETENCIAS QUE NO DEBEN APARECER:
- 100005-1-001: Está completa

RAPS QUE DEBEN APARECER PARA COMPLETAR:
- 100001-1-001-1: Falta codigoRapDiseño
- 100001-1-001-2: Falta nombreRap
- 100001-1-001-3: Falta horasDesarrolloRap
- 100002-1-001-1: Faltan TODOS los campos

RAPS QUE NO DEBEN APARECER:
- 100005-1-001-1: Está completo
*/