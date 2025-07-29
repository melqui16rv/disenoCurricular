<?php
/**
 * Funciones auxiliares para paginación y filtros
 * Sistema de Gestión de Diseños Curriculares SENA
 */

function generarFiltrosYPaginacion($accion, $tipo = '', $codigo = '', $filtros = [], $total_registros = 0, $pagina_actual = 1, $registros_por_pagina = 10, $total_paginas = 1) {
    $busqueda = $filtros['busqueda'] ?? '';
    $horas_min = $filtros['horas_min'] ?? '';
    $horas_max = $filtros['horas_max'] ?? '';
    $meses_min = $filtros['meses_min'] ?? '';
    $meses_max = $filtros['meses_max'] ?? '';
    $nivel_academico = $filtros['nivel_academico'] ?? '';
    $red_tecnologica = $filtros['red_tecnologica'] ?? '';
    
    $base_url = "?accion=" . urlencode($accion);
    if ($codigo) {
        $base_url .= "&codigo=" . urlencode($codigo);
    }
    
    ob_start();
    ?>
    <div class="filtros-paginacion-container">
        <!-- Filtros -->
        <form method="GET" class="filtros-form">
            <input type="hidden" name="accion" value="<?php echo htmlspecialchars($accion); ?>">
            <?php if ($codigo): ?>
                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($codigo); ?>">
            <?php endif; ?>
            
            <div class="filtros-grid">
                <!-- Búsqueda general -->
                <div>
                    <label>
                        <i class="fas fa-search"></i> Búsqueda
                    </label>
                    <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                           placeholder="Buscar programa, código..." class="form-control">
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
                
                <?php if ($accion === 'listar'): ?>
                    <!-- Filtros específicos para diseños -->
                    <div>
                        <label>
                            <i class="fas fa-calendar-alt"></i> Meses mín
                        </label>
                        <input type="number" name="meses_min" value="<?php echo htmlspecialchars($meses_min); ?>" 
                               placeholder="0" min="0" class="form-control">
                    </div>
                    
                    <div>
                        <label>
                            <i class="fas fa-calendar-alt"></i> Meses máx
                        </label>
                        <input type="number" name="meses_max" value="<?php echo htmlspecialchars($meses_max); ?>" 
                               placeholder="99" min="0" class="form-control">
                    </div>
                    
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
                    
                    <div>
                        <label>
                            <i class="fas fa-network-wired"></i> Red Tecnológica
                        </label>
                        <input type="text" name="red_tecnologica" value="<?php echo htmlspecialchars($red_tecnologica); ?>" 
                               placeholder="Ej: Informática" class="form-control">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="filtros-botones">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
                <a href="<?php echo $base_url; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>
        </form>
        
        <!-- Información de resultados y paginación -->
        <div class="resultados-info">
            <!-- Información de registros -->
            <div class="info-registros">
                <i class="fas fa-info-circle"></i>
                Mostrando <?php echo min($registros_por_pagina, $total_registros - (($pagina_actual - 1) * $registros_por_pagina)); ?> 
                de <?php echo number_format($total_registros); ?> registros (Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>)
            </div>
            
            <!-- Selector de registros por página -->
            <div class="registros-selector">
                <label>Registros por página:</label>
                <select onchange="cambiarRegistrosPorPagina(this.value)" class="form-control">
                    <?php foreach ([5, 10, 25, 50, 100] as $opcion): ?>
                        <option value="<?php echo $opcion; ?>" <?php echo ($registros_por_pagina == $opcion) ? 'selected' : ''; ?>>
                            <?php echo $opcion; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <div class="paginacion">
                    <?php
                    // Página anterior
                    if ($pagina_actual > 1):
                    ?>
                        <a href="<?php echo $base_url; ?>&pagina=<?php echo ($pagina_actual - 1); ?>&registros_por_pagina=<?php echo $registros_por_pagina; ?>&busqueda=<?php echo urlencode($busqueda); ?>&horas_min=<?php echo urlencode($horas_min); ?>&horas_max=<?php echo urlencode($horas_max); ?>&meses_min=<?php echo urlencode($meses_min); ?>&meses_max=<?php echo urlencode($meses_max); ?>&nivel_academico=<?php echo urlencode($nivel_academico); ?>&red_tecnologica=<?php echo urlencode($red_tecnologica); ?>" 
                           class="btn btn-sm btn-secondary">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    // Páginas numéricas
                    $inicio = max(1, $pagina_actual - 2);
                    $fin = min($total_paginas, $pagina_actual + 2);
                    
                    for ($i = $inicio; $i <= $fin; $i++):
                    ?>
                        <a href="<?php echo $base_url; ?>&pagina=<?php echo $i; ?>&registros_por_pagina=<?php echo $registros_por_pagina; ?>&busqueda=<?php echo urlencode($busqueda); ?>&horas_min=<?php echo urlencode($horas_min); ?>&horas_max=<?php echo urlencode($horas_max); ?>&meses_min=<?php echo urlencode($meses_min); ?>&meses_max=<?php echo urlencode($meses_max); ?>&nivel_academico=<?php echo urlencode($nivel_academico); ?>&red_tecnologica=<?php echo urlencode($red_tecnologica); ?>" 
                           class="btn btn-sm <?php echo ($i == $pagina_actual) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php
                    // Página siguiente
                    if ($pagina_actual < $total_paginas):
                    ?>
                        <a href="<?php echo $base_url; ?>&pagina=<?php echo ($pagina_actual + 1); ?>&registros_por_pagina=<?php echo $registros_por_pagina; ?>&busqueda=<?php echo urlencode($busqueda); ?>&horas_min=<?php echo urlencode($horas_min); ?>&horas_max=<?php echo urlencode($horas_max); ?>&meses_min=<?php echo urlencode($meses_min); ?>&meses_max=<?php echo urlencode($meses_max); ?>&nivel_academico=<?php echo urlencode($nivel_academico); ?>&red_tecnologica=<?php echo urlencode($red_tecnologica); ?>" 
                           class="btn btn-sm btn-secondary">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    function cambiarRegistrosPorPagina(nuevosRegistros) {
        const url = new URL(window.location);
        url.searchParams.set('registros_por_pagina', nuevosRegistros);
        url.searchParams.set('pagina', '1'); // Resetear a la primera página
        window.location.href = url.toString();
    }
    </script>
    <?php
    return ob_get_clean();
}
?>
