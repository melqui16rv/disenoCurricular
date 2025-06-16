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

-- Insertar competencias de ejemplo para el primer diseño (124101-1)
INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
(
    '124101-1-220201501', '220201501',
    'Comprender textos en inglés en forma escrita y auditiva',
    'Norma de competencia laboral para la comprensión de textos en inglés técnico',
    180.00,
    'Profesional en Lenguas Modernas, Licenciado en Inglés o áreas afines con certificación internacional mínimo B2',
    'Veinticuatro (24) meses de experiencia en docencia de inglés técnico'
),
(
    '124101-1-220501046', '220501046',
    'Aplicar herramientas metodológicas para el desarrollo de proyectos',
    'Norma para la aplicación de metodologías de gestión de proyectos',
    120.00,
    'Profesional en Ingeniería de Sistemas, Administración de Proyectos o áreas afines',
    'Dieciocho (18) meses de experiencia en gestión de proyectos de software'
),
(
    '124101-1-220201048', '220201048',
    'Desarrollar el sistema que cumpla con los requisitos de la solución informática',
    'Norma para el desarrollo de sistemas de información',
    400.00,
    'Profesional en Ingeniería de Sistemas, Ingeniería de Software o Tecnología en Desarrollo de Software',
    'Treinta y seis (36) meses de experiencia en desarrollo de software'
);

-- Insertar RAPs de ejemplo para la primera competencia (124101-1-220201501)
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRap`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '124101-1-220201501-RA1', 'RA1',
    'Encontrar información específica y predecible en escritos sencillos y cotidianos',
    45.00
),
(
    '124101-1-220201501-RA2', 'RA2', 
    'Encontrar vocabulario y expresiones de inglés técnico en artículos de revistas, libros especializados, páginas web, etc.',
    45.00
),
(
    '124101-1-220201501-RA3', 'RA3',
    'Comprender frases y vocabulario habitual sobre temas de interés personal y temas técnicos',
    45.00
),
(
    '124101-1-220201501-RA4', 'RA4',
    'Comprender la idea principal en avisos y mensajes breves, claros y sencillos en inglés técnico',
    45.00
);

-- Insertar RAPs para la segunda competencia (124101-1-220501046)
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRap`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '124101-1-220501046-RA1', 'RA1',
    'Identificar las fases del proyecto aplicando los fundamentos de la gestión de proyectos',
    40.00
),
(
    '124101-1-220501046-RA2', 'RA2',
    'Planificar actividades de acuerdo con los objetivos del proyecto y los recursos disponibles',
    40.00
),
(
    '124101-1-220501046-RA3', 'RA3',
    'Realizar seguimiento y control del proyecto aplicando herramientas de gestión',
    40.00
);

-- Insertar RAPs para la tercera competencia (124101-1-220201048) 
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRap`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '124101-1-220201048-RA1', 'RA1',
    'Interpretar el diseño de software, de acuerdo con las especificaciones del sistema de información',
    80.00
),
(
    '124101-1-220201048-RA2', 'RA2',
    'Desarrollar la interfaz gráfica de usuario de acuerdo con el diseño establecido',
    100.00
),
(
    '124101-1-220201048-RA3', 'RA3',
    'Construir la base de datos, de acuerdo con el diseño establecido para el sistema de información',
    80.00
),
(
    '124101-1-220201048-RA4', 'RA4',
    'Codificar los módulos del software, de acuerdo con las especificaciones del diseño',
    140.00
);

-- Insertar competencia para el segundo diseño (228106-2)
INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
(
    '228106-2-210101019', '210101019',
    'Coordinar el talento humano asignado, de acuerdo con el plan estratégico de la organización',
    'Norma de competencia para la coordinación del talento humano',
    160.00,
    'Profesional en Administración de Empresas, Gestión del Talento Humano o áreas afines',
    'Veinticuatro (24) meses de experiencia en gestión del talento humano'
);

-- Insertar RAPs para la competencia del segundo diseño
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRap`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '228106-2-210101019-RA1', 'RA1',
    'Dirigir el talento humano según la estructura organizacional y las normas de convivencia',
    80.00
),
(
    '228106-2-210101019-RA2', 'RA2',
    'Aplicar procesos de comunicación organizacional de acuerdo con las políticas de la organización',
    80.00
);

-- Insertar competencia para el tercer diseño (233104-1)
INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
(
    '233104-1-220201077', '220201077',
    'Desarrollar algoritmos que permitan resolver problemas sencillos de programación',
    'Norma para el desarrollo de algoritmos de programación',
    200.00,
    'Tecnólogo en Desarrollo de Software, Técnico en Programación o Profesional en áreas afines',
    'Dieciocho (18) meses de experiencia en programación y desarrollo de software'
);

-- Insertar RAPs para la competencia del tercer diseño
INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRap`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '233104-1-220201077-RA1', 'RA1',
    'Interpretar el problema planteado según las especificaciones del cliente',
    50.00
),
(
    '233104-1-220201077-RA2', 'RA2',
    'Diseñar algoritmos según la lógica de programación y las especificaciones del problema',
    75.00
),
(
    '233104-1-220201077-RA3', 'RA3',
    'Codificar algoritmos de acuerdo con la lógica de programación y el lenguaje escogido',
    75.00
);
