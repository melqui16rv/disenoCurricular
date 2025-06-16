CREATE TABLE `diseños`(
    `codigoDiseño` VARCHAR(255), -- CSV
    `codigoPrograma` VARCHAR(255), -- CSV
    `versionPograma` VARCHAR(255), -- CSV
    `nombrePrograma` VARCHAR(255), -- CSV
    `lineaTecnologica` VARCHAR(255), -- FORMS - aqui puede ser muy difil saber loque va a tener por lo que es abierto este campo
    `redTecnologica` VARCHAR(255), -- FORMS
    `redConocimiento` VARCHAR(255), -- FORMS
    `horasDesarrolloLectiva` DECIMAL(10,2), -- FORMS - aqui el usuairo puede poner otras en este campo o meses en el campo "mesesDesarrolloLectiva" pero no en necesario en los dos campos, esto aplica para el caso de horasDesarrolloProductiva
    `horasDesarrolloProductiva` DECIMAL(10,2), -- FORMS
    `mesesDesarrolloLectiva` DECIMAL(10,2), -- FORMS
    `mesesDesarrolloProductiva` DECIMAL(10,2), -- FORMS
    `horasDesarrolloDiseño` DECIMAL(10,2), -- FORMS
    `mesesDesarrolloDiseño` DECIMAL(10,2), -- FORMS
    `nivelAcademicoIngreso` VARCHAR(255), -- FORMS
    `gradoNivelAcademico` INT, -- FORMS
    `formacionTrabajoDesarrolloHumano` ENUM('Si', 'No'), -- FORMS
    `edadMinima` INT,-- FORMS
    `requisitosAdicionales` TEXT, -- FORMS
    PRIMARY KEY(`codigoDiseño`)
);
CREATE TABLE `competencias`(
    `codigoDiseñoCompetencia` VARCHAR(255), -- CSV
    `codigoCompetencia` VARCHAR(255), -- CSV
    `nombreCompetencia` VARCHAR(255), -- CSV
    `normaUnidadCompetencia` TEXT, -- FORMS
    `horasDesarrolloCompetencia` DECIMAL(10,2), -- FORMS
    `requisitosAcademicosInstructor` TEXT, -- FORMS
    `experienciaLaboralInstructor` TEXT, -- FORMS
    PRIMARY KEY(`codigoDiseñoCompetencia`)
);
CREATE TABLE `raps`(
    `codigoDiseñoCompetenciaRap` VARCHAR(255), -- CSV
    `codigoRapAutomatico` int NOT NULL AUTO_INCREMENT, -- CSV
    `codigoRapDiseño` VARCHAR(55), -- FORMS
    `nombreRap` TEXT, -- FORMS
    `horasDesarrolloRap` DECIMAL(10,2), -- FORMS
    PRIMARY KEY(`codigoDiseñoCompetenciaRap`)
);
