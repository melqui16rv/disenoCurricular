<?php
/**
 * Funciones auxiliares para filtros de completar información
 * Sistema de Gestión de Diseños Curriculares SENA
 */

function generarFiltrosCompletarInformacion($filtros = [], $total_registros = 0, $pagina_actual = 1, $registros_por_pagina = 10, $total_paginas = 1) {
    $seccion = $filtros['seccion'] ?? 'todas';
    $busqueda = $filtros['busqueda'] ?? '';
    $horas_min = $filtros['horas_min'] ?? '';
    $horas_max = $filtros['horas_max'] ?? '';
    $tipo_programa = $filtros['tipo_programa'] ?? '';
    $nivel_academico = $filtros['nivel_academico'] ?? '';
    $estado = $filtros['estado'] ?? '';
    $fecha_desde = $filtros['fecha_desde'] ?? '';
    $fecha_hasta = $filtros['fecha_hasta'] ?? '';
    
    ob_start();
    ?>
    <div class="filtros-completar-container">
        <!-- Filtros específicos para completar información -->
        <form method="GET" class="filtros-completar-form">
            <input type="hidden" name="accion" value="completar_informacion">
            
            <div class="filtros-completar-grid">
                <!-- Sección a mostrar -->
                <div>
                    <label>
                        <i class="fas fa-filter"></i> Sección
                    </label>
                    <select name="seccion" class="form-control">
                        <option value="todas" <?php echo ($seccion === 'todas') ? 'selected' : ''; ?>>Todas las secciones</option>
                        <option value="disenos" <?php echo ($seccion === 'disenos') ? 'selected' : ''; ?>>Solo Diseños</option>
                        <option value="competencias" <?php echo ($seccion === 'competencias') ? 'selected' : ''; ?>>Solo Competencias</option>
                        <option value="raps" <?php echo ($seccion === 'raps') ? 'selected' : ''; ?>>Solo RAPs</option>
                    </select>
                </div>
                
                <!-- Búsqueda general -->
                <div>
                    <label>
                        <i class="fas fa-search"></i> Búsqueda
                    </label>
                    <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                           placeholder="Buscar por código, nombre..." class="form-control">
                </div>
                
                <!-- Estado de completitud -->
                <div>
                    <label>
                        <i class="fas fa-tasks"></i> Estado
                    </label>
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="incompleto" <?php echo ($estado === 'incompleto') ? 'selected' : ''; ?>>Incompletos</option>
                        <option value="completo" <?php echo ($estado === 'completo') ? 'selected' : ''; ?>>Completos</option>
                    </select>
                </div>
                
                <!-- Filtro de horas mínimas -->
                <div>
                    <label>
                        <i class="fas fa-clock"></i> Horas mín
                    </label>
                    <input type="number" name="horas_min" value="<?php echo htmlspecialchars($horas_min); ?>" 
                           placeholder="0" min="0" class="form-control">
                </div>
                
                <!-- Filtro de horas máximas -->
                <div>
                    <label>
                        <i class="fas fa-clock"></i> Horas máx
                    </label>
                    <input type="number" name="horas_max" value="<?php echo htmlspecialchars($horas_max); ?>" 
                           placeholder="9999" min="0" class="form-control">
                </div>
                
                <!-- Tipo de programa -->
                <div>
                    <label>
                        <i class="fas fa-graduation-cap"></i> Tipo Programa
                    </label>
                    <select name="tipo_programa" class="form-control">
                        <option value="">Todos los tipos</option>
                        <option value="Técnico" <?php echo ($tipo_programa === 'Técnico') ? 'selected' : ''; ?>>Técnico</option>
                        <option value="Tecnólogo" <?php echo ($tipo_programa === 'Tecnólogo') ? 'selected' : ''; ?>>Tecnólogo</option>
                        <option value="Especialización" <?php echo ($tipo_programa === 'Especialización') ? 'selected' : ''; ?>>Especialización</option>
                    </select>
                </div>
                
                <!-- Nivel académico -->
                <div>
                    <label>
                        <i class="fas fa-user-graduate"></i> Nivel Académico
                    </label>
                    <select name="nivel_academico" class="form-control">
                        <option value="">Todos los niveles</option>
                        <option value="Básica Primaria" <?php echo ($nivel_academico === 'Básica Primaria') ? 'selected' : ''; ?>>Básica Primaria</option>
                        <option value="Básica Secundaria" <?php echo ($nivel_academico === 'Básica Secundaria') ? 'selected' : ''; ?>>Básica Secundaria</option>
                        <option value="Media Académica" <?php echo ($nivel_academico === 'Media Académica') ? 'selected' : ''; ?>>Media Académica</option>
                        <option value="Media Técnica" <?php echo ($nivel_academico === 'Media Técnica') ? 'selected' : ''; ?>>Media Técnica</option>
                        <option value="Técnico" <?php echo ($nivel_academico === 'Técnico') ? 'selected' : ''; ?>>Técnico</option>
                        <option value="Tecnólogo" <?php echo ($nivel_academico === 'Tecnólogo') ? 'selected' : ''; ?>>Tecnólogo</option>
                    </select>
                </div>
                
                <!-- Rango de fechas -->
                <div>
                    <label>
                        <i class="fas fa-calendar-alt"></i> Fecha desde
                    </label>
                    <input type="date" name="fecha_desde" value="<?php echo htmlspecialchars($fecha_desde); ?>" 
                           class="form-control">
                </div>
                
                <div>
                    <label>
                        <i class="fas fa-calendar-alt"></i> Fecha hasta
                    </label>
                    <input type="date" name="fecha_hasta" value="<?php echo htmlspecialchars($fecha_hasta); ?>" 
                           class="form-control">
                </div>
            </div>
            
            <div class="filtros-botones">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
                <a href="?accion=completar_informacion" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </a>
            </div>
        </form>
        
        <!-- Información de resultados -->
        <div class="resultados-info-completar">
            <div class="info-registros">
                <span class="badge badge-info">
                    <i class="fas fa-info-circle"></i>
                    Mostrando <?php echo min($registros_por_pagina, $total_registros); ?> de <?php echo $total_registros; ?> registros
                </span>
            </div>
        </div>
    </div>
    
    <style>
    .filtros-completar-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .filtros-completar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .filtros-completar-grid > div {
        display: flex;
        flex-direction: column;
    }
    
    .filtros-completar-grid label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .filtros-completar-grid label i {
        margin-right: 0.5rem;
        color: #3498db;
    }
    
    .filtros-botones {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        border-top: 1px solid #e9ecef;
        padding-top: 1rem;
    }
    
    .resultados-info-completar {
        margin-top: 1rem;
        display: flex;
        justify-content: center;
    }
    
    .badge {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .filtros-completar-grid {
            grid-template-columns: 1fr;
        }
        
        .filtros-botones {
            flex-direction: column;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * Generar paginación específica para completar información con secciones independientes
 */
function generarPaginacionCompletarInformacion($seccion, $total_registros, $pagina_actual, $registros_por_pagina, $filtros = []) {
    if ($total_registros == 0) {
        return '';
    }
    
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    if ($total_paginas <= 1) {
        return '';
    }
    
    // Construir URL base con filtros actuales
    $base_url = "?accion=completar_informacion";
    foreach ($filtros as $key => $value) {
        if (!empty($value) && $key !== "pagina_{$seccion}") {
            $base_url .= "&" . urlencode($key) . "=" . urlencode($value);
        }
    }
    
    ob_start();
    ?>
    <div class="paginacion-completar-<?php echo $seccion; ?>">
        <nav aria-label="Paginación de <?php echo $seccion; ?>">
            <ul class="pagination justify-content-center">
                <!-- Botón Anterior -->
                <?php if ($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $base_url; ?>&pagina_<?php echo $seccion; ?>=<?php echo ($pagina_actual - 1); ?>">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fas fa-chevron-left"></i> Anterior</span>
                    </li>
                <?php endif; ?>
                
                <!-- Números de página -->
                <?php
                $inicio = max(1, $pagina_actual - 2);
                $fin = min($total_paginas, $pagina_actual + 2);
                
                if ($inicio > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $base_url; ?>&pagina_<?php echo $seccion; ?>=1">1</a>
                    </li>
                    <?php if ($inicio > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                endif;
                
                for ($i = $inicio; $i <= $fin; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <?php if ($i == $pagina_actual): ?>
                            <span class="page-link"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a class="page-link" href="<?php echo $base_url; ?>&pagina_<?php echo $seccion; ?>=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor;
                
                if ($fin < $total_paginas): ?>
                    <?php if ($fin < $total_paginas - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $base_url; ?>&pagina_<?php echo $seccion; ?>=<?php echo $total_paginas; ?>"><?php echo $total_paginas; ?></a>
                    </li>
                <?php endif; ?>
                
                <!-- Botón Siguiente -->
                <?php if ($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $base_url; ?>&pagina_<?php echo $seccion; ?>=<?php echo ($pagina_actual + 1); ?>">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Siguiente <i class="fas fa-chevron-right"></i></span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    
    <style>
    .paginacion-completar-disenos,
    .paginacion-completar-competencias,
    .paginacion-completar-raps {
        margin: 1rem 0;
    }
    
    .pagination .page-link {
        border: 1px solid #dee2e6;
        color: #667eea;
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .pagination .page-link:hover {
        background-color: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    </style>
    <?php
    return ob_get_clean();
}
?>
