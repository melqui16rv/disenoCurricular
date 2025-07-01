-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 01-07-2025 a las 16:21:08
-- Versión del servidor: 10.11.13-MariaDB-cll-lve
-- Versión de PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `appscide_disenos_curriculares`
--

-- --------------------------------------------------------

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

-- --------------------------------------------------------

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

-- --------------------------------------------------------

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
-- Índices para tablas volcadas
--

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `raps`
--
ALTER TABLE `raps`
  MODIFY `codigoRapAutomatico` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
