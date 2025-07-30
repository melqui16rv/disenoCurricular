<div class="card-header">
    <div class="flex-between">
        <div>
            <h2 class="card-title"><i class="fas fa-tasks"></i> Competencias del Diseño</h2>
            <p class="card-subtitle">
                <strong><?php echo htmlspecialchars($diseño_actual['nombrePrograma'] ?? 'Diseño no encontrado'); ?></strong>
                <br>Código: <?php echo htmlspecialchars($_GET['codigo'] ?? ''); ?>
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="?accion=crear&tipo=competencias&codigoDiseño=<?php echo urlencode($_GET['codigo'] ?? ''); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Competencia
            </a>
            <a href="?accion=listar" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<?php
// Incluir funciones de paginación
require_once 'funciones_paginacion.php';

// Preparar filtros para la función de paginación (solo filtros básicos para competencias)
$filtros_array = [
    'busqueda' => $busqueda ?? '',
    'horas_min' => $filtro_horas_min ?? '',
    'horas_max' => $filtro_horas_max ?? '',
    'tipo_programa' => '',
    'nivel_academico' => '',
    'red_tecnologica' => ''
];

// Generar filtros y paginación para competencias
echo generarFiltrosYPaginacion(
    'ver_competencias',
    'competencias',
    $_GET['codigo'] ?? '',
    $filtros_array,
    $total_registros ?? 0,
    $pagina ?? 1,
    $registros_por_pagina ?? 10,
    $total_paginas ?? 1
);
?>

<?php if (!$diseño_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró el diseño curricular especificado.
    </div>
<?php elseif (empty($competencias)): ?>
    <div class="text-center" style="padding: 3rem;">
        <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #bdc3c7; margin-bottom: 1rem;"></i>
        <h3 style="color: #7f8c8d;">No hay competencias registradas</h3>
        <p style="color: #95a5a6;">Este diseño curricular aún no tiene competencias asociadas</p>
        <a href="?accion=crear&tipo=competencias&codigoDiseño=<?php echo urlencode($_GET['codigo'] ?? ''); ?>" class="btn btn-primary mt-3">
            <i class="fas fa-plus"></i> Crear Primera Competencia
        </a>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-code"></i> Código Competencia</th>
                    <th><i class="fas fa-star"></i> Nombre de la Competencia</th>
                    <th><i class="fas fa-clock"></i> Horas</th>
                    <th><i class="fas fa-graduation-cap"></i> Requisitos Instructor</th>
                    <th><i class="fas fa-cogs"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($competencias as $competencia): ?>
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-weight: bold; color: #2c3e50;">
                                <?php echo htmlspecialchars($competencia['codigoCompetenciaReporte']); ?>
                            </span>
                            <br>
                            <small class="text-muted" style="font-family: monospace;">
                                <?php echo htmlspecialchars($competencia['codigoDiseñoCompetenciaReporte']); ?>
                            </small>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($competencia['nombreCompetencia']); ?></strong>
                            <?php if (!empty($competencia['normaUnidadCompetencia'])): ?>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-file-alt"></i> 
                                    <?php echo htmlspecialchars(substr($competencia['normaUnidadCompetencia'], 0, 100)); ?>
                                    <?php if (strlen($competencia['normaUnidadCompetencia']) > 100): ?>...<?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="font-size: 1.2rem; font-weight: bold; color: #e74c3c;">
                                <?php echo number_format($competencia['horasDesarrolloCompetencia'], 0); ?>h
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;">
                                <?php if (!empty($competencia['requisitosAcademicosInstructor'])): ?>
                                    <div><strong>Académicos:</strong></div>
                                    <div class="text-muted" style="margin-bottom: 5px;">
                                        <?php echo htmlspecialchars(substr($competencia['requisitosAcademicosInstructor'], 0, 80)); ?>
                                        <?php if (strlen($competencia['requisitosAcademicosInstructor']) > 80): ?>...<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($competencia['experienciaLaboralInstructor'])): ?>
                                    <div><strong>Experiencia:</strong></div>
                                    <div class="text-muted">
                                        <?php echo htmlspecialchars(substr($competencia['experienciaLaboralInstructor'], 0, 80)); ?>
                                        <?php if (strlen($competencia['experienciaLaboralInstructor']) > 80): ?>...<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                <a href="?accion=ver_raps&codigo=<?php echo urlencode($competencia['codigoDiseñoCompetenciaReporte']); ?>" 
                                   class="btn btn-primary btn-sm" title="Ver RAPs">
                                    <i class="fas fa-list-ul"></i> RAPs
                                </a>
                                <a href="?accion=editar&tipo=competencias&codigo=<?php echo urlencode($competencia['codigoDiseñoCompetenciaReporte']); ?>" 
                                   class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?accion=eliminar&tipo=competencias&codigo=<?php echo urlencode($competencia['codigoDiseñoCompetenciaReporte']); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Estás seguro de eliminar esta competencia? Se eliminarán también todos sus RAPs asociados.')"
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 2rem; padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; backdrop-filter: blur(10px);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; color: white;">
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;"><?php echo count($competencias); ?></h3>
                <p style="margin: 0; opacity: 0.8;">Competencias</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php 
                    $totalHorasCompetencias = array_sum(array_column($competencias, 'horasDesarrolloCompetencia'));
                    echo number_format($totalHorasCompetencias, 0);
                    ?>
                </h3>
                <p style="margin: 0; opacity: 0.8;">Horas Competencias</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php 
                    $porcentajeCobertura = 0;
                    if ($diseño_actual && $diseño_actual['horasDesarrolloDiseño'] > 0) {
                        $porcentajeCobertura = ($totalHorasCompetencias / $diseño_actual['horasDesarrolloDiseño']) * 100;
                    }
                    echo number_format($porcentajeCobertura, 1);
                    ?>%
                </h3>
                <p style="margin: 0; opacity: 0.8;">Cobertura del Diseño</p>
            </div>
        </div>
    </div>
<?php endif; ?>
