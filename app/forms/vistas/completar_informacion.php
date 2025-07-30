<?php
// Vista para completar información faltante en diseños, competencias y RAPs

// Verificar que $metodos esté disponible
if (!isset($metodos)) {
    die('Error: Variable $metodos no está disponible. Asegúrate de que el archivo se incluya desde index.php');
}

// Incluir funciones auxiliares para AJAX y generación de HTML
include_once 'completar_informacion_funciones.php';
// Incluir filtros específicos para completar información
include_once 'filtros_completar_informacion.php';

// Obtener filtros y paginación
$filtro_seccion = $_GET['seccion'] ?? 'todas';
$filtro_busqueda = $_GET['busqueda'] ?? '';

// Filtros adicionales específicos para completar información
$filtro_horas_min = $_GET['horas_min'] ?? '';
$filtro_horas_max = $_GET['horas_max'] ?? '';
$filtro_tipo_programa = $_GET['tipo_programa'] ?? '';
$filtro_nivel_academico = $_GET['nivel_academico'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Paginación independiente por sección
$pagina_disenos = max(1, (int)($_GET['pagina_disenos'] ?? 1));
$pagina_competencias = max(1, (int)($_GET['pagina_competencias'] ?? 1));
$pagina_raps = max(1, (int)($_GET['pagina_raps'] ?? 1));

// Registros por página independientes por sección
$registros_disenos = (int)($_GET['registros_disenos'] ?? 10);
$registros_competencias = (int)($_GET['registros_competencias'] ?? 10);
$registros_raps = (int)($_GET['registros_raps'] ?? 10);

// Validar registros por página para cada sección
$registros_permitidos = [5, 10, 25, 50, 100];
if (!in_array($registros_disenos, $registros_permitidos)) {
    $registros_disenos = 10;
}
if (!in_array($registros_competencias, $registros_permitidos)) {
    $registros_competencias = 10;
}
if (!in_array($registros_raps, $registros_permitidos)) {
    $registros_raps = 10;
}

// Función para validar y corregir páginas fuera de rango
function validarPagina($pagina_solicitada, $total_registros, $registros_por_pagina) {
    if ($total_registros == 0) {
        return 1;
    }
    
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $pagina_corregida = max(1, min($pagina_solicitada, $total_paginas));
    
    return $pagina_corregida;
}

// Función para detectar campos faltantes en diseños con filtros avanzados
function obtenerDisenosConCamposFaltantes($metodos, $filtros_array = [], $pagina = 1, $registros_por_pagina = 10) {
    try {
        $sql = "SELECT * FROM diseños WHERE 1=1";
        $params = [];

        // Filtro básico de búsqueda
        if (!empty($filtros_array['busqueda'])) {
            $sql .= " AND (codigoDiseño LIKE ? OR nombrePrograma LIKE ?)";
            $params[] = "%{$filtros_array['busqueda']}%";
            $params[] = "%{$filtros_array['busqueda']}%";
        }

        // Filtro por tipo de programa
        if (!empty($filtros_array['tipo_programa'])) {
            $sql .= " AND tipoPrograma = ?";
            $params[] = $filtros_array['tipo_programa'];
        }

        // Filtro por nivel académico
        if (!empty($filtros_array['nivel_academico'])) {
            $sql .= " AND nivelAcademicoIngreso = ?";
            $params[] = $filtros_array['nivel_academico'];
        }

        // Filtro por rango de horas totales
        if (!empty($filtros_array['horas_min']) && $filtros_array['horas_min'] > 0) {
            $sql .= " AND (horasDesarrolloLectiva + horasDesarrolloProductiva) >= ?";
            $params[] = $filtros_array['horas_min'];
        }

        if (!empty($filtros_array['horas_max']) && $filtros_array['horas_max'] > 0) {
            $sql .= " AND (horasDesarrolloLectiva + horasDesarrolloProductiva) <= ?";
            $params[] = $filtros_array['horas_max'];
        }

        // Obtener todos los registros para filtrar campos faltantes
        $diseños = $metodos->ejecutarConsulta($sql, $params);
        $diseñosConFaltantes = [];

        foreach ($diseños as $diseño) {
            $camposFaltantes = [];

            // VALIDAR SOLO LOS CAMPOS ESPECIFICADOS EXACTAMENTE

            // 1. Campos de tecnología y conocimiento
            if (empty($diseño['lineaTecnologica'])) $camposFaltantes[] = 'Línea Tecnológica';
            if (empty($diseño['redTecnologica'])) $camposFaltantes[] = 'Red Tecnológica';
            if (empty($diseño['redConocimiento'])) $camposFaltantes[] = 'Red de Conocimiento';

            // 2. Validación de horas y meses (solo si NINGUNO de los dos sistemas está completo)
            $horasLectiva = (float)($diseño['horasDesarrolloLectiva'] ?? 0);
            $horasProductiva = (float)($diseño['horasDesarrolloProductiva'] ?? 0);
            $mesesLectiva = (float)($diseño['mesesDesarrolloLectiva'] ?? 0);
            $mesesProductiva = (float)($diseño['mesesDesarrolloProductiva'] ?? 0);

            $tieneHorasCompletas = ($horasLectiva > 0 && $horasProductiva > 0);
            $tieneMesesCompletos = ($mesesLectiva > 0 && $mesesProductiva > 0);

            if (!$tieneHorasCompletas && !$tieneMesesCompletos) {
                $camposFaltantes[] = 'Debe completar HORAS (Lectivas + Productivas) O MESES (Lectivos + Productivos)';
            }

            // 3. Campos académicos y requisitos
            if (empty($diseño['nivelAcademicoIngreso'])) $camposFaltantes[] = 'Nivel Académico de Ingreso';
            if (empty($diseño['gradoNivelAcademico']) || $diseño['gradoNivelAcademico'] == 0) $camposFaltantes[] = 'Grado del Nivel Académico';
            if (empty($diseño['formacionTrabajoDesarrolloHumano'])) $camposFaltantes[] = 'Formación en Trabajo y Desarrollo Humano';
            if (empty($diseño['edadMinima']) || $diseño['edadMinima'] == 0) $camposFaltantes[] = 'Edad Mínima';
            if (empty($diseño['requisitosAdicionales'])) $camposFaltantes[] = 'Requisitos Adicionales';

            if (!empty($camposFaltantes)) {
                $diseño['camposFaltantes'] = $camposFaltantes;
                $diseñosConFaltantes[] = $diseño;
            }
        }

        // Aplicar paginación después del filtrado
        $total_registros = count($diseñosConFaltantes);
        $total_paginas = ceil($total_registros / $registros_por_pagina);
        
        // Validar y corregir página fuera de rango
        $pagina_corregida = validarPagina($pagina, $total_registros, $registros_por_pagina);
        
        $offset = ($pagina_corregida - 1) * $registros_por_pagina;
        $diseñosPaginados = array_slice($diseñosConFaltantes, $offset, $registros_por_pagina);

        return [
            'datos' => $diseñosPaginados,
            'total_registros' => $total_registros,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_corregida,
            'registros_por_pagina' => $registros_por_pagina
        ];
    } catch (Exception $e) {
        error_log("Error en obtenerDisenosConCamposFaltantes: " . $e->getMessage());
        return [
            'datos' => [],
            'total_registros' => 0,
            'total_paginas' => 0,
            'pagina_actual' => 1,
            'registros_por_pagina' => $registros_por_pagina
        ];
    }
}

// Función para detectar campos faltantes en competencias con filtros avanzados
function obtenerCompetenciasConCamposFaltantes($metodos, $filtros_array = [], $pagina = 1, $registros_por_pagina = 10) {
    $sql = "SELECT c.*, d.nombrePrograma 
            FROM competencias c 
            LEFT JOIN diseños d ON SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2) = d.codigoDiseño 
            WHERE 1=1";
    $params = [];

    // Filtro básico de búsqueda
    if (!empty($filtros_array['busqueda'])) {
        $sql .= " AND (c.codigoDiseñoCompetenciaReporte LIKE ? OR c.nombreCompetencia LIKE ? OR d.nombrePrograma LIKE ?)";
        $params[] = "%{$filtros_array['busqueda']}%";
        $params[] = "%{$filtros_array['busqueda']}%";
        $params[] = "%{$filtros_array['busqueda']}%";
    }

    // Filtro por rango de horas
    if (!empty($filtros_array['horas_min']) && $filtros_array['horas_min'] > 0) {
        $sql .= " AND c.horasDesarrolloCompetencia >= ?";
        $params[] = $filtros_array['horas_min'];
    }

    if (!empty($filtros_array['horas_max']) && $filtros_array['horas_max'] > 0) {
        $sql .= " AND c.horasDesarrolloCompetencia <= ?";
        $params[] = $filtros_array['horas_max'];
    }

    $competencias = $metodos->ejecutarConsulta($sql, $params);
    $competenciasConFaltantes = [];

    foreach ($competencias as $competencia) {
        $camposFaltantes = [];

        // VALIDAR SOLO LOS CAMPOS ESPECIFICADOS EXACTAMENTE

        // 1. Nombre de la competencia
        if (empty($competencia['nombreCompetencia'])) $camposFaltantes[] = 'Nombre de la Competencia';

        // 2. Norma unidad competencia
        if (empty($competencia['normaUnidadCompetencia'])) $camposFaltantes[] = 'Norma Unidad Competencia';

        // 3. Horas de desarrollo (debe ser > 0)
        $horas = (float)($competencia['horasDesarrolloCompetencia'] ?? 0);
        if ($horas <= 0) $camposFaltantes[] = 'Horas de Desarrollo (debe ser > 0)';

        // 4. Requisitos académicos del instructor
        if (empty($competencia['requisitosAcademicosInstructor'])) $camposFaltantes[] = 'Requisitos Académicos del Instructor';

        // 5. Experiencia laboral del instructor
        if (empty($competencia['experienciaLaboralInstructor'])) $camposFaltantes[] = 'Experiencia Laboral del Instructor';

        if (!empty($camposFaltantes)) {
            $competencia['camposFaltantes'] = $camposFaltantes;
            $competenciasConFaltantes[] = $competencia;
        }
    }

    // Aplicar paginación después del filtrado
    $total_registros = count($competenciasConFaltantes);
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    // Validar y corregir página fuera de rango
    $pagina_corregida = validarPagina($pagina, $total_registros, $registros_por_pagina);
    
    $offset = ($pagina_corregida - 1) * $registros_por_pagina;
    $competenciasPaginadas = array_slice($competenciasConFaltantes, $offset, $registros_por_pagina);

    return [
        'datos' => $competenciasPaginadas,
        'total_registros' => $total_registros,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina_corregida,
        'registros_por_pagina' => $registros_por_pagina
    ];
}

// Función para detectar campos faltantes en RAPs con filtros avanzados
function obtenerRapsConCamposFaltantes($metodos, $filtros_array = [], $pagina = 1, $registros_por_pagina = 10) {
    $sql = "SELECT r.*, c.nombreCompetencia, d.nombrePrograma 
            FROM raps r 
            LEFT JOIN competencias c ON SUBSTRING_INDEX(r.codigoDiseñoCompetenciaReporteRap, '-', 3) = c.codigoDiseñoCompetenciaReporte 
            LEFT JOIN diseños d ON SUBSTRING_INDEX(r.codigoDiseñoCompetenciaReporteRap, '-', 2) = d.codigoDiseño 
            WHERE 1=1";
    $params = [];

    // Filtro básico de búsqueda
    if (!empty($filtros_array['busqueda'])) {
        $sql .= " AND (r.codigoDiseñoCompetenciaReporteRap LIKE ? OR r.nombreRap LIKE ? OR c.nombreCompetencia LIKE ? OR d.nombrePrograma LIKE ?)";
        $params[] = "%{$filtros_array['busqueda']}%";
        $params[] = "%{$filtros_array['busqueda']}%";
        $params[] = "%{$filtros_array['busqueda']}%";
        $params[] = "%{$filtros_array['busqueda']}%";
    }

    // Filtro por rango de horas
    if (!empty($filtros_array['horas_min']) && $filtros_array['horas_min'] > 0) {
        $sql .= " AND r.horasDesarrolloRap >= ?";
        $params[] = $filtros_array['horas_min'];
    }

    if (!empty($filtros_array['horas_max']) && $filtros_array['horas_max'] > 0) {
        $sql .= " AND r.horasDesarrolloRap <= ?";
        $params[] = $filtros_array['horas_max'];
    }

    $raps = $metodos->ejecutarConsulta($sql, $params);
    $rapsConFaltantes = [];

    foreach ($raps as $rap) {
        $camposFaltantes = [];

        // VALIDAR SOLO LOS CAMPOS ESPECIFICADOS EXACTAMENTE

        // 1. Nombre del RAP
        if (empty($rap['nombreRap'])) $camposFaltantes[] = 'Nombre del RAP';

        // 2. Horas de desarrollo (debe ser > 0)
        $horas = (float)($rap['horasDesarrolloRap'] ?? 0);
        if ($horas <= 0) $camposFaltantes[] = 'Horas de Desarrollo (debe ser > 0)';

        if (!empty($camposFaltantes)) {
            $rap['camposFaltantes'] = $camposFaltantes;
            $rapsConFaltantes[] = $rap;
        }
    }

    // Aplicar paginación después del filtrado
    $total_registros = count($rapsConFaltantes);
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    // Validar y corregir página fuera de rango
    $pagina_corregida = validarPagina($pagina, $total_registros, $registros_por_pagina);
    
    $offset = ($pagina_corregida - 1) * $registros_por_pagina;
    $rapsPaginados = array_slice($rapsConFaltantes, $offset, $registros_por_pagina);

    return [
        'datos' => $rapsPaginados,
        'total_registros' => $total_registros,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina_corregida,
        'registros_por_pagina' => $registros_por_pagina
    ];
}

// Función para generar controles de paginación mejorada con independencia por sección
function generarPaginacion($resultado, $filtros_array, $seccion_id = '') {
    if ($resultado['total_paginas'] <= 1) {
        return '';
    }
    
    $pagina_actual = $resultado['pagina_actual'];
    $total_paginas = $resultado['total_paginas'];
    $total_registros = $resultado['total_registros'];
    $registros_por_pagina = $resultado['registros_por_pagina'];
    
    $inicio_registro = (($pagina_actual - 1) * $registros_por_pagina) + 1;
    $fin_registro = min($pagina_actual * $registros_por_pagina, $total_registros);
    
    // Construir query string EXCLUYENDO solo la página específica de la sección actual
    $query_params = [];
    $exclude_params = [
        'pagina_' . $seccion_id  // Solo excluir la página de esta sección
    ];
    
    foreach ($filtros_array as $key => $value) {
        if (!empty($value) && !in_array($key, $exclude_params)) {
            $query_params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    
    // Agregar parámetros de otras secciones Y registros de la sección actual para mantener su estado
    $current_url_params = $_GET;
    foreach ($current_url_params as $key => $value) {
        // Incluir todos los parámetros de paginación y registros EXCEPTO la página de la sección actual
        if ((strpos($key, 'pagina_') === 0 || strpos($key, 'registros_') === 0) && !in_array($key, $exclude_params) && !empty($value)) {
            $query_params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    
    $base_url = '?' . implode('&', $query_params);
    $separator = empty($query_params) ? '?' : '&';
    
    $html = '<div class="pagination-container" data-section="' . $seccion_id . '">';
    
    // Información de registros con opciones de visualización
    $html .= '<div class="pagination-info-extended">';
    $html .= '<div class="pagination-info">';
    $html .= "Mostrando {$inicio_registro}-{$fin_registro} de {$total_registros} registros";
    $html .= '</div>';
    
    // Selector de registros por página
    $html .= '<div class="records-per-page">';
    $html .= '<select onchange="cambiarRegistrosPorPaginaAjax(this.value, \'' . $seccion_id . '\')" class="records-selector">';
    foreach ([5, 10, 25, 50, 100] as $option) {
        $selected = ($option == $registros_por_pagina) ? 'selected' : '';
        $html .= "<option value='{$option}' {$selected}>{$option} por página</option>";
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Controles de paginación
    $html .= '<div class="pagination-controls">';
    
    // Botón Primera página
    if ($pagina_actual > 3) {
        $html .= "<a href='#' data-page='1' data-section='{$seccion_id}' class='page-btn ajax-page-btn first-btn' title='Primera página'>";
        $html .= '<i class="fas fa-angle-double-left"></i></a>';
    }
    
    // Botón Anterior
    if ($pagina_actual > 1) {
        $prev = $pagina_actual - 1;
        $html .= "<a href='#' data-page='{$prev}' data-section='{$seccion_id}' class='page-btn ajax-page-btn prev-btn' title='Página anterior'>";
        $html .= '<i class="fas fa-chevron-left"></i> Anterior</a>';
    }
    
    // Números de página
    $inicio = max(1, $pagina_actual - 2);
    $fin = min($total_paginas, $pagina_actual + 2);
    
    if ($inicio > 1) {
        $html .= "<a href='#' data-page='1' data-section='{$seccion_id}' class='page-btn ajax-page-btn'>1</a>";
        if ($inicio > 2) {
            $html .= '<span class="page-ellipsis">...</span>';
        }
    }
    
    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $pagina_actual) ? 'active' : '';
        $html .= "<a href='#' data-page='{$i}' data-section='{$seccion_id}' class='page-btn ajax-page-btn {$active}'>{$i}</a>";
    }
    
    if ($fin < $total_paginas) {
        if ($fin < $total_paginas - 1) {
            $html .= '<span class="page-ellipsis">...</span>';
        }
        $html .= "<a href='#' data-page='{$total_paginas}' data-section='{$seccion_id}' class='page-btn ajax-page-btn'>{$total_paginas}</a>";
    }
    
    // Botón Siguiente
    if ($pagina_actual < $total_paginas) {
        $next = $pagina_actual + 1;
        $html .= "<a href='#' data-page='{$next}' data-section='{$seccion_id}' class='page-btn ajax-page-btn next-btn' title='Página siguiente'>";
        $html .= 'Siguiente <i class="fas fa-chevron-right"></i></a>';
    }
    
    // Botón Última página
    if ($pagina_actual < $total_paginas - 2) {
        $html .= "<a href='#' data-page='{$total_paginas}' data-section='{$seccion_id}' class='page-btn ajax-page-btn last-btn' title='Última página'>";
        $html .= '<i class="fas fa-angle-double-right"></i></a>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

// Construir array de filtros para URLs
$filtros_array = [
    'accion' => 'completar_informacion',
    'seccion' => $filtro_seccion,
    'busqueda' => $filtro_busqueda,
    'horas_min' => $filtro_horas_min,
    'horas_max' => $filtro_horas_max,
    'tipo_programa' => $filtro_tipo_programa,
    'nivel_academico' => $filtro_nivel_academico,
    'estado' => $filtro_estado,
    'fecha_desde' => $filtro_fecha_desde,
    'fecha_hasta' => $filtro_fecha_hasta
];

// Obtener datos según filtros con paginación independiente por sección
$resultadoDiseños = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_disenos];
$resultadoCompetencias = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_competencias];
$resultadoRaps = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_raps];

if ($filtro_seccion === 'todas' || $filtro_seccion === 'disenos') {
    $resultadoDiseños = obtenerDisenosConCamposFaltantes($metodos, $filtros_array, $pagina_disenos, $registros_disenos);
}

if ($filtro_seccion === 'todas' || $filtro_seccion === 'competencias') {
    $resultadoCompetencias = obtenerCompetenciasConCamposFaltantes($metodos, $filtros_array, $pagina_competencias, $registros_competencias);
}

if ($filtro_seccion === 'todas' || $filtro_seccion === 'raps') {
    $resultadoRaps = obtenerRapsConCamposFaltantes($metodos, $filtros_array, $pagina_raps, $registros_raps);
}

// Extraer datos para compatibilidad
$diseñosConFaltantes = $resultadoDiseños['datos'];
$competenciasConFaltantes = $resultadoCompetencias['datos'];
$rapsConFaltantes = $resultadoRaps['datos'];

// Calcular estadísticas (totales sin paginación)
$totalDiseñosFaltantes = $resultadoDiseños['total_registros'];
$totalCompetenciasFaltantes = $resultadoCompetencias['total_registros'];
$totalRapsFaltantes = $resultadoRaps['total_registros'];
$totalRegistrosFaltantes = $totalDiseñosFaltantes + $totalCompetenciasFaltantes + $totalRapsFaltantes;
?>

<div class="completar-informacion-container">
    <div class="header-section">
        <h2><i class="fas fa-clipboard-check"></i> Completar Información Faltante</h2>
        <p>Identifica y completa los campos obligatorios que faltan en diseños, competencias y RAPs</p>
    </div>

    <!-- Panel de estadísticas -->
    <div class="statistics-panel">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalRegistrosFaltantes; ?></h3>
                <p>Total Registros con Información Faltante</p>
            </div>
        </div>

        <div class="stat-card disenos">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalDiseñosFaltantes; ?></h3>
                <p>Diseños con Campos Faltantes</p>
            </div>
        </div>

        <div class="stat-card competencias">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalCompetenciasFaltantes; ?></h3>
                <p>Competencias con Campos Faltantes</p>
            </div>
        </div>

        <div class="stat-card raps">
            <div class="stat-icon">
                <i class="fas fa-list-ul"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalRapsFaltantes; ?></h3>
                <p>RAPs con Campos Faltantes</p>
            </div>
        </div>
    </div>

    <!-- Filtros específicos para completar información -->
    <?php 
    $filtros_para_render = [
        'seccion' => $filtro_seccion,
        'busqueda' => $filtro_busqueda,
        'horas_min' => $filtro_horas_min,
        'horas_max' => $filtro_horas_max,
        'tipo_programa' => $filtro_tipo_programa,
        'nivel_academico' => $filtro_nivel_academico,
        'estado' => $filtro_estado,
        'fecha_desde' => $filtro_fecha_desde,
        'fecha_hasta' => $filtro_fecha_hasta
    ];
    
    echo generarFiltrosCompletarInformacion($filtros_para_render, $totalRegistrosFaltantes, 1, 10, 1);
    ?>

    <!-- Resultados -->
    <div class="results-section">
        <?php if ($totalRegistrosFaltantes === 0): ?>
            <div class="no-results">
                <i class="fas fa-check-circle"></i>
                <h3>¡Excelente!</h3>
                <p>No se encontraron registros con información faltante.</p>
            </div>
        <?php else: ?>

            <!-- Diseños con campos faltantes -->
            <?php if (!empty($diseñosConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'disenos')): ?>
                <div class="section-results" id="seccion-disenos">
                    <h3><i class="fas fa-graduation-cap"></i> Diseños con Campos Faltantes (<?php echo $totalDiseñosFaltantes; ?>)</h3>
                    
                    <!-- Paginación superior -->
                    <?php echo generarPaginacion($resultadoDiseños, $filtros_array, 'disenos'); ?>
                    
                    <!-- Controles de búsqueda dentro de la tabla -->
                    <div class="table-controls" data-table="disenos">
                        <div class="table-search-container">
                            <div class="search-group">
                                <label for="search-disenos">Buscar en tabla:</label>
                                <input type="text" id="search-disenos" class="table-search-input" placeholder="Buscar por código, programa, versión..." data-target="tabla-disenos">
                            </div>
                            <div class="filter-group">
                                <label for="filter-disenos">Buscar en:</label>
                                <select id="filter-disenos" class="table-filter-column">
                                    <option value="all">Todas las columnas</option>
                                    <option value="0">Código</option>
                                    <option value="1">Programa</option>
                                    <option value="2">Versión</option>
                                    <option value="3">Campos Faltantes</option>
                                </select>
                            </div>
                            <div class="sort-group">
                                <label for="sort-disenos">Ordenar por:</label>
                                <select id="sort-disenos" class="table-sort-column">
                                    <option value="0-asc">Código (A-Z)</option>
                                    <option value="0-desc">Código (Z-A)</option>
                                    <option value="1-asc">Programa (A-Z)</option>
                                    <option value="1-desc">Programa (Z-A)</option>
                                    <option value="2-asc">Versión (A-Z)</option>
                                    <option value="2-desc">Versión (Z-A)</option>
                                </select>
                            </div>
                            <button type="button" class="clear-table-filters" data-target="disenos">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                        <div class="table-info">
                            <span class="visible-rows">Mostrando <span class="visible-count">0</span> de <span class="total-count">0</span> registros</span>
                        </div>
                    </div>
                    
                    <div class="results-table" id="tabla-disenos">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Programa</th>
                                    <th>Versión</th>
                                    <th>Campos Faltantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($diseñosConFaltantes as $diseño): ?>
                                    <tr>
                                        <td class="codigo"><?php echo htmlspecialchars($diseño['codigoDiseño']); ?></td>
                                        <td class="programa"><?php echo htmlspecialchars($diseño['nombrePrograma'] ?? 'Sin nombre'); ?></td>
                                        <td class="version"><?php echo htmlspecialchars($diseño['versionPrograma'] ?? 'Sin versión'); ?></td>
                                        <td class="campos-faltantes">
                                            <div class="missing-fields">
                                                <?php foreach ($diseño['camposFaltantes'] as $campo): ?>
                                                    <span class="missing-field"><?php echo htmlspecialchars($campo); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="actions">
                                            <a href="?accion=completar&tipo=disenos&codigo=<?php echo urlencode($diseño['codigoDiseño']); ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Completar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación inferior -->
                    <?php echo generarPaginacion($resultadoDiseños, $filtros_array, 'disenos'); ?>
                </div>
            <?php endif; ?>

            <!-- Competencias con campos faltantes -->
            <?php if (!empty($competenciasConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'competencias')): ?>
                <div class="section-results" id="seccion-competencias">
                    <h3><i class="fas fa-tasks"></i> Competencias con Campos Faltantes (<?php echo count($competenciasConFaltantes); ?>)</h3>
                    
                    <!-- Controles de paginación superiores para competencias -->
                    <?php echo generarPaginacion($resultadoCompetencias, $filtros_array, 'competencias'); ?>
                    
                    <!-- Controles de búsqueda dentro de la tabla de competencias -->
                    <div class="table-controls" data-table="competencias">
                        <div class="table-search-container">
                            <div class="search-group">
                                <label for="search-competencias">Buscar en tabla:</label>
                                <input type="text" id="search-competencias" class="table-search-input" placeholder="Buscar por código, competencia, programa..." data-target="tabla-competencias">
                            </div>
                            <div class="filter-group">
                                <label for="filter-competencias">Buscar en:</label>
                                <select id="filter-competencias" class="table-filter-column">
                                    <option value="all">Todas las columnas</option>
                                    <option value="0">Código</option>
                                    <option value="1">Competencia</option>
                                    <option value="2">Programa</option>
                                    <option value="3">Campos Faltantes</option>
                                </select>
                            </div>
                            <div class="sort-group">
                                <label for="sort-competencias">Ordenar por:</label>
                                <select id="sort-competencias" class="table-sort-column">
                                    <option value="0-asc">Código (A-Z)</option>
                                    <option value="0-desc">Código (Z-A)</option>
                                    <option value="1-asc">Competencia (A-Z)</option>
                                    <option value="1-desc">Competencia (Z-A)</option>
                                    <option value="2-asc">Programa (A-Z)</option>
                                    <option value="2-desc">Programa (Z-A)</option>
                                </select>
                            </div>
                            <button type="button" class="clear-table-filters" data-target="competencias">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                        <div class="table-info">
                            <span class="visible-rows">Mostrando <span class="visible-count">0</span> de <span class="total-count">0</span> registros</span>
                        </div>
                    </div>
                    
                    <div class="results-table" id="tabla-competencias">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Competencia</th>
                                    <th>Programa</th>
                                    <th>Campos Faltantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($competenciasConFaltantes as $competencia): ?>
                                    <tr>
                                        <td class="codigo"><?php echo htmlspecialchars($competencia['codigoDiseñoCompetenciaReporte']); ?></td>
                                        <td class="competencia"><?php echo htmlspecialchars($competencia['nombreCompetencia'] ?? 'Sin nombre'); ?></td>
                                        <td class="programa"><?php echo htmlspecialchars($competencia['nombrePrograma'] ?? 'Sin programa'); ?></td>
                                        <td class="campos-faltantes">
                                            <div class="missing-fields">
                                                <?php foreach ($competencia['camposFaltantes'] as $campo): ?>
                                                    <span class="missing-field"><?php echo htmlspecialchars($campo); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="actions">
                                            <a href="?accion=completar&tipo=competencias&codigo=<?php echo urlencode($competencia['codigoDiseñoCompetenciaReporte']); ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Completar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Controles de paginación para competencias -->
                    <?php echo generarPaginacion($resultadoCompetencias, $filtros_array, 'competencias'); ?>
                </div>
            <?php endif; ?>

            <!-- RAPs con campos faltantes -->
            <?php if (!empty($rapsConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'raps')): ?>
                <div class="section-results" id="seccion-raps">
                    <h3><i class="fas fa-list-ul"></i> RAPs con Campos Faltantes (<?php echo count($rapsConFaltantes); ?>)</h3>
                    
                    <!-- Controles de paginación superiores para RAPs -->
                    <?php echo generarPaginacion($resultadoRaps, $filtros_array, 'raps'); ?>
                    
                    <!-- Controles de búsqueda dentro de la tabla de RAPs -->
                    <div class="table-controls" data-table="raps">
                        <div class="table-search-container">
                            <div class="search-group">
                                <label for="search-raps">Buscar en tabla:</label>
                                <input type="text" id="search-raps" class="table-search-input" placeholder="Buscar por código, RAP, competencia..." data-target="tabla-raps">
                            </div>
                            <div class="filter-group">
                                <label for="filter-raps">Buscar en:</label>
                                <select id="filter-raps" class="table-filter-column">
                                    <option value="all">Todas las columnas</option>
                                    <option value="0">Código</option>
                                    <option value="1">RAP</option>
                                    <option value="2">Competencia</option>
                                    <option value="3">Programa</option>
                                    <option value="4">Campos Faltantes</option>
                                </select>
                            </div>
                            <div class="sort-group">
                                <label for="sort-raps">Ordenar por:</label>
                                <select id="sort-raps" class="table-sort-column">
                                    <option value="0-asc">Código (A-Z)</option>
                                    <option value="0-desc">Código (Z-A)</option>
                                    <option value="1-asc">RAP (A-Z)</option>
                                    <option value="1-desc">RAP (Z-A)</option>
                                    <option value="2-asc">Competencia (A-Z)</option>
                                    <option value="2-desc">Competencia (Z-A)</option>
                                    <option value="3-asc">Programa (A-Z)</option>
                                    <option value="3-desc">Programa (Z-A)</option>
                                </select>
                            </div>
                            <button type="button" class="clear-table-filters" data-target="raps">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                        <div class="table-info">
                            <span class="visible-rows">Mostrando <span class="visible-count">0</span> de <span class="total-count">0</span> registros</span>
                        </div>
                    </div>
                    
                    <div class="results-table" id="tabla-raps">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>RAP</th>
                                    <th>Competencia</th>
                                    <th>Programa</th>
                                    <th>Campos Faltantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rapsConFaltantes as $rap): ?>
                                    <tr>
                                        <td class="codigo"><?php echo htmlspecialchars($rap['codigoDiseñoCompetenciaReporteRap']); ?></td>
                                        <td class="rap"><?php echo htmlspecialchars($rap['nombreRap'] ?? 'Sin nombre'); ?></td>
                                        <td class="competencia"><?php echo htmlspecialchars($rap['nombreCompetencia'] ?? 'Sin competencia'); ?></td>
                                        <td class="programa"><?php echo htmlspecialchars($rap['nombrePrograma'] ?? 'Sin programa'); ?></td>
                                        <td class="campos-faltantes">
                                            <div class="missing-fields">
                                                <?php foreach ($rap['camposFaltantes'] as $campo): ?>
                                                    <span class="missing-field"><?php echo htmlspecialchars($campo); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="actions">
                                            <a href="?accion=completar&tipo=raps&codigo=<?php echo urlencode($rap['codigoDiseñoCompetenciaReporteRap']); ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Completar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Controles de paginación para RAPs -->
                    <?php echo generarPaginacion($resultadoRaps, $filtros_array, 'raps'); ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<style>
.completar-informacion-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.header-section {
    text-align: center;
    margin-bottom: 2rem;
}

.header-section h2 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 2rem;
}

.header-section p {
    color: #7f8c8d;
    font-size: 1.1rem;
}

/* Panel de estadísticas */
.statistics-panel {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.total {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card.disenos {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card.competencias {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stat-card.raps {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.stat-content h3 {
    font-size: 2rem;
    margin: 0;
    font-weight: bold;
}

.stat-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Filtros */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Indicador de filtros activos */
.active-filters-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 8px;
    font-weight: 600;
}

.filter-clear-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
    margin-left: auto;
}

.filter-clear-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
}

.filter-group select,
.filter-group input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.9rem;
    min-width: 200px;
}

.filter-actions {
    grid-column: 1 / -1;
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1rem;
}

.btn-filter,
.btn-reset {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-filter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-reset {
    background: #e74c3c;
    color: white;
}

.btn-reset:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

/* Estilos adicionales para filtros de tabla */
.table-row-highlight {
    background-color: #fff3cd !important;
}

.table-row-hidden {
    display: none !important;
}

.sort-indicator {
    font-weight: bold;
    color: #007bff;
    margin-left: 5px;
}

.no-results-message {
    text-align: center;
    padding: 2rem;
    color: #666;
    font-style: italic;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 1rem 0;
}

.clear-table-filters {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.clear-table-filters:hover {
    background: #c82333;
    transform: translateY(-1px);
}

/* Resultados */
.results-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.no-results {
    text-align: center;
    padding: 3rem;
    color: #27ae60;
}

.no-results i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.section-results {
    margin-bottom: 2rem;
}

.section-results h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 1.3rem;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 0.5rem;
}

.results-table {
    overflow-x: auto;
}

.results-table table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.results-table th,
.results-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #ecf0f1;
}

.results-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.results-table tr:hover {
    background: #f8f9fa;
}

.codigo {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #3498db;
}

.missing-fields {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.missing-field {
    background: #fff5f5;
    color: #e53e3e;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    border: 1px solid #fed7d7;
}

.btn-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Paginación mejorada */
.pagination-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.pagination-info-extended {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.records-per-page {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.records-selector {
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 0.85rem;
    background: white;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.page-btn {
    padding: 0.5rem 0.75rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    min-width: 40px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.page-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.page-btn.first-btn,
.page-btn.last-btn {
    font-weight: bold;
}

.page-btn.prev-btn,
.page-btn.next-btn {
    padding: 0.5rem 1rem;
    font-weight: 600;
}

.page-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.page-btn.active:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.page-btn.active {
    background: #2ecc71;
}

.page-ellipsis {
    align-self: center;
    color: #7f8c8d;
    font-size: 1.2rem;
}

/* Controles de búsqueda dentro de las tablas */
.table-controls {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.table-controls.has-active-filters {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-color: #ffc107;
}

.table-controls.has-active-filters .table-search-container::before {
    content: '🔍 Filtros activos';
    display: block;
    font-size: 0.8rem;
    color: #856404;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.table-search-container {
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 1rem;
    align-items: end;
    margin-bottom: 0.5rem;
}

.search-group,
.filter-group,
.sort-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.search-group label,
.filter-group label,
.sort-group label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
}

.table-search-input,
.table-filter-column,
.table-sort-column {
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 0.9rem;
    background: white;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.table-search-input:focus,
.table-filter-column:focus,
.table-sort-column:focus {
    border-color: #4facfe;
    box-shadow: 0 0 0 0.2rem rgba(79, 172, 254, 0.25);
    outline: 0;
}

.clear-table-filters {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    height: fit-content;
    transition: background-color 0.15s ease-in-out;
}

.clear-table-filters:hover {
    background: #c82333;
}

.table-info {
    color: #6c757d;
    font-size: 0.85rem;
    text-align: right;
}

.visible-count,
.total-count {
    font-weight: bold;
    color: #495057;
}

/* Animaciones para mensajes y carga AJAX */
@keyframes fadeInOut {
    0% { opacity: 0; transform: translateX(100%); }
    10% { opacity: 1; transform: translateX(0); }
    90% { opacity: 1; transform: translateX(0); }
    100% { opacity: 0; transform: translateX(100%); }
}

@keyframes fadeInScale {
    0% { opacity: 0; transform: scale(0.8); }
    100% { opacity: 1; transform: scale(1); }
}

.ajax-loading-indicator {
    animation: fadeInScale 0.3s ease-out;
}

.ajax-error-message {
    animation: fadeInOut 4s ease-in-out forwards;
}

.filter-restored-message {
    animation: fadeInOut 3s ease-in-out forwards;
}

/* Responsive para controles de tabla */
@media (max-width: 768px) {
    .table-search-container {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .search-group {
        grid-column: 1 / -1;
    }
    
    .table-info {
        text-align: center;
        margin-top: 0.5rem;
    }
}

@media (max-width: 576px) {
    .table-controls {
        padding: 0.75rem;
    }
    
    .table-search-container {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .clear-table-filters {
        width: 100%;
        margin-top: 0.5rem;
    }
}
</style>

<!-- Incluir JavaScript simplificado y sin conflictos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando funcionalidad AJAX para completar información...');
    
    // Event delegation para botones de paginación AJAX (más simple y directo)
    document.addEventListener('click', function(e) {
        const ajaxPageBtn = e.target.closest('.ajax-page-btn');
        if (ajaxPageBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const page = ajaxPageBtn.getAttribute('data-pagina') || ajaxPageBtn.getAttribute('data-page');
            const section = ajaxPageBtn.getAttribute('data-seccion') || ajaxPageBtn.getAttribute('data-section');
            
            console.log('Clic en paginación AJAX:', { page, section });
            
            if (page && section) {
                loadPageAjax(section, page);
            }
        }
        
        // Guardar estado cuando se hace clic en enlaces de "Completar"
        const link = e.target.closest('.btn-edit');
        if (link) {
            console.log('Guardando estado antes de navegar a completar');
        }
    });
    
    // Event delegation para selectores de registros por página
    document.addEventListener('change', function(e) {
        const selector = e.target.closest('.ajax-records-selector');
        if (selector) {
            e.preventDefault();
            e.stopPropagation();
            
            const seccion = selector.getAttribute('data-seccion');
            const registros = selector.value;
            
            console.log('Cambio en registros por página:', { seccion, registros });
            
            if (seccion && registros) {
                loadPageAjax(seccion, 1, registros);
            }
        }
    });
    
    // Función principal para cargar página vía AJAX
    function loadPageAjax(seccion, pagina, registros = null) {
        console.log(`Cargando página AJAX: sección=${seccion}, página=${pagina}, registros=${registros}`);
        
        // Mostrar indicador de carga
        const tablaContainer = document.getElementById(`tabla-${seccion}`);
        if (tablaContainer) {
            showLoadingIndicator(tablaContainer);
        }
        
        // Construir parámetros
        const params = new URLSearchParams();
        params.append('seccion', seccion);
        params.append('pagina', pagina);
        
        if (registros) {
            params.append('registros', registros);
        } else {
            // Obtener registros por página actual de la sección
            const registrosSelect = document.querySelector(`select.ajax-records-selector[data-seccion="${seccion}"]`);
            if (registrosSelect) {
                params.append('registros', registrosSelect.value);
            } else {
                params.append('registros', '10'); // Default
            }
        }
        
        // Añadir filtros de búsqueda globales
        const searchInput = document.querySelector('input[name="busqueda"]');
        if (searchInput && searchInput.value) {
            params.append('search', searchInput.value);
        }
        
        console.log('Parámetros AJAX:', params.toString());
        
        // Realizar petición AJAX
        fetch(`control/ajax_pagination.php?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Respuesta AJAX recibida:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos AJAX procesados:', data);
            if (data.success) {
                updateTableContent(seccion, data);
                console.log('Tabla actualizada exitosamente');
            } else {
                throw new Error(data.error || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error en paginación AJAX:', error);
            showErrorMessage('Error al cargar la página: ' + error.message);
        })
        .finally(() => {
            hideLoadingIndicator(tablaContainer);
        });
    }
    
    // Función para actualizar contenido de tabla
    function updateTableContent(seccion, data) {
        console.log('Actualizando contenido de tabla:', seccion);
        
        // Actualizar tabla - usar solo el tbody del HTML retornado
        const tbody = document.querySelector(`#tabla-${seccion} table tbody`);
        if (tbody && data.html_tabla) {
            // Extraer solo el tbody del HTML retornado
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html_tabla;
            const tableElement = tempDiv.querySelector('table tbody');
            if (tableElement) {
                tbody.innerHTML = tableElement.innerHTML;
            } else {
                tbody.innerHTML = data.html_tabla;
            }
            console.log('Tabla actualizada');
        }
        
        // Actualizar controles de paginación (buscar todos los contenedores)
        const paginationContainers = document.querySelectorAll(`#seccion-${seccion} .pagination-container`);
        console.log('Contenedores de paginación encontrados:', paginationContainers.length);
        
        paginationContainers.forEach(container => {
            if (data.html_paginacion) {
                container.outerHTML = data.html_paginacion;
            }
        });
        
        console.log('Paginación actualizada');
    }
    
    // Funciones auxiliares para indicadores de carga
    function showLoadingIndicator(container) {
        const indicator = document.createElement('div');
        indicator.className = 'ajax-loading-indicator';
        indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
        indicator.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            font-weight: 600;
            color: #007bff;
        `;
        
        container.style.position = 'relative';
        container.appendChild(indicator);
    }
    
    function hideLoadingIndicator(container) {
        if (container) {
            const indicator = container.querySelector('.ajax-loading-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }
    
    function showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'ajax-error-message';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            z-index: 9999;
            font-size: 0.9rem;
            font-weight: 600;
        `;
        
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 4000);
    }
    
    // Función global para cambiar registros por página con AJAX (para compatibilidad)
    window.cambiarRegistrosPorPaginaAjax = function(valor, seccion) {
        console.log('Función de compatibilidad llamada:', { valor, seccion });
        loadPageAjax(seccion, 1, valor);
    };
    
    // Función global para limpiar filtros
    window.limpiarFiltros = function() {
        sessionStorage.clear();
        window.location.href = '?accion=completar_informacion';
    };
    
    // Función simple para guardar estado de filtros (compatible)
    window.saveTableFiltersState = function() {
        console.log('Guardando estado de filtros de tabla');
    };
    
    // Exponer funciones globalmente para compatibilidad
    window.loadPageAjax = loadPageAjax;
    
    // Inicializar controles de búsqueda en tablas
    initializeTableControls();
    
    console.log('Sistema AJAX inicializado correctamente');
    
    // Funciones para los filtros de tabla específicos por sección
    function initializeTableControls() {
        document.querySelectorAll('.table-controls').forEach(control => {
            const tableId = control.getAttribute('data-table');
            const searchInput = control.querySelector('.table-search-input');
            const filterSelect = control.querySelector('.table-filter-column');
            const sortSelect = control.querySelector('.table-sort-column');
            const clearButton = control.querySelector('.clear-table-filters');
            
            const table = document.getElementById(`tabla-${tableId}`)?.querySelector('table');
            
            if (!table) return;
            
            // Inicializar contador de registros
            updateTableInfo(control, table);
            
            // Event listeners
            if (searchInput) {
                searchInput.addEventListener('input', debounce(() => {
                    filterTable(table, control);
                }, 300));
            }
            
            if (filterSelect) {
                filterSelect.addEventListener('change', () => {
                    filterTable(table, control);
                });
            }
            
            if (sortSelect) {
                sortSelect.addEventListener('change', () => {
                    sortTable(table, control);
                });
            }
            
            if (clearButton) {
                clearButton.addEventListener('click', () => {
                    clearTableFilters(control, table);
                });
            }
        });
    }
    
    // Función para filtrar tabla
    function filterTable(table, control) {
        const searchTerm = control.querySelector('.table-search-input').value.toLowerCase();
        const filterColumn = control.querySelector('.table-filter-column').value;
        const rows = table.querySelectorAll('tbody tr');
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            let shouldShow = true;
            
            if (searchTerm) {
                let textToSearch = '';
                
                if (filterColumn === 'all') {
                    textToSearch = row.textContent.toLowerCase();
                } else {
                    const cells = row.querySelectorAll('td');
                    const columnIndex = parseInt(filterColumn);
                    if (cells[columnIndex]) {
                        textToSearch = cells[columnIndex].textContent.toLowerCase();
                    }
                }
                
                shouldShow = textToSearch.includes(searchTerm);
            }
            
            if (shouldShow) {
                row.style.display = '';
                row.classList.remove('table-row-hidden');
                if (searchTerm) {
                    row.classList.add('table-row-highlight');
                } else {
                    row.classList.remove('table-row-highlight');
                }
                visibleCount++;
            } else {
                row.style.display = 'none';
                row.classList.add('table-row-hidden');
                row.classList.remove('table-row-highlight');
            }
        });
        
        updateTableInfo(control, table, visibleCount);
        showNoResultsMessage(table, visibleCount === 0 && searchTerm);
    }
    
    // Función para ordenar tabla
    function sortTable(table, control) {
        const sortValue = control.querySelector('.table-sort-column').value;
        const [columnIndex, direction] = sortValue.split('-');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Remover indicadores de ordenamiento anteriores
        table.querySelectorAll('th .sort-indicator').forEach(indicator => {
            indicator.remove();
        });
        
        // Ordenar filas
        rows.sort((a, b) => {
            const cellA = a.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';
            const cellB = b.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';
            
            let comparison = cellA.localeCompare(cellB, 'es', { numeric: true, sensitivity: 'base' });
            
            return direction === 'desc' ? -comparison : comparison;
        });
        
        // Reordenar en el DOM
        rows.forEach(row => tbody.appendChild(row));
        
        // Añadir indicador de ordenamiento
        const headerCell = table.querySelectorAll('th')[columnIndex];
        if (headerCell) {
            const indicator = document.createElement('span');
            indicator.className = `sort-indicator sort-${direction}`;
            indicator.innerHTML = direction === 'asc' ? ' ↑' : ' ↓';
            headerCell.appendChild(indicator);
        }
        
        // Aplicar filtros después del ordenamiento si hay filtros activos
        filterTable(table, control);
    }
    
    // Función para limpiar filtros de tabla
    function clearTableFilters(control, table) {
        control.querySelector('.table-search-input').value = '';
        control.querySelector('.table-filter-column').value = 'all';
        control.querySelector('.table-sort-column').selectedIndex = 0;
        
        // Mostrar todas las filas
        table.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = '';
            row.classList.remove('table-row-hidden', 'table-row-highlight');
        });
        
        // Remover indicadores de ordenamiento
        table.querySelectorAll('th .sort-indicator').forEach(indicator => {
            indicator.remove();
        });
        
        // Remover mensaje de "sin resultados"
        const noResultsMsg = table.parentElement.querySelector('.no-results-message');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        updateTableInfo(control, table);
    }
    
    // Función para actualizar información de la tabla
    function updateTableInfo(control, table, visibleCount = null) {
        const totalRows = table.querySelectorAll('tbody tr').length;
        const visibleRows = visibleCount !== null ? visibleCount : totalRows;
        
        const visibleCountSpan = control.querySelector('.visible-count');
        const totalCountSpan = control.querySelector('.total-count');
        
        if (visibleCountSpan) visibleCountSpan.textContent = visibleRows;
        if (totalCountSpan) totalCountSpan.textContent = totalRows;
        
        // Marcar si hay filtros activos
        const searchInput = control.querySelector('.table-search-input');
        const filterSelect = control.querySelector('.table-filter-column');
        const sortSelect = control.querySelector('.table-sort-column');
        
        const hasActiveSearch = searchInput && searchInput.value.trim() !== '';
        const hasActiveFilter = filterSelect && filterSelect.value !== 'all';
        const hasActiveSort = sortSelect && sortSelect.selectedIndex !== 0;
        
        if (hasActiveSearch || hasActiveFilter || hasActiveSort) {
            control.classList.add('has-active-filters');
        } else {
            control.classList.remove('has-active-filters');
        }
    }
    
    // Función para mostrar mensaje de "sin resultados"
    function showNoResultsMessage(table, show) {
        const container = table.parentElement;
        let message = container.querySelector('.no-results-message');
        
        if (show && !message) {
            message = document.createElement('div');
            message.className = 'no-results-message';
            message.innerHTML = '<i class="fas fa-search"></i> No se encontraron resultados para la búsqueda actual.';
            message.style.cssText = `
                text-align: center;
                padding: 2rem;
                color: #666;
                font-style: italic;
                background: #f8f9fa;
                border-radius: 8px;
                margin: 1rem 0;
            `;
            container.appendChild(message);
        } else if (!show && message) {
            message.remove();
        }
    }
    
    // Función debounce para optimizar búsquedas
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
