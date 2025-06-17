--
-- Estructura de tabla para la tabla `diseños`
--
CREATE TABLE `diseños` (
  `codigoDiseño` varchar(255) NOT NULL,
  `codigoPrograma` varchar(255) NOT NULL,
  `versionPrograma` varchar(255) NOT NULL,
  `nombrePrograma` varchar(255) NOT NULL,
  `lineaTecnologica` varchar(255) DEFAULT NULL,
  `redTecnologica` varchar(255) DEFAULT NULL,
  `redConocimiento` varchar(255) DEFAULT NULL,
  `horasDesarrolloLectiva` decimal(10,2) DEFAULT NULL,
  `horasDesarrolloProductiva` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloLectiva` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloProductiva` decimal(10,2) DEFAULT NULL,
  `horasDesarrolloDiseño` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloDiseño` decimal(10,2) DEFAULT NULL,
  `nivelAcademicoIngreso` varchar(255) DEFAULT NULL,
  `gradoNivelAcademico` int(11) DEFAULT NULL,
  `formacionTrabajoDesarrolloHumano` enum('Si','No') DEFAULT NULL,
  `edadMinima` int(11) DEFAULT NULL,
  `requisitosAdicionales` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Estructura de tabla para la tabla `competencias`
--
CREATE TABLE `competencias` (
  `codigoDiseñoCompetencia` varchar(255) NOT NULL,
  `codigoCompetencia` varchar(255) NOT NULL,
  `nombreCompetencia` varchar(255) NOT NULL,
  `normaUnidadCompetencia` text DEFAULT NULL,
  `horasDesarrolloCompetencia` decimal(10,2) DEFAULT NULL,
  `requisitosAcademicosInstructor` text DEFAULT NULL,
  `experienciaLaboralInstructor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Estructura de tabla para la tabla `raps`
--
CREATE TABLE `raps` (
  `codigoDiseñoCompetenciaRap` varchar(255) NOT NULL,
  `codigoRapAutomatico` int(11) NOT NULL,
  `codigoRapDiseño` varchar(55) DEFAULT NULL,
  `nombreRap` text DEFAULT NULL,
  `horasDesarrolloRap` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Indices de la tabla `competencias`
--
ALTER TABLE `competencias`
  ADD PRIMARY KEY (`codigoDiseñoCompetencia`),
  ADD KEY `idx_codigo_competencia` (`codigoCompetencia`);
--
-- Indices de la tabla `diseños`
--
ALTER TABLE `diseños`
  ADD PRIMARY KEY (`codigoDiseño`);

--
-- Indices de la tabla `raps`
--
ALTER TABLE `raps`
  ADD PRIMARY KEY (`codigoDiseñoCompetenciaRap`),
  ADD UNIQUE KEY `idx_auto_increment` (`codigoRapAutomatico`);
--
-- AUTO_INCREMENT de la tabla `raps`
--
ALTER TABLE `raps`
  MODIFY `codigoRapAutomatico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;