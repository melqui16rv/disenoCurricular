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

-- Datos de ejemplo para el Sistema de Diseños Curriculares
-- Ejecutar después de crear las tablas principales

-- Insertar diseños de ejemplo
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`, 
    `lineaTecnologica`, `redTecnologica`, `redConocimiento`, 
    `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, 
    `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`, 
    `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`, 
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, 
    `formacionTrabajoDesarrolloHumano`, `edadMinima`, 
    `requisitosAdicionales`
) VALUES 
(
    '124101-1', '124101', '1', 
    'Tecnólogo en Análisis y Desarrollo de Sistemas de Información',
    'Tecnologías de la Información y las Comunicaciones',
    'Tecnologías de la Información y las Comunicaciones',
    'Red de Conocimiento en Tecnologías de la Información',
    1548.00, 880.00, 18.00, 6.00,
    2428.00, 24.00,
    'Media Académica', 11,
    'No', 16,
    'Conocimientos básicos en matemáticas y lógica. Manejo básico de computadores.'
),
(
    '228106-2', '228106', '2', 
    'Tecnólogo en Gestión Logística',
    'Logística y Transporte',
    'Tecnologías de Gestión Administrativa y Servicios Financieros',
    'Red de Conocimiento en Gestión Administrativa',
    1320.00, 880.00, 15.00, 6.00,
    2200.00, 21.00,
    'Media Académica', 11,
    'No', 16,
    'Conocimientos básicos en matemáticas. Capacidad de análisis y síntesis.'
),
(
    '233104-1', '233104', '1',
    'Técnico en Programación de Software',
    'Tecnologías de la Información y las Comunicaciones',
    'Tecnologías de la Información y las Comunicaciones',
    'Red de Conocimiento en Tecnologías de la Información',
    1320.00, 440.00, 12.00, 3.00,
    1760.00, 15.00,
    'Básica Secundaria', 9,
    'Si', 16,
    'Conocimientos básicos en matemáticas y lógica de programación.'
);

-- Insertar competencias de ejemplo
INSERT INTO `competencias` (
    `codigoDiseñoCompetenciaReporte`, `codigoCompetenciaReporte`, `codigoCompetenciaPDF`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
(
    '124101-1-220201501', '220201501', '220201501',
    'Comprender textos en inglés en forma escrita y auditiva',
    'Norma de competencia laboral para la comprensión de textos en inglés técnico',
    180.00,
    'Profesional en Lenguas Modernas, Licenciado en Inglés o áreas afines con certificación internacional mínimo B2',
    'Veinticuatro (24) meses de experiencia en docencia de inglés técnico'
),
(
    '124101-1-220501046', '220501046', '220501046',
    'Aplicar herramientas metodológicas para el desarrollo de proyectos',
    'Norma para la aplicación de metodologías de gestión de proyectos',
    120.00,
    'Profesional en Ingeniería de Sistemas, Administración de Proyectos o áreas afines',
    'Dieciocho (18) meses de experiencia en gestión de proyectos de software'
),
(
    '124101-1-220201048', '220201048', '220201048',
    'Desarrollar el sistema que cumpla con los requisitos de la solución informática',
    'Norma para el desarrollo de sistemas de información',
    400.00,
    'Profesional en Ingeniería de Sistemas, Ingeniería de Software o Tecnología en Desarrollo de Software',
    'Treinta y seis (36) meses de experiencia en desarrollo de software'
);

-- Insertar RAPs de ejemplo
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaReporteRap`, `codigoRapReporte`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '124101-1-220201501-1', 1,
    'Encontrar información específica y predecible en escritos sencillos y cotidianos',
    45.00
),
(
    '124101-1-220201501-2', 2, 
    'Encontrar vocabulario y expresiones de inglés técnico en artículos de revistas, libros especializados, páginas web, etc.',
    45.00
),
(
    '124101-1-220201501-3', 3,
    'Comprender frases y vocabulario habitual sobre temas de interés personal y temas técnicos',
    45.00
),
(
    '124101-1-220201501-4', 4,
    'Comprender la idea principal en avisos y mensajes breves, claros y sencillos en inglés técnico',
    45.00
);
