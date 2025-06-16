CREATE TABLE `diseños`(
    `codigoDiseño` VARCHAR(255), -- en el registro se debe concatenear: "codigoPrograma{tabla:diseños}-versionPrograma{tabla:diseños}" 
    -- ejemplo final: codigoPrograma=124101
    -- versionPrograma=1
    -- codigoDiseño=124101-1
    `codigoPrograma` VARCHAR(255),
    `versionPrograma` VARCHAR(255),
    --"nuevo campo:
    `nombrePrograma` VARCHAR(255),
    --"
    `lineaTecnologica` VARCHAR(255), -- este campo tiene que ser abierto no se puede con listas desplegables
    `redTecnologica` VARCHAR(255), -- este campo tiene que ser abierto no se puede con listas desplegables
    `redConocimiento` VARCHAR(255), -- este campo tiene que ser abierto no se puede con listas desplegables
    --"nuevo campo:
    `horasDesarrolloLectiva` DECIMAL(10,2), -- este campo se puede poner cualquier valor incluso 0 o null
    `horasDesarrolloProductiva` DECIMAL(10,2), -- este campo se puede poner cualquier valor incluso 0 o null
    `mesesDesarrolloLectiva` DECIMAL(10,2), -- este campo se puede poner cualquier valor incluso 0 o null
    `mesesDesarrolloProductiva` DECIMAL(10,2), -- este campo se puede poner cualquier valor incluso 0 o null
    --"
    `horasDesarrolloDiseño` DECIMAL(10,2), -- es la suma entre: horasDesarrolloLectiva+horasDesarrolloProductiva - si esta en cero o null pues da resultado 0
    `mesesDesarrolloDiseño` DECIMAL(10,2), -- es la suma entre: mesesDesarrolloLectiva+mesesDesarrolloProductiva - si esta en cero o null pues da resultado 0
    `nivelAcademicoIngreso` VARCHAR(255), -- en este campo pon menos en la lista desplegable de los formularios pon los basicos
    `gradoNivelAcademico` INT, -- antes estaba en VARCHAR(255) pero ahora se cambio a INT
    `formacionTrabajoDesarrolloHumano` ENUM('Si', 'No'),
    `edadMinima` INT,
    `requisitosAdicionales` TEXT,
    PRIMARY KEY(`codigoDiseño`)
);
CREATE TABLE `competencias`(
    `codigoDiseñoCompetencia` VARCHAR(255), -- en el registro se debe concatenear: "codigoDiseño{tabla:diseños}-codigoCompetencia{tabla:competencias}"
    -- ejemplo final: codigoDiseño=124101-1
    -- codigoCompetencia=220201501
    -- codigoDiseñoCompetencia=124101-1-220201501
    `codigoCompetencia` VARCHAR(255),
    `nombreCompetencia` VARCHAR(255),
    `normaUnidadCompetencia` TEXT,
    `horasDesarrolloCompetencia` DECIMAL(10,2),
    `requisitosAcademicosInstructor` TEXT,
    `experienciaLaboralInstructor` TEXT,
    PRIMARY KEY(`codigoDiseñoCompetencia`)
);
CREATE TABLE `raps`(
    `codigoDiseñoCompetenciaRap` VARCHAR(255), -- en el registro se debe concatenear: "codigoDiseño{tabla:diseños}-codigoCompetencia{tabla:competencias}-codigoRapAutomatico{tabla:raps}"
    -- ejemplo final: codigoDiseño=124101-1
    -- codigoCompetencia=220201501
    -- codigoRapAutomatico=1
    -- codigoDiseñoCompetenciaRap=124101-1-220201501-1
    `codigoRapAutomatico` int NOT NULL AUTO_INCREMENT,
    `codigoRapDiseño` VARCHAR(55),
    `nombreRap` TEXT,
    `horasDesarrolloRap` DECIMAL(10,2),
    PRIMARY KEY(`codigoDiseñoCompetenciaRap`)
);

-- en los ejemplo se refleja como deben funsionar las llaves..

