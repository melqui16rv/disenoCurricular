-- Base de datos para desarrollo local
-- Ejecutar este script en phpMyAdmin o MySQL Workbench

CREATE DATABASE IF NOT EXISTS `disenos_curriculares` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `disenos_curriculares`;

-- Tabla de diseños curriculares
CREATE TABLE IF NOT EXISTS `diseños` (
    `codigoDiseño` VARCHAR(255) NOT NULL,
    `codigoPrograma` VARCHAR(255) NOT NULL,
    `versionPograma` VARCHAR(255) NOT NULL,
    `nombrePrograma` VARCHAR(255) NOT NULL,
    `lineaTecnologica` VARCHAR(255) DEFAULT NULL,
    `redTecnologica` VARCHAR(255) DEFAULT NULL,
    `redConocimiento` VARCHAR(255) DEFAULT NULL,
    `horasDesarrolloLectiva` DECIMAL(10,2) DEFAULT NULL,
    `horasDesarrolloProductiva` DECIMAL(10,2) DEFAULT NULL,
    `mesesDesarrolloLectiva` DECIMAL(10,2) DEFAULT NULL,
    `mesesDesarrolloProductiva` DECIMAL(10,2) DEFAULT NULL,
    `horasDesarrolloDiseño` DECIMAL(10,2) DEFAULT NULL,
    `mesesDesarrolloDiseño` DECIMAL(10,2) DEFAULT NULL,
    `nivelAcademicoIngreso` VARCHAR(255) DEFAULT NULL,
    `gradoNivelAcademico` INT DEFAULT NULL,
    `formacionTrabajoDesarrolloHumano` ENUM('Si', 'No') DEFAULT NULL,
    `edadMinima` INT DEFAULT NULL,
    `requisitosAdicionales` TEXT DEFAULT NULL,
    PRIMARY KEY (`codigoDiseño`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de competencias
CREATE TABLE IF NOT EXISTS `competencias` (
    `codigoDiseñoCompetencia` VARCHAR(255) NOT NULL,
    `codigoCompetencia` VARCHAR(255) NOT NULL,
    `nombreCompetencia` VARCHAR(255) NOT NULL,
    `normaUnidadCompetencia` TEXT DEFAULT NULL,
    `horasDesarrolloCompetencia` DECIMAL(10,2) DEFAULT NULL,
    `requisitosAcademicosInstructor` TEXT DEFAULT NULL,
    `experienciaLaboralInstructor` TEXT DEFAULT NULL,
    PRIMARY KEY (`codigoDiseñoCompetencia`),
    KEY `idx_codigo_competencia` (`codigoCompetencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de RAPs (Resultados de Aprendizaje)
CREATE TABLE IF NOT EXISTS `raps` (
    `codigoDiseñoCompetenciaRap` VARCHAR(255) NOT NULL,
    `codigoRapAutomatico` INT NOT NULL AUTO_INCREMENT,
    `codigoRapDiseño` VARCHAR(55) DEFAULT NULL,
    `nombreRap` TEXT DEFAULT NULL,
    `horasDesarrolloRap` DECIMAL(10,2) DEFAULT NULL,
    PRIMARY KEY (`codigoDiseñoCompetenciaRap`),
    UNIQUE KEY `idx_auto_increment` (`codigoRapAutomatico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo para pruebas
INSERT INTO `diseños` (`codigoDiseño`, `codigoPrograma`, `versionPograma`, `nombrePrograma`, `lineaTecnologica`, `redTecnologica`, `redConocimiento`, `horasDesarrolloLectiva`, `horasDesarrolloProductiva`, `mesesDesarrolloLectiva`, `mesesDesarrolloProductiva`, `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`, `nivelAcademicoIngreso`, `gradoNivelAcademico`, `formacionTrabajoDesarrolloHumano`, `edadMinima`, `requisitosAdicionales`) VALUES
('124101-1', '124101', '1', 'Tecnología en Desarrollo de Software', 'Tecnologías de la Información y las Comunicaciones', 'Red de Tecnologías de Información y Comunicación', 'Red de Conocimiento en Informática', 1760.00, 880.00, NULL, NULL, 2640.00, NULL, 'Bachiller', 11, 'Si', 16, 'Conocimientos básicos en matemáticas y lógica');

INSERT INTO `competencias` (`codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`, `normaUnidadCompetencia`, `horasDesarrolloCompetencia`, `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`) VALUES
('124101-1-220201501', '220201501', 'Programar software de acuerdo con el diseño realizado', 'Norma de competencia laboral NCL 220201501', 400.00, 'Tecnólogo en áreas relacionadas con desarrollo de software', 'Mínimo 2 años en desarrollo de software'),
('124101-1-220201502', '220201502', 'Realizar mantenimiento de software de acuerdo con los procedimientos establecidos', 'Norma de competencia laboral NCL 220201502', 300.00, 'Tecnólogo en áreas relacionadas con sistemas', 'Mínimo 2 años en soporte y mantenimiento');

INSERT INTO `raps` (`codigoDiseñoCompetenciaRap`, `codigoRapDiseño`, `nombreRap`, `horasDesarrolloRap`) VALUES
('124101-1-220201501-1', 'RAP001', 'Interpretar el diseño de acuerdo con los requerimientos del cliente para el desarrollo del software', 80.00),
('124101-1-220201501-2', 'RAP002', 'Desarrollar el software de acuerdo con el diseño establecido para satisfacer los requerimientos del cliente', 200.00),
('124101-1-220201501-3', 'RAP003', 'Realizar pruebas del software desarrollado de acuerdo con los procedimientos establecidos', 120.00);
