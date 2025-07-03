<?php
// Vista para completar información faltante en diseños, competencias y RAPs

// Obtener filtros y paginación
$filtro_seccion = $_GET['seccion'] ?? 'todas';
$filtro_busqueda = $_GET['busqueda'] ?? '';
$pagina_actual = max(1, (int)($_GET['pagina'] ?? 1));
$registros_por_pagina = 10;

// Función para detectar campos faltantes en diseños
function obtenerDisenosConCamposFaltantes($metodos, $filtro_busqueda = '', $pagina = 1, $registros_por_pagina = 10) {
    $sql = "SELECT * FROM diseños WHERE 1=1";
    $params = [];

    if (!empty($filtro_busqueda)) {
        $sql .= " AND (codigoDiseño LIKE ? OR nombrePrograma LIKE ?)";
        $params[] = "%$filtro_busqueda%";
        $params[] = "%$filtro_busqueda%";
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
    $offset = ($pagina - 1) * $registros_por_pagina;
    
    $diseñosPaginados = array_slice($diseñosConFaltantes, $offset, $registros_por_pagina);

    return [
        'datos' => $diseñosPaginados,
        'total_registros' => $total_registros,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina,
        'registros_por_pagina' => $registros_por_pagina
    ];
}

// Función para detectar campos faltantes en competencias
function obtenerCompetenciasConCamposFaltantes($metodos, $filtro_busqueda = '', $pagina = 1, $registros_por_pagina = 10) {
    $sql = "SELECT c.*, d.nombrePrograma 
            FROM competencias c 
            LEFT JOIN diseños d ON SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2) = d.codigoDiseño 
            WHERE 1=1";
    $params = [];

    if (!empty($filtro_busqueda)) {
        $sql .= " AND (c.codigoDiseñoCompetenciaReporte LIKE ? OR c.nombreCompetencia LIKE ? OR d.nombrePrograma LIKE ?)";
        $params[] = "%$filtro_busqueda%";
        $params[] = "%$filtro_busqueda%";
        $params[] = "%$filtro_busqueda%";
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
    $offset = ($pagina - 1) * $registros_por_pagina;
    
    $competenciasPaginadas = array_slice($competenciasConFaltantes, $offset, $registros_por_pagina);

    return [
        'datos' => $competenciasPaginadas,
        'total_registros' => $total_registros,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina,
        'registros_por_pagina' => $registros_por_pagina
    ];
}

// Función para detectar campos faltantes en RAPs
function obtenerRapsConCamposFaltantes($metodos, $filtro_busqueda = '', $pagina = 1, $registros_por_pagina = 10) {
    $sql = "SELECT r.*, c.nombreCompetencia, d.nombrePrograma 
            FROM raps r 
            LEFT JOIN competencias c ON SUBSTRING_INDEX(r.codigoDiseñoCompetenciaReporteRap, '-', 3) = c.codigoDiseñoCompetenciaReporte 
            LEFT JOIN diseños d ON SUBSTRING_INDEX(r.codigoDiseñoCompetenciaReporteRap, '-', 2) = d.codigoDiseño 
            WHERE 1=1";
    $params = [];

    if (!empty($filtro_busqueda)) {
        $sql .= " AND (r.codigoDiseñoCompetenciaReporteRap LIKE ? OR r.nombreRap LIKE ? OR c.nombreCompetencia LIKE ? OR d.nombrePrograma LIKE ?)";
        $params[] = "%$filtro_busqueda%";
        $params[] = "%$filtro_busqueda%";
        $params[] = "%$filtro_busqueda%";
        $params[] = "%$filtro_busqueda%";
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

        if (!empty($camposFaltantes)) {
            $rap['camposFaltantes'] = $camposFaltantes;
            $rapsConFaltantes[] = $rap;
        }
    }

    // Aplicar paginación después del filtrado
    $total_registros = count($rapsConFaltantes);
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $offset = ($pagina - 1) * $registros_por_pagina;
    
    $rapsPaginados = array_slice($rapsConFaltantes, $offset, $registros_por_pagina);

    return [
        'datos' => $rapsPaginados,
        'total_registros' => $total_registros,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina,
        'registros_por_pagina' => $registros_por_pagina
    ];
}

// Función para generar controles de paginación
function generarPaginacion($resultado, $filtro_seccion, $filtro_busqueda, $seccion_id = '') {
    if ($resultado['total_paginas'] <= 1) {
        return '';
    }
    
    $pagina_actual = $resultado['pagina_actual'];
    $total_paginas = $resultado['total_paginas'];
    $total_registros = $resultado['total_registros'];
    $registros_por_pagina = $resultado['registros_por_pagina'];
    
    $inicio_registro = (($pagina_actual - 1) * $registros_por_pagina) + 1;
    $fin_registro = min($pagina_actual * $registros_por_pagina, $total_registros);
    
    $html = '<div class="pagination-container">';
    
    // Información de registros
    $html .= '<div class="pagination-info">';
    $html .= "Mostrando {$inicio_registro}-{$fin_registro} de {$total_registros} registros";
    $html .= '</div>';
    
    // Controles de paginación
    $html .= '<div class="pagination-controls">';
    
    // Botón Anterior
    if ($pagina_actual > 1) {
        $prev = $pagina_actual - 1;
        $html .= "<a href='?accion=completar_informacion&seccion={$filtro_seccion}&busqueda=" . urlencode($filtro_busqueda) . "&pagina={$prev}' class='page-btn prev-btn'>";
        $html .= '<i class="fas fa-chevron-left"></i> Anterior</a>';
    }
    
    // Números de página
    $inicio = max(1, $pagina_actual - 2);
    $fin = min($total_paginas, $pagina_actual + 2);
    
    if ($inicio > 1) {
        $html .= "<a href='?accion=completar_informacion&seccion={$filtro_seccion}&busqueda=" . urlencode($filtro_busqueda) . "&pagina=1' class='page-btn'>1</a>";
        if ($inicio > 2) {
            $html .= '<span class="page-ellipsis">...</span>';
        }
    }
    
    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $pagina_actual) ? 'active' : '';
        $html .= "<a href='?accion=completar_informacion&seccion={$filtro_seccion}&busqueda=" . urlencode($filtro_busqueda) . "&pagina={$i}' class='page-btn {$active}'>{$i}</a>";
    }
    
    if ($fin < $total_paginas) {
        if ($fin < $total_paginas - 1) {
            $html .= '<span class="page-ellipsis">...</span>';
        }
        $html .= "<a href='?accion=completar_informacion&seccion={$filtro_seccion}&busqueda=" . urlencode($filtro_busqueda) . "&pagina={$total_paginas}' class='page-btn'>{$total_paginas}</a>";
    }
    
    // Botón Siguiente
    if ($pagina_actual < $total_paginas) {
        $next = $pagina_actual + 1;
        $html .= "<a href='?accion=completar_informacion&seccion={$filtro_seccion}&busqueda=" . urlencode($filtro_busqueda) . "&pagina={$next}' class='page-btn next-btn'>";
        $html .= 'Siguiente <i class="fas fa-chevron-right"></i></a>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

// Obtener datos según filtros con paginación
$resultadoDiseños = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_actual];
$resultadoCompetencias = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_actual];
$resultadoRaps = ['datos' => [], 'total_registros' => 0, 'total_paginas' => 0, 'pagina_actual' => $pagina_actual];

if ($filtro_seccion === 'todas' || $filtro_seccion === 'disenos') {
    $resultadoDiseños = obtenerDisenosConCamposFaltantes($metodos, $filtro_busqueda, $pagina_actual, $registros_por_pagina);
}

if ($filtro_seccion === 'todas' || $filtro_seccion === 'competencias') {
    $resultadoCompetencias = obtenerCompetenciasConCamposFaltantes($metodos, $filtro_busqueda, $pagina_actual, $registros_por_pagina);
}

if ($filtro_seccion === 'todas' || $filtro_seccion === 'raps') {
    $resultadoRaps = obtenerRapsConCamposFaltantes($metodos, $filtro_busqueda, $pagina_actual, $registros_por_pagina);
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

    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <form method="GET" class="filters-form" id="filtrosForm">
            <input type="hidden" name="accion" value="completar_informacion">
            <input type="hidden" name="pagina" value="1"> <!-- Reset a página 1 al filtrar -->

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

            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filtrar
            </button>

            <a href="?accion=completar_informacion" class="btn-reset">
                <i class="fas fa-times"></i> Limpiar
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
                <div class="section-results">
                    <h3><i class="fas fa-graduation-cap"></i> Diseños con Campos Faltantes (<?php echo $totalDiseñosFaltantes; ?>)</h3>
                    <div class="results-table">
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
                    
                    <!-- Controles de paginación para diseños -->
                    <?php echo generarPaginacion($resultadoDiseños, $filtro_seccion, $filtro_busqueda, 'disenos'); ?>
                </div>
            <?php endif; ?>
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

                    <!-- Controles de paginación para diseños -->
                    <?php echo generarPaginacion($resultadoDiseños, $filtro_seccion, $filtro_busqueda, 'disenos'); ?>
                </div>
            <?php endif; ?>

            <!-- Competencias con campos faltantes -->
            <?php if (!empty($competenciasConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'competencias')): ?>
                <div class="section-results">
                    <h3><i class="fas fa-tasks"></i> Competencias con Campos Faltantes (<?php echo count($competenciasConFaltantes); ?>)</h3>
                    <div class="results-table">
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
                    <?php echo generarPaginacion($resultadoCompetencias, $filtro_seccion, $filtro_busqueda, 'competencias'); ?>
                </div>
            <?php endif; ?>

            <!-- RAPs con campos faltantes -->
            <?php if (!empty($rapsConFaltantes) && ($filtro_seccion === 'todas' || $filtro_seccion === 'raps')): ?>
                <div class="section-results">
                    <h3><i class="fas fa-list-ul"></i> RAPs con Campos Faltantes (<?php echo count($rapsConFaltantes); ?>)</h3>
                    <div class="results-table">
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
                    <?php echo generarPaginacion($resultadoRaps, $filtro_seccion, $filtro_busqueda, 'raps'); ?>
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
    flex-direction: column;
    gap: 1rem;
    margin-top: 1.5rem;
}

.pagination-info {
    color: #2c3e50;
    font-size: 0.9rem;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.page-btn {
    background: #3498db;
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

.page-btn:hover {
    background: #2980b9;
}

.page-btn.prev-btn {
    margin-right: auto;
}

.page-btn.next-btn {
    margin-left: auto;
}

.page-btn.active {
    background: #2ecc71;
}

.page-ellipsis {
    align-self: center;
    color: #7f8c8d;
    font-size: 1.2rem;
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

    .pagination-controls {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
