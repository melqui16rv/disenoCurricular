<?php
// Vista para completar información faltante en diseños, competencias y RAPs

// Verificar que $metodos esté disponible
if (!isset($metodos)) {
    die('Error: Variable $metodos no está disponible. Asegúrate de que el archivo se incluya desde index.php');
}

// Incluir funciones auxiliares para AJAX y generación de HTML
include_once 'completar_informacion_funciones.php';

// Obtener filtros y paginación
$filtro_seccion = $_GET['seccion'] ?? 'todas';
$filtro_busqueda = $_GET['busqueda'] ?? '';

// Paginación independiente por sección
$pagina_disenos = max(1, (int)($_GET['pagina_disenos'] ?? 1));
$pagina_competencias = max(1, (int)($_GET['pagina_competencias'] ?? 1));
$pagina_raps = max(1, (int)($_GET['pagina_raps'] ?? 1));

// Registros por página independientes por sección
$registros_disenos = (int)($_GET['registros_disenos'] ?? 10);
$registros_competencias = (int)($_GET['registros_competencias'] ?? 10);
$registros_raps = (int)($_GET['registros_raps'] ?? 10);

// Filtros avanzados
$filtro_horas_min = (float)($_GET['horas_min'] ?? 0);
$filtro_horas_max = (float)($_GET['horas_max'] ?? 0);
$filtro_tipo_programa = $_GET['tipo_programa'] ?? '';
$filtro_nivel_academico = $_GET['nivel_academico'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';

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
    $html .= '<select onchange="cambiarRegistrosPorPagina(this.value, \'' . $seccion_id . '\')" class="records-selector">';
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
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}=1' class='page-btn first-btn' title='Primera página'>";
        $html .= '<i class="fas fa-angle-double-left"></i></a>';
    }
    
    // Botón Anterior
    if ($pagina_actual > 1) {
        $prev = $pagina_actual - 1;
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}={$prev}' class='page-btn prev-btn' title='Página anterior'>";
        $html .= '<i class="fas fa-chevron-left"></i> Anterior</a>';
    }
    
    // Números de página
    $inicio = max(1, $pagina_actual - 2);
    $fin = min($total_paginas, $pagina_actual + 2);
    
    if ($inicio > 1) {
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}=1' class='page-btn'>1</a>";
        if ($inicio > 2) {
            $html .= '<span class="page-ellipsis">...</span>';
        }
    }
    
    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $pagina_actual) ? 'active' : '';
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}={$i}' class='page-btn {$active}'>{$i}</a>";
    }
    
    if ($fin < $total_paginas) {
        if ($fin < $total_paginas - 1) {
            $html .= '<span class="page-ellipsis">...</span>';
        }
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}={$total_paginas}' class='page-btn'>{$total_paginas}</a>";
    }
    
    // Botón Siguiente
    if ($pagina_actual < $total_paginas) {
        $next = $pagina_actual + 1;
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}={$next}' class='page-btn next-btn' title='Página siguiente'>";
        $html .= 'Siguiente <i class="fas fa-chevron-right"></i></a>';
    }
    
    // Botón Última página
    if ($pagina_actual < $total_paginas - 2) {
        $html .= "<a href='{$base_url}{$separator}pagina_{$seccion_id}={$total_paginas}' class='page-btn last-btn' title='Última página'>";
        $html .= '<i class="fas fa-angle-double-right"></i></a>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
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
        if (!$diseños) {
            $diseños = [];
        }
        
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

            // Filtro adicional por estado (solo diseños con campos faltantes o completos)
            if (!empty($filtros_array['estado'])) {
                $tieneFaltantes = !empty($camposFaltantes);
                if ($filtros_array['estado'] === 'incompleto' && !$tieneFaltantes) continue;
                if ($filtros_array['estado'] === 'completo' && $tieneFaltantes) continue;
            }

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
            'pagina_actual' => $pagina,
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

        // Filtro adicional por estado
        if (!empty($filtros_array['estado'])) {
            $tieneFaltantes = !empty($camposFaltantes);
            if ($filtros_array['estado'] === 'incompleto' && !$tieneFaltantes) continue;
            if ($filtros_array['estado'] === 'completo' && $tieneFaltantes) continue;
        }

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

// Función para detectar campos faltantes en RAPs
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

        // 1. Código RAP reporte (nuevo campo en lugar de codigoRapDiseño)
        if (empty($rap['codigoRapReporte'])) $camposFaltantes[] = 'Código RAP Reporte';

        // 2. Nombre del RAP
        if (empty($rap['nombreRap'])) $camposFaltantes[] = 'Nombre del RAP';

        // 3. Horas de desarrollo (debe ser > 0)
        $horas = (float)($rap['horasDesarrolloRap'] ?? 0);
        if ($horas <= 0) $camposFaltantes[] = 'Horas de Desarrollo (debe ser > 0)';

        // Filtro adicional por estado
        if (!empty($filtros_array['estado'])) {
            $tieneFaltantes = !empty($camposFaltantes);
            if ($filtros_array['estado'] === 'incompleto' && !$tieneFaltantes) continue;
            if ($filtros_array['estado'] === 'completo' && $tieneFaltantes) continue;
        }

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

// Construir array de filtros para URLs
$filtros_array = [
    'accion' => 'completar_informacion',
    'seccion' => $filtro_seccion,
    'busqueda' => $filtro_busqueda,
    'registros_por_pagina' => $registros_por_pagina,
    'horas_min' => $filtro_horas_min,
    'horas_max' => $filtro_horas_max,
    'tipo_programa' => $filtro_tipo_programa,
    'nivel_academico' => $filtro_nivel_academico,
    'estado' => $filtro_estado,
    'fecha_desde' => $filtro_fecha_desde,
    'fecha_hasta' => $filtro_fecha_hasta
];

// Obtener datos según filtros con paginación
$resultadoDiseños = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_actual];
$resultadoCompetencias = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_actual];
$resultadoRaps = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_actual];

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

    <!-- Filtros y búsqueda mejorados -->
    <div class="filters-section">
        <?php if ($filtro_busqueda || $filtro_horas_min || $filtro_horas_max || $filtro_tipo_programa || $filtro_nivel_academico || $filtro_estado): ?>
            <div class="active-filters-indicator">
                <i class="fas fa-filter"></i>
                <span>Filtros activos</span>
                <button type="button" class="filter-clear-btn" onclick="window.location.href='?accion=completar_informacion'" title="Limpiar todos los filtros">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>
        
        <form method="GET" class="filters-form" id="filtrosForm">
            <input type="hidden" name="accion" value="completar_informacion">
            <input type="hidden" name="pagina" value="1"> <!-- Reset a página 1 al filtrar -->

            <!-- Filtros básicos -->
            <div class="filter-group">
                <label for="seccion">Sección:</label>
                <select name="seccion" id="seccion" onchange="this.form.submit()">
                    <option value="todas" <?php echo $filtro_seccion === 'todas' ? 'selected' : ''; ?>>Todas las Secciones</option>
                    <option value="disenos" <?php echo $filtro_seccion === 'disenos' ? 'selected' : ''; ?>>Solo Diseños</option>
                    <option value="competencias" <?php echo $filtro_seccion === 'competencias' ? 'selected' : ''; ?>>Solo Competencias</option>
                    <option value="raps" <?php echo $filtro_seccion === 'raps' ? 'selected' : ''; ?>>Solo RAPs</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="busqueda">Buscar:</label>
                <input type="text" name="busqueda" id="busqueda" value="<?php echo htmlspecialchars($filtro_busqueda); ?>" placeholder="Código, nombre del programa...">
            </div>

            <div class="filter-group">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado">
                    <option value="">Todos los estados</option>
                    <option value="incompleto" <?php echo $filtro_estado === 'incompleto' ? 'selected' : ''; ?>>Solo Incompletos</option>
                    <option value="completo" <?php echo $filtro_estado === 'completo' ? 'selected' : ''; ?>>Solo Completos</option>
                </select>
            </div>

            <!-- Botón para mostrar filtros avanzados -->
            <button type="button" class="advanced-filters-toggle" onclick="toggleAdvancedFilters()">
                <i class="fas fa-sliders-h"></i> Filtros Avanzados
                <i class="fas fa-chevron-down" id="advanced-arrow"></i>
            </button>

            <!-- Filtros avanzados (ocultos por defecto) -->
            <div class="advanced-filters-content" id="advanced-filters">
                <div class="filter-group">
                    <label for="registros_por_pagina">Registros por página:</label>
                    <select name="registros_por_pagina" id="registros_por_pagina">
                        <option value="5" <?php echo $registros_por_pagina === 5 ? 'selected' : ''; ?>>5 registros</option>
                        <option value="10" <?php echo $registros_por_pagina === 10 ? 'selected' : ''; ?>>10 registros</option>
                        <option value="25" <?php echo $registros_por_pagina === 25 ? 'selected' : ''; ?>>25 registros</option>
                        <option value="50" <?php echo $registros_por_pagina === 50 ? 'selected' : ''; ?>>50 registros</option>
                        <option value="100" <?php echo $registros_por_pagina === 100 ? 'selected' : ''; ?>>100 registros</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Rango de Horas:</label>
                    <div class="range-filter">
                        <input type="number" name="horas_min" placeholder="Mínimo" value="<?php echo $filtro_horas_min ?: ''; ?>" min="0" step="1">
                        <span class="range-separator">-</span>
                        <input type="number" name="horas_max" placeholder="Máximo" value="<?php echo $filtro_horas_max ?: ''; ?>" min="0" step="1">
                    </div>
                </div>

                <div class="filter-group">
                    <label for="tipo_programa">Tipo de Programa:</label>
                    <select name="tipo_programa" id="tipo_programa">
                        <option value="">Todos los tipos</option>
                        <option value="Tecnólogo" <?php echo $filtro_tipo_programa === 'Tecnólogo' ? 'selected' : ''; ?>>Tecnólogo</option>
                        <option value="Técnico" <?php echo $filtro_tipo_programa === 'Técnico' ? 'selected' : ''; ?>>Técnico</option>
                        <option value="Auxiliar" <?php echo $filtro_tipo_programa === 'Auxiliar' ? 'selected' : ''; ?>>Auxiliar</option>
                        <option value="Operario" <?php echo $filtro_tipo_programa === 'Operario' ? 'selected' : ''; ?>>Operario</option>
                        <option value="Especialización" <?php echo $filtro_tipo_programa === 'Especialización' ? 'selected' : ''; ?>>Especialización</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="nivel_academico">Nivel Académico:</label>
                    <select name="nivel_academico" id="nivel_academico">
                        <option value="">Todos los niveles</option>
                        <option value="Bachillerato" <?php echo $filtro_nivel_academico === 'Bachillerato' ? 'selected' : ''; ?>>Bachillerato</option>
                        <option value="Técnico" <?php echo $filtro_nivel_academico === 'Técnico' ? 'selected' : ''; ?>>Técnico</option>
                        <option value="Tecnólogo" <?php echo $filtro_nivel_academico === 'Tecnólogo' ? 'selected' : ''; ?>>Tecnólogo</option>
                        <option value="Profesional" <?php echo $filtro_nivel_academico === 'Profesional' ? 'selected' : ''; ?>>Profesional</option>
                        <option value="Especialista" <?php echo $filtro_nivel_academico === 'Especialista' ? 'selected' : ''; ?>>Especialista</option>
                    </select>
                </div>
            </div>

            <!-- Botones de acción -->
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Aplicar Filtros
            </button>

            <a href="?accion=completar_informacion" class="btn-reset">
                <i class="fas fa-times"></i> Limpiar Todo
            </a>
        </form>
    </div>

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
                <div class="section-results" data-section="disenos" id="seccion-disenos">
                    <h3><i class="fas fa-graduation-cap"></i> Diseños con Campos Faltantes</h3>
                    
                    <!-- Paginación superior -->
                    <div id="paginacion-superior-disenos">
                        <?php if (!empty($resultadoDiseños['datos'])): ?>
                            <?php echo generarPaginacion($resultadoDiseños, $filtros_array, 'disenos'); ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Contenedor de tabla -->
                    <div id="tabla-disenos">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Programa</th>
                                        <th>Tipo</th>
                                        <th>Horas Totales</th>
                                        <th>Campos Faltantes</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($diseñosConFaltantes as $diseño): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($diseño['codigoDiseño'] ?? ''); ?></code></td>
                                            <td><?php echo htmlspecialchars($diseño['nombrePrograma'] ?? ''); ?></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($diseño['tipoPrograma'] ?? ''); ?></span></td>
                                            <td>
                                                <?php 
                                                $horas_lectiva = (float)($diseño['horasDesarrolloLectiva'] ?? 0);
                                                $horas_productiva = (float)($diseño['horasDesarrolloProductiva'] ?? 0);
                                                $total_horas = $horas_lectiva + $horas_productiva;
                                                echo number_format($total_horas) . ' hrs';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="campos-faltantes">
                                                    <?php foreach ($diseño['camposFaltantes'] as $campo): ?>
                                                        <span class="badge badge-warning"><?php echo htmlspecialchars($campo); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="?accion=editar&tipo=disenos&codigo=<?php echo urlencode($diseño['codigoDiseño']); ?>" 
                                                   class="btn-action btn-edit" title="Editar diseño">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Paginación inferior -->
                    <div id="paginacion-disenos">
                        <?php if (!empty($resultadoDiseños['datos'])): ?>
                            <?php echo generarPaginacion($resultadoDiseños, $filtros_array, 'disenos'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Competencias con campos faltantes -->
            <?php if (!empty($competenciasConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'competencias')): ?>
                <div class="section-results" data-section="competencias" id="seccion-competencias">
                    <h3><i class="fas fa-tasks"></i> Competencias con Campos Faltantes</h3>
                    
                    <!-- Paginación superior -->
                    <div id="paginacion-superior-competencias">
                        <?php if (!empty($resultadoCompetencias['datos'])): ?>
                            <?php echo generarPaginacion($resultadoCompetencias, $filtros_array, 'competencias'); ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Contenedor de tabla -->
                    <div id="tabla-competencias">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Competencia</th>
                                        <th>Programa</th>
                                        <th>Horas</th>
                                        <th>Campos Faltantes</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($competenciasConFaltantes as $competencia): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($competencia['codigoDiseñoCompetenciaReporte'] ?? ''); ?></code></td>
                                            <td><?php echo htmlspecialchars($competencia['nombreCompetencia'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($competencia['nombrePrograma'] ?? ''); ?></td>
                                            <td><?php echo number_format((float)($competencia['horasDesarrolloCompetencia'] ?? 0)) . ' hrs'; ?></td>
                                            <td>
                                                <div class="campos-faltantes">
                                                    <?php foreach ($competencia['camposFaltantes'] as $campo): ?>
                                                        <span class="badge badge-warning"><?php echo htmlspecialchars($campo); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="?accion=editar&tipo=competencias&codigo=<?php echo urlencode($competencia['codigoDiseñoCompetenciaReporte']); ?>" 
                                                   class="btn-action btn-edit" title="Editar competencia">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Paginación inferior -->
                    <div id="paginacion-competencias">
                        <?php if (!empty($resultadoCompetencias['datos'])): ?>
                            <?php echo generarPaginacion($resultadoCompetencias, $filtros_array, 'competencias'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- RAPs con campos faltantes -->
            <?php if (!empty($rapsConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'raps')): ?>
                <div class="section-results" data-section="raps" id="seccion-raps">
                    <h3><i class="fas fa-list-ul"></i> RAPs con Campos Faltantes</h3>
                    
                    <!-- Paginación superior -->
                    <div id="paginacion-superior-raps">
                        <?php if (!empty($resultadoRaps['datos'])): ?>
                            <?php echo generarPaginacion($resultadoRaps, $filtros_array, 'raps'); ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Contenedor de tabla -->
                    <div id="tabla-raps">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>RAP</th>
                                        <th>Competencia</th>
                                        <th>Programa</th>
                                        <th>Horas</th>
                                        <th>Campos Faltantes</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rapsConFaltantes as $rap): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($rap['codigoDiseñoCompetenciaReporteRap'] ?? ''); ?></code></td>
                                            <td><?php echo htmlspecialchars($rap['nombreRap'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($rap['nombreCompetencia'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($rap['nombrePrograma'] ?? ''); ?></td>
                                            <td><?php echo number_format((float)($rap['horasDesarrolloRap'] ?? 0)) . ' hrs'; ?></td>
                                            <td>
                                                <div class="campos-faltantes">
                                                    <?php foreach ($rap['camposFaltantes'] as $campo): ?>
                                                        <span class="badge badge-warning"><?php echo htmlspecialchars($campo); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="?accion=editar&tipo=raps&codigo=<?php echo urlencode($rap['codigoDiseñoCompetenciaReporteRap']); ?>" 
                                                   class="btn-action btn-edit" title="Editar RAP">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Paginación inferior -->
                    <div id="paginacion-raps">
                        <?php if (!empty($resultadoRaps['datos'])): ?>
                            <?php echo generarPaginacion($resultadoRaps, $filtros_array, 'raps'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Incluir JavaScript -->
<script src="/assets/js/forms/completar-informacion.js"></script>

<script>
// JavaScript inline mínimo para funcionalidad básica
document.addEventListener('DOMContentLoaded', function() {
    // Fallback si el archivo JS externo no carga
    if (typeof CompletarInformacion === 'undefined') {
        console.warn('Archivo JS externo no cargado, usando funcionalidad básica');
        
        // Funcionalidad básica de filtros avanzados
        window.toggleAdvancedFilters = function() {
            const advancedFilters = document.getElementById('advanced-filters');
            const arrow = document.getElementById('advanced-arrow');
            
            if (advancedFilters && arrow) {
                if (advancedFilters.classList.contains('show')) {
                    advancedFilters.classList.remove('show');
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    advancedFilters.classList.add('show');
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        };
        
        // Funcionalidad básica para cambiar registros por página con soporte para secciones
        window.cambiarRegistrosPorPagina = function(valor, seccion = null) {
            if (seccion) {
                // Cambio específico por sección
                const url = new URL(window.location);
                url.searchParams.set(`registros_${seccion}`, valor);
                url.searchParams.set(`pagina_${seccion}`, 1); // Reset página de la sección
                window.location.href = url.toString();
            } else {
                // Cambio global (compatibilidad)
                const form = document.getElementById('filtrosForm');
                const registrosPorPaginaInput = document.querySelector('select[name="registros_por_pagina"]');
                
                if (registrosPorPaginaInput) {
                    registrosPorPaginaInput.value = valor;
                }
                
                if (form) {
                    form.submit();
                }
            }
        };
        
        // Funcionalidad básica para ir a una página específica
        window.irAPagina = function(pagina, seccion = null) {
            if (seccion) {
                // Navegación específica por sección
                const url = new URL(window.location);
                url.searchParams.set(`pagina_${seccion}`, pagina);
                window.location.href = url.toString();
            } else {
                // Navegación global (compatibilidad)
                const form = document.getElementById('filtrosForm');
                const paginaInput = document.querySelector('input[name="pagina"]');
                
                if (paginaInput) {
                    paginaInput.value = pagina;
                }
                
                if (form) {
                    form.submit();
                }
            }
        };
    }
});
</script>

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

.filters-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
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

/* Paginación */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding: 1rem 0;
    border-top: 1px solid #ecf0f1;
}

.pagination-info {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.page-btn {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    color: #2c3e50;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.page-btn:hover {
    background: #f8f9fa;
    border-color: #3498db;
    color: #3498db;
}

.page-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.page-btn.active:hover {
    transform: translateY(-1px);
}

.page-ellipsis {
    padding: 0.5rem;
    color: #7f8c8d;
}

.prev-btn,
.next-btn {
    font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
    .statistics-panel {
        grid-template-columns: 1fr;
    }

    .filters-form {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group select,
    .filter-group input {
        min-width: auto;
    }

    .results-table {
        font-size: 0.8rem;
    }

    .results-table th,
    .results-table td {
        padding: 0.5rem;
    }

    .pagination-container {
        flex-direction: column;
        gap: 1rem;
    }

    .pagination-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
