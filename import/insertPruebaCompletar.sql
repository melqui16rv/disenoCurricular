--
-- Estructura de tabla para la tabla `diseños`
--
CREATE TABLE `diseños` (
  -- campos a cargar
  `codigoDiseño` varchar(255) NOT NULL,
  `codigoPrograma` varchar(255) NOT NULL,
  `versionPrograma` varchar(255) NOT NULL,
  `nombrePrograma` varchar(255) NOT NULL,
  -- campos con valor por defecto "NULL"
  `lineaTecnologica` varchar(255) DEFAULT NULL,
  `redTecnologica` varchar(255) DEFAULT NULL,
  `redConocimiento` varchar(255) DEFAULT NULL,
  -- campos numericos con valor por defecto "0"
  `horasDesarrolloLectiva` decimal(10,2) DEFAULT NULL,
  `horasDesarrolloProductiva` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloLectiva` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloProductiva` decimal(10,2) DEFAULT NULL,
  `horasDesarrolloDiseño` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloDiseño` decimal(10,2) DEFAULT NULL,
  -- campos con valor por defecto "NULL"
  `nivelAcademicoIngreso` varchar(255) DEFAULT NULL,
  -- campo numerico con valor por defecto "0"
  `gradoNivelAcademico` int(11) DEFAULT NULL,
  -- campos con valor por defecto "NULL"
  `formacionTrabajoDesarrolloHumano` enum('Si','No') DEFAULT NULL,
  `edadMinima` int(11) DEFAULT NULL,
  `requisitosAdicionales` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `competencias`
--
CREATE TABLE `competencias` (
  -- campos a cargar
  `codigoDiseñoCompetenciaReporte` varchar(255) NOT NULL,
  `codigoCompetenciaReporte` varchar(255) NOT NULL,
  `codigoCompetenciaPDF` varchar(255) DEFAULT NULL,
  `nombreCompetencia` varchar(255) NOT NULL,
  -- campos con valor por defecto "NULL"
  `normaUnidadCompetencia` text DEFAULT NULL,
  -- campo numerico con valor por defecto "0"
  `horasDesarrolloCompetencia` decimal(10,2) DEFAULT NULL,
  -- campos con valor por defecto "NULL"
  `requisitosAcademicosInstructor` text DEFAULT NULL,
  `experienciaLaboralInstructor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Estructura de tabla para la tabla `raps`
--
CREATE TABLE `raps` (
  -- campos a cargar
  `codigoDiseñoCompetenciaReporteRap` varchar(255) NOT NULL,
  `codigoRapReporte` int(11) NOT NULL,
  -- campo numerico con valor por defecto "0"
  -- NOTA: codigoRapDiseno ya no se usa, se eliminó ya que el codigo queda con codigoRapReporte
  -- campos con valor por defecto "NULL"
  `nombreRap` text DEFAULT NULL,
  -- campo numerico con valor por defecto "0"
  `horasDesarrolloRap` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Indices de la tabla `competencias`
--
ALTER TABLE `competencias`
  ADD PRIMARY KEY (`codigoDiseñoCompetenciaReporte`),
  ADD KEY `idx_codigo_competencia` (`codigoCompetenciaReporte`);
--
-- Indices de la tabla `diseños`
--
ALTER TABLE `diseños`
  ADD PRIMARY KEY (`codigoDiseño`);

--
-- Indices de la tabla `raps`
--
ALTER TABLE `raps`
  ADD PRIMARY KEY (`codigoDiseñoCompetenciaReporteRap`),
  ADD UNIQUE KEY `idx_auto_increment` (`codigoRapReporte`);
--
-- AUTO_INCREMENT de la tabla `raps`
--
ALTER TABLE `raps`
  MODIFY `codigoRapReporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

-- ========================================
-- DATOS DE PRUEBA PARA "COMPLETAR INFORMACIÓN FALTANTE"
-- Estos registros tienen campos específicos vacíos para probar
-- la funcionalidad de detección de información faltante
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
    1440.00, 880.00, -- Horas completas
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

-- Insertar competencias de ejemplo con nueva estructura
INSERT INTO `competencias` (
    `codigoDiseñoCompetenciaReporte`, `codigoCompetenciaReporte`, `codigoCompetenciaPDF`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
-- CASO 1: Competencia con campos faltantes
(
    '100001-1-001', '001', '001', '',
    'Desarrollar software aplicando metodologías ágiles',
    480.00,
    'Título profesional en Ingeniería de Sistemas o afín',
    'Mínimo 2 años de experiencia en desarrollo de software'
),
-- CASO 2: Competencia con horas = 0 (faltante)
(
    '100001-1-002', '002', '002', 'Gestionar bases de datos',
    'Diseñar, implementar y administrar bases de datos',
    0.00,
    '',
    'Experiencia en administración de bases de datos MySQL, PostgreSQL'
);

-- RAPs de ejemplo con nueva estructura
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaReporteRap`, `codigoRapReporte`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
-- CASO 1: RAP completo
(
    '100001-1-001-1', 1,
    'Analizar requerimientos de software según especificaciones del cliente',
    80.00
),
-- CASO 2: RAP con datos faltantes
(
    '100001-1-001-2', 2,
    '',
    0.00
);