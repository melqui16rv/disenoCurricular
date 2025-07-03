<?php
/**
 * Funciones auxiliares para AJAX de completar información
 * Extraídas de completar_informacion_new.php para mantener consistencia
 */

// Incluir las funciones principales de la vista
if (!function_exists('validarPagina')) {
    // Función para validar y corregir páginas fuera de rango
    function validarPagina($pagina_solicitada, $total_registros, $registros_por_pagina) {
        if ($total_registros == 0) {
            return 1;
        }
        
        $total_paginas = ceil($total_registros / $registros_por_pagina);
        $pagina_corregida = max(1, min($pagina_solicitada, $total_paginas));
        
        return $pagina_corregida;
    }
}

if (!function_exists('obtenerDisenosConCamposFaltantes')) {
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
}

if (!function_exists('obtenerCompetenciasConCamposFaltantes')) {
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
}

if (!function_exists('obtenerRapsConCamposFaltantes')) {
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
}

// Función para generar HTML de tabla específica por sección
function generarTablaSeccion($seccion, $datos) {
    if (empty($datos)) {
        return '<div class="no-results">
                    <i class="fas fa-info-circle"></i>
                    <h3>Sin resultados</h3>
                    <p>No se encontraron registros con campos faltantes en esta página.</p>
                </div>';
    }
    
    $html = '<div class="table-responsive">
                <table class="data-table">
                    <thead>';
    
    // Headers según la sección
    switch ($seccion) {
        case 'disenos':
            $html .= '<tr>
                        <th>Código</th>
                        <th>Programa</th>
                        <th>Tipo</th>
                        <th>Horas Totales</th>
                        <th>Campos Faltantes</th>
                        <th>Acciones</th>
                    </tr>';
            break;
            
        case 'competencias':
            $html .= '<tr>
                        <th>Código</th>
                        <th>Competencia</th>
                        <th>Programa</th>
                        <th>Horas</th>
                        <th>Campos Faltantes</th>
                        <th>Acciones</th>
                    </tr>';
            break;
            
        case 'raps':
            $html .= '<tr>
                        <th>Código</th>
                        <th>RAP</th>
                        <th>Competencia</th>
                        <th>Programa</th>
                        <th>Horas</th>
                        <th>Campos Faltantes</th>
                        <th>Acciones</th>
                    </tr>';
            break;
    }
    
    $html .= '</thead><tbody>';
    
    // Filas según la sección
    foreach ($datos as $item) {
        $html .= '<tr>';
        
        switch ($seccion) {
            case 'disenos':
                $horas_lectiva = (float)($item['horasDesarrolloLectiva'] ?? 0);
                $horas_productiva = (float)($item['horasDesarrolloProductiva'] ?? 0);
                $total_horas = $horas_lectiva + $horas_productiva;
                
                $html .= '<td><code>' . htmlspecialchars($item['codigoDiseño'] ?? '') . '</code></td>';
                $html .= '<td>' . htmlspecialchars($item['nombrePrograma'] ?? '') . '</td>';
                $html .= '<td><span class="badge badge-info">' . htmlspecialchars($item['tipoPrograma'] ?? '') . '</span></td>';
                $html .= '<td>' . number_format($total_horas) . ' hrs</td>';
                break;
                
            case 'competencias':
                $html .= '<td><code>' . htmlspecialchars($item['codigoDiseñoCompetenciaReporte'] ?? '') . '</code></td>';
                $html .= '<td>' . htmlspecialchars($item['nombreCompetencia'] ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($item['nombrePrograma'] ?? '') . '</td>';
                $html .= '<td>' . number_format((float)($item['horasDesarrolloCompetencia'] ?? 0)) . ' hrs</td>';
                break;
                
            case 'raps':
                $html .= '<td><code>' . htmlspecialchars($item['codigoDiseñoCompetenciaReporteRap'] ?? '') . '</code></td>';
                $html .= '<td>' . htmlspecialchars($item['nombreRap'] ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($item['nombreCompetencia'] ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($item['nombrePrograma'] ?? '') . '</td>';
                $html .= '<td>' . number_format((float)($item['horasDesarrolloRap'] ?? 0)) . ' hrs</td>';
                break;
        }
        
        // Campos faltantes (común para todas las secciones)
        $html .= '<td><div class="campos-faltantes">';
        if (!empty($item['camposFaltantes'])) {
            foreach ($item['camposFaltantes'] as $campo) {
                $html .= '<span class="badge badge-warning">' . htmlspecialchars($campo) . '</span> ';
            }
        }
        $html .= '</div></td>';
        
        // Acciones (común para todas las secciones)
        $html .= '<td>';
        switch ($seccion) {
            case 'disenos':
                $codigo = $item['codigoDiseño'] ?? '';
                $html .= '<a href="?accion=editar&tipo=disenos&codigo=' . urlencode($codigo) . '" 
                           class="btn-action btn-edit" title="Editar diseño">
                            <i class="fas fa-edit"></i>
                          </a>';
                break;
            case 'competencias':
                $codigo = $item['codigoDiseñoCompetenciaReporte'] ?? '';
                $html .= '<a href="?accion=editar&tipo=competencias&codigo=' . urlencode($codigo) . '" 
                           class="btn-action btn-edit" title="Editar competencia">
                            <i class="fas fa-edit"></i>
                          </a>';
                break;
            case 'raps':
                $codigo = $item['codigoDiseñoCompetenciaReporteRap'] ?? '';
                $html .= '<a href="?accion=editar&tipo=raps&codigo=' . urlencode($codigo) . '" 
                           class="btn-action btn-edit" title="Editar RAP">
                            <i class="fas fa-edit"></i>
                          </a>';
                break;
        }
        $html .= '</td>';
        
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table></div>';
    
    return $html;
}

if (!function_exists('generarPaginacion')) {
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
        $html .= '<select data-seccion="' . $seccion_id . '" data-registros="true" class="records-selector">';
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
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='1' class='page-btn first-btn' title='Primera página'>";
            $html .= '<i class="fas fa-angle-double-left"></i></a>';
        }
        
        // Botón Anterior
        if ($pagina_actual > 1) {
            $prev = $pagina_actual - 1;
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='{$prev}' class='page-btn prev-btn' title='Página anterior'>";
            $html .= '<i class="fas fa-chevron-left"></i> Anterior</a>';
        }
        
        // Números de página
        $inicio = max(1, $pagina_actual - 2);
        $fin = min($total_paginas, $pagina_actual + 2);
        
        if ($inicio > 1) {
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='1' class='page-btn'>1</a>";
            if ($inicio > 2) {
                $html .= '<span class="page-ellipsis">...</span>';
            }
        }
        
        for ($i = $inicio; $i <= $fin; $i++) {
            $active = ($i == $pagina_actual) ? 'active' : '';
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='{$i}' class='page-btn {$active}'>{$i}</a>";
        }
        
        if ($fin < $total_paginas) {
            if ($fin < $total_paginas - 1) {
                $html .= '<span class="page-ellipsis">...</span>';
            }
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='{$total_paginas}' class='page-btn'>{$total_paginas}</a>";
        }
        
        // Botón Siguiente
        if ($pagina_actual < $total_paginas) {
            $next = $pagina_actual + 1;
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='{$next}' class='page-btn next-btn' title='Página siguiente'>";
            $html .= 'Siguiente <i class="fas fa-chevron-right"></i></a>';
        }
        
        // Botón Última página
        if ($pagina_actual < $total_paginas - 2) {
            $html .= "<a href='javascript:void(0)' data-seccion='{$seccion_id}' data-pagina='{$total_paginas}' class='page-btn last-btn' title='Última página'>";
            $html .= '<i class="fas fa-angle-double-right"></i></a>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
?>
