-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-06-2025 a las 16:54:45
-- Versión del servidor: 10.11.11-MariaDB-cll-lve
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

--
-- Volcado de datos para la tabla `competencias`
--

INSERT INTO `competencias` (`codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`, `normaUnidadCompetencia`, `horasDesarrolloCompetencia`, `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`) VALUES
('124101-1-220201501', '220201501', 'Programar software de acuerdo con el diseño realizado', 'Norma de competencia laboral NCL 220201501', 400.00, 'Tecnólogo en áreas relacionadas con desarrollo de software', 'Mínimo 2 años en desarrollo de software'),
('124101-1-220201502', '220201502', 'Realizar mantenimiento de software de acuerdo con los procedimientos establecidos', 'Norma de competencia laboral NCL 220201502', 300.00, 'Tecnólogo en áreas relacionadas con sistemas', 'Mínimo 2 años en soporte y mantenimiento'),
('124101-2-220201501', '220201501', 'Programar software de acuerdo con el diseño realizado', 'Norma de competencia laboral NCL 220201501', 450.00, 'Tecnólogo en áreas relacionadas con desarrollo de software', 'Mínimo 3 años en desarrollo de software'),
('124101-2-220201502', '220201502', 'Realizar mantenimiento de software de acuerdo con los procedimientos establecidos', 'Norma de competencia laboral NCL 220201502', 320.00, 'Tecnólogo en áreas relacionadas con sistemas', 'Mínimo 2 años en soporte y mantenimiento'),
('124101-2-220201503', '220201503', 'Implementar sistemas de información según requerimientos definidos', 'Norma de competencia laboral NCL 220201503', 380.00, 'Tecnólogo en sistemas de información', 'Mínimo 3 años en implementación de sistemas'),
('228106-1-220201501', '220201501', 'Programar software de acuerdo con el diseño realizado', 'Norma de competencia laboral NCL 220201501', 350.00, 'Tecnólogo en áreas relacionadas con desarrollo de software', 'Mínimo 2 años en desarrollo de software'),
('228106-1-220201502', '220201502', 'Realizar mantenimiento de software de acuerdo con los procedimientos establecidos', 'Norma de competencia laboral NCL 220201502', 280.00, 'Tecnólogo en áreas relacionadas con sistemas', 'Mínimo 2 años en soporte y mantenimiento'),
('228106-1-220201504', '220201504', 'Desarrollar aplicaciones con lenguajes de programación orientado por objetos', 'Norma de competencia laboral NCL 220201504', 400.00, 'Tecnólogo en programación', 'Mínimo 2 años en programación orientada a objetos'),
('521240-1-220201501', '220201501', 'Programar software de acuerdo con el diseño realizado', 'Norma de competencia laboral NCL 220201501', 280.00, 'Especialista en desarrollo de software', 'Mínimo 4 años en desarrollo de aplicaciones web'),
('521240-1-220201505', '220201505', 'Desarrollar aplicaciones web interactivas', 'Norma de competencia laboral NCL 220201505', 360.00, 'Especialista en desarrollo web', 'Mínimo 3 años en desarrollo web frontend y backend');

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

--
-- Volcado de datos para la tabla `diseños`
--

INSERT INTO `diseños` (`codigoDiseño`, `codigoPrograma`, `versionPrograma`, `nombrePrograma`, `lineaTecnologica`, `redTecnologica`, `redConocimiento`, `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`, `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`, `nivelAcademicoIngreso`, `gradoNivelAcademico`, `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`) VALUES
('124101-1', '124101', '1', 'Tecnología en Desarrollo de Software', 'Tecnologías de la Información y las Comunicaciones', 'Red de Tecnologías de Información y Comunicación', 'Red de Conocimiento en Informática', 0.00, 0.00, 12.00, 6.00, 0.00, 18.00, 'Media', 11, 'Si', 16, 'Conocimientos básicos en matemáticas y lógica'),
('124101-2', '124101', '2', 'Tecnología en Desarrollo de Software - Versión 2', 'Tecnologías de la Información y las Comunicaciones', 'Red de Tecnologías de Información y Comunicación', 'Red de Conocimiento en Informática', 1800.00, 900.00, 18.00, 6.00, 2700.00, 24.00, 'Bachiller', 11, 'Si', 16, 'Conocimientos básicos en matemáticas, lógica y programación'),
('228106-1', '228106', '1', 'Técnico en Programación de Software', 'Tecnologías de la Información y las Comunicaciones', 'Red de Tecnologías de Información y Comunicación', 'Red de Conocimiento en Informática', 1320.00, 440.00, 12.00, 3.00, 1760.00, 15.00, 'Bachiller', 11, 'Si', 16, 'Conocimientos básicos en matemáticas y lógica'),
('521240-1', '521240', '1', 'Especialización en Desarrollo de Aplicaciones Web', 'Tecnologías de la Información y las Comunicaciones', 'Red de Tecnologías de Información y Comunicación', 'Red de Conocimiento en Informática', 600.00, 120.00, 6.00, 1.00, 720.00, 7.00, 'Título Profesional', 16, 'No', 18, 'Conocimientos en programación y desarrollo web');

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
-- Volcado de datos para la tabla `raps`
--

INSERT INTO `raps` (`codigoDiseñoCompetenciaRap`, `codigoRapAutomatico`, `codigoRapDiseño`, `nombreRap`, `horasDesarrolloRap`) VALUES
('124101-1-220201501-1', 1, 'RAP001', 'Interpretar el diseño de acuerdo con los requerimientos del cliente para el desarrollo del software', 80.00),
('124101-1-220201501-2', 2, 'RAP002', 'Desarrollar el software de acuerdo con el diseño establecido para satisfacer los requerimientos del cliente', 200.00),
('124101-1-220201501-3', 3, 'RAP003', 'Realizar pruebas del software desarrollado de acuerdo con los procedimientos establecidos', 120.00),
('124101-2-220201501-1', 4, 'RA01', 'Analizar los requerimientos del software de acuerdo con las necesidades del cliente', 90.00),
('124101-2-220201501-2', 5, 'RA02', 'Disenar la solución de software aplicando patrones de diseno y buenas prácticas', 120.00),
('124101-2-220201501-3', 6, 'RA03', 'Implementar el software utilizando lenguajes de programación y frameworks apropiados', 180.00),
('124101-2-220201501-4', 7, 'RA04', 'Realizar pruebas de software para garantizar la calidad del producto desarrollado', 60.00),
('124101-2-220201502-1', 15, 'RA05', 'Diagnosticar problemas en sistemas de software en producción', 80.00),
('124101-2-220201502-2', 16, 'RA06', 'Aplicar técnicas de refactoring y optimización de código existente', 120.00),
('124101-2-220201502-3', 17, 'RA07', 'Implementar mejoras y nuevas funcionalidades en sistemas legacy', 120.00),
('124101-2-220201503-1', 21, 'RA08', 'Analizar requerimientos de sistemas de información empresariales', 100.00),
('124101-2-220201503-2', 22, 'RA09', 'Disenar bases de datos relacionales y no relacionales', 130.00),
('124101-2-220201503-3', 23, 'RA10', 'Implementar sistemas de información con arquitectura de microservicios', 150.00),
('228106-1-220201501-1', 8, 'RAP1', 'Interpretar documentación técnica y requerimientos funcionales del software', 70.00),
('228106-1-220201501-2', 9, 'RAP2', 'Codificar aplicaciones siguiendo estándares de programación establecidos', 150.00),
('228106-1-220201501-3', 10, 'RAP3', 'Realizar pruebas básicas y corrección de errores en el código desarrollado', 80.00),
('228106-1-220201501-4', 11, 'RAP4', 'Documentar el código fuente y procesos de desarrollo implementados', 50.00),
('228106-1-220201502-1', 18, 'RAP5', 'Identificar y corregir errores en aplicaciones de software', 90.00),
('228106-1-220201502-2', 19, 'RAP6', 'Realizar mantenimiento preventivo y correctivo de aplicaciones', 100.00),
('228106-1-220201502-3', 20, 'RAP7', 'Actualizar versiones de software y gestionar dependencias', 90.00),
('228106-1-220201504-1', 24, 'RAP8', 'Aplicar conceptos de programación orientada a objetos en proyectos', 120.00),
('228106-1-220201504-2', 25, 'RAP9', 'Desarrollar aplicaciones desktop usando frameworks orientados a objetos', 160.00),
('228106-1-220201504-3', 26, 'RAP10', 'Implementar patrones de diseno en aplicaciones orientadas a objetos', 120.00),
('521240-1-220201501-1', 12, 'R1', 'Aplicar metodologías ágiles en el desarrollo de aplicaciones web complejas', 80.00),
('521240-1-220201501-2', 13, 'R2', 'Implementar arquitecturas de software escalables para aplicaciones web', 120.00),
('521240-1-220201501-3', 14, 'R3', 'Integrar servicios web y APIs REST en aplicaciones web modernas', 80.00),
('521240-1-220201505-1', 27, 'R4', 'Desarrollar interfaces de usuario interactivas con frameworks modernos', 120.00),
('521240-1-220201505-2', 28, 'R5', 'Implementar funcionalidades avanzadas de JavaScript y TypeScript', 120.00),
('521240-1-220201505-3', 29, 'R6', 'Crear aplicaciones web progresivas (PWA) y single page applications (SPA)', 120.00);

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
  MODIFY `codigoRapAutomatico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
