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
  `codigoRapReporte` varchar(255) NOT NULL,
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
  ADD KEY `idx_codigo_rap` (`codigoRapReporte`);
COMMIT;