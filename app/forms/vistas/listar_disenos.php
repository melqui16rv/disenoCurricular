<div class="card-header">
    <div class="flex-between">
        <div>
            <h2 class="card-title"><i class="fas fa-list"></i> Diseños Curriculares</h2>
            <p class="card-subtitle">Gestiona todos los programas formativos del SENA</p>
        </div>
        <a href="?accion=crear&tipo=disenos" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Diseño
        </a>
    </div>
</div>

<?php
// Incluir funciones de paginación
require_once 'funciones_paginacion.php';

// Preparar filtros para la función de paginación
$filtros_array = [
    'busqueda' => $busqueda ?? '',
    'horas_min' => $horas_min ?? '',
    'horas_max' => $horas_max ?? '',
    'meses_min' => $meses_min ?? '',
    'meses_max' => $meses_max ?? '',
    'nivel_academico' => $nivel_academico ?? '',
    'red_tecnologica' => $red_tecnologica ?? ''
];

// Generar filtros y paginación
echo generarFiltrosYPaginacion(
    'listar',
    '',
    '',
    $filtros_array,
    $total_registros ?? 0,
    $pagina ?? 1,
    $registros_por_pagina ?? 10,
    $total_paginas ?? 1
);
?>

<?php if (empty($diseños)): ?>
    <div class="text-center" style="padding: 3rem;">
        <i class="fas fa-folder-open" style="font-size: 4rem; color: #bdc3c7; margin-bottom: 1rem;"></i>
        <h3 style="color: #7f8c8d;">No hay diseños curriculares registrados</h3>
        <p style="color: #95a5a6;">Comienza creando tu primer diseño curricular</p>
        <a href="?accion=crear&tipo=disenos" class="btn btn-primary mt-3">
            <i class="fas fa-plus"></i> Crear Primer Diseño
        </a>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-code"></i> Código</th>
                    <th><i class="fas fa-graduation-cap"></i> Programa</th>
                    <th><i class="fas fa-network-wired"></i> Red Tecnológica</th>
                    <th><i class="fas fa-clock"></i> Horas Totales</th>
                    <th><i class="fas fa-calendar"></i> Meses Totales</th>
                    <th><i class="fas fa-user-graduate"></i> Nivel Ingreso</th>
                    <th><i class="fas fa-cogs"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($diseños as $diseño): ?>
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-weight: bold; color: #2c3e50;">
                                <?php echo htmlspecialchars($diseño['codigoDiseño']); ?>
                            </span>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($diseño['nombrePrograma'] ?? ''); ?></strong>
                            <br>
                            <small class="text-muted">
                                Código: <?php echo htmlspecialchars($diseño['codigoPrograma'] ?? ''); ?> 
                                | Versión: <?php echo htmlspecialchars($diseño['versionPrograma'] ?? ''); ?>
                            </small>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;">
                                <div><strong>Red:</strong> <?php echo htmlspecialchars($diseño['redTecnologica'] ?? ''); ?></div>
                                <div><strong>Línea:</strong> <?php echo htmlspecialchars($diseño['lineaTecnologica'] ?? ''); ?></div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;">
                                <div><i class="fas fa-book" style="color: #3498db;"></i> Lectivas: <strong><?php echo number_format($diseño['horasDesarrolloLectiva'] ?? 0, 0); ?></strong></div>
                                <div><i class="fas fa-industry" style="color: #e74c3c;"></i> Productivas: <strong><?php echo number_format($diseño['horasDesarrolloProductiva'] ?? 0, 0); ?></strong></div>
                                <div style="border-top: 1px solid #eee; margin-top: 3px; padding-top: 3px;">
                                    <i class="fas fa-calculator" style="color: #27ae60;"></i> Total: <strong><?php echo number_format($diseño['horasDesarrolloDiseño'] ?? 0, 0); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;">
                                <div><i class="fas fa-book" style="color: #3498db;"></i> Lectivos: <strong><?php echo number_format($diseño['mesesDesarrolloLectiva'] ?? 0, 1); ?></strong></div>
                                <div><i class="fas fa-industry" style="color: #e74c3c;"></i> Productivos: <strong><?php echo number_format($diseño['mesesDesarrolloProductiva'] ?? 0, 1); ?></strong></div>
                                <div style="border-top: 1px solid #eee; margin-top: 3px; padding-top: 3px;">
                                    <i class="fas fa-calculator" style="color: #27ae60;"></i> Total: <strong><?php echo number_format($diseño['mesesDesarrolloDiseño'] ?? 0, 1); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;">
                                <div><strong><?php echo htmlspecialchars($diseño['nivelAcademicoIngreso'] ?? ''); ?></strong></div>
                                <div>Grado: <span style="color: #e74c3c; font-weight: bold;"><?php echo htmlspecialchars($diseño['gradoNivelAcademico'] ?? ''); ?></span></div>
                                <div>Edad mín: <span style="color: #3498db;"><?php echo htmlspecialchars($diseño['edadMinima'] ?? ''); ?> años</span></div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                <a href="?accion=ver_competencias&codigo=<?php echo urlencode($diseño['codigoDiseño']); ?>" 
                                   class="btn btn-primary btn-sm" title="Ver Competencias">
                                    <i class="fas fa-tasks"></i> Competencias
                                </a>
                                <a href="?accion=editar&tipo=disenos&codigo=<?php echo urlencode($diseño['codigoDiseño']); ?>" 
                                   class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?accion=eliminar&tipo=disenos&codigo=<?php echo urlencode($diseño['codigoDiseño']); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Estás seguro de eliminar este diseño? Se eliminarán también todas sus competencias y RAPs asociados.')"
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
                <h3 style="margin: 0; font-size: 2rem; color: #fff;"><?php echo count($diseños); ?></h3>
                <p style="margin: 0; opacity: 0.8;">Diseños Curriculares</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php 
                    $totalHoras = array_sum(array_column($diseños, 'horasDesarrolloDiseño'));
                    echo number_format($totalHoras, 0);
                    ?>
                </h3>
                <p style="margin: 0; opacity: 0.8;">Horas Totales</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php 
                    $totalMeses = array_sum(array_column($diseños, 'mesesDesarrolloDiseño'));
                    echo number_format($totalMeses, 1);
                    ?>
                </h3>
                <p style="margin: 0; opacity: 0.8;">Meses Totales</p>
            </div>
        </div>
    </div>
<?php endif; ?>
