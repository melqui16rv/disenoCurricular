--
-- Estructura de tabla para la tabla `diseños`
--
CREATE TABLE `diseños` (
------cargar
  `codigoDiseño` varchar(255) NOT NULL,
  `codigoPrograma` varchar(255) NOT NULL,
  `versionPrograma` varchar(255) NOT NULL,
  `nombrePrograma` varchar(255) NOT NULL,
------
------parabra "NULL"
  `lineaTecnologica` varchar(255) DEFAULT NULL,
  `redTecnologica` varchar(255) DEFAULT NULL,
  `redConocimiento` varchar(255) DEFAULT NULL,
-----
-----numericos "0"
  `horasDesarrolloLectiva` decimal(10,2) DEFAULT NULL,
  `horasDesarrolloProductiva` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloLectiva` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloProductiva` decimal(10,2) DEFAULT NULL,
  `horasDesarrolloDiseño` decimal(10,2) DEFAULT NULL,
  `mesesDesarrolloDiseño` decimal(10,2) DEFAULT NULL,
-----
-----palabra "NULL"
  `nivelAcademicoIngreso` varchar(255) DEFAULT NULL,
----
----numerico "0"
  `gradoNivelAcademico` int(11) DEFAULT NULL,
----
----palabra "NULL"
  `formacionTrabajoDesarrolloHumano` enum('Si','No') DEFAULT NULL,
----
----palabra "NULL"
  `edadMinima` int(11) DEFAULT NULL,
----
----palabra "NULL"
  `requisitosAdicionales` text DEFAULT NULL
----
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `competencias`
--
CREATE TABLE `competencias` (
  ------cargar
  `codigoDiseñoCompetencia` varchar(255) NOT NULL,
  `codigoCompetencia` varchar(255) NOT NULL,
  `nombreCompetencia` varchar(255) NOT NULL,
  ------
  ------palabra "NULL"
  `normaUnidadCompetencia` text DEFAULT NULL,
  ------
  ------numerico "0"
  `horasDesarrolloCompetencia` decimal(10,2) DEFAULT NULL,
  ------
  ------palabra "NULL"
  `requisitosAcademicosInstructor` text DEFAULT NULL,
  `experienciaLaboralInstructor` text DEFAULT NULL
  ------
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Estructura de tabla para la tabla `raps`
--
CREATE TABLE `raps` (
  ----cargar
  `codigoDiseñoCompetenciaRap` varchar(255) NOT NULL,
  `codigoRapAutomatico` int(11) NOT NULL,
  ----
  ----numerico "0"
  `codigoRapDiseño` varchar(55) DEFAULT NULL,
  ------
  ------palabra "NULL"
  `nombreRap` text DEFAULT NULL,
  ------
  ----numerico "0"
  `horasDesarrolloRap` decimal(10,2) DEFAULT NULL
  ------
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