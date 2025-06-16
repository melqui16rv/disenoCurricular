<div class="card-header">
    <div class="flex-between">
        <div>
            <h2 class="card-title"><i class="fas fa-list-ul"></i> RAPs de la Competencia</h2>
            <p class="card-subtitle">
                <strong><?php echo htmlspecialchars($competencia_actual['nombreCompetencia'] ?? 'Competencia no encontrada'); ?></strong>
                <br>Código: <?php echo htmlspecialchars($_GET['codigo'] ?? ''); ?>
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="?accion=crear&tipo=raps&codigoDiseñoCompetencia=<?php echo urlencode($_GET['codigo'] ?? ''); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo RAP
            </a>
            <?php 
            // Extraer el código del diseño de la competencia
            $partes = explode('-', $_GET['codigo'] ?? '');
            $codigoDiseño = isset($partes[0]) && isset($partes[1]) ? $partes[0] . '-' . $partes[1] : '';
            ?>
            <a href="?accion=ver_competencias&codigo=<?php echo urlencode($codigoDiseño); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<?php if (!$competencia_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró la competencia especificada.
    </div>
<?php elseif (empty($raps)): ?>
    <div class="text-center" style="padding: 3rem;">
        <i class="fas fa-clipboard-check" style="font-size: 4rem; color: #bdc3c7; margin-bottom: 1rem;"></i>
        <h3 style="color: #7f8c8d;">No hay RAPs registrados</h3>
        <p style="color: #95a5a6;">Esta competencia aún no tiene Resultados de Aprendizaje asociados</p>
        <a href="?accion=crear&tipo=raps&codigoDiseñoCompetencia=<?php echo urlencode($_GET['codigo'] ?? ''); ?>" class="btn btn-primary mt-3">
            <i class="fas fa-plus"></i> Crear Primer RAP
        </a>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-code"></i> Código RAP</th>
                    <th><i class="fas fa-bullseye"></i> Resultado de Aprendizaje</th>
                    <th><i class="fas fa-clock"></i> Horas</th>
                    <th><i class="fas fa-cogs"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($raps as $rap): ?>
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-weight: bold; color: #2c3e50; font-size: 1.1rem;">
                                <?php echo htmlspecialchars($rap['codigoRapDiseño'] ?? 'Sin código'); ?>
                            </span>
                            <br>
                            <small class="text-muted" style="font-family: monospace; font-size: 0.8rem;">
                                Técnico: <?php echo htmlspecialchars($rap['codigoDiseñoCompetenciaRap']); ?>
                            </small>
                        </td>
                        <td>
                            <div style="line-height: 1.4;">
                                <?php echo nl2br(htmlspecialchars($rap['nombreRap'])); ?>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 1.2rem; font-weight: bold; color: #e74c3c;">
                                <?php echo number_format($rap['horasDesarrolloRap'] ?? 0, 0); ?>h
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                <a href="?accion=editar&tipo=raps&codigo=<?php echo urlencode($rap['codigoDiseñoCompetenciaRap']); ?>" 
                                   class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?accion=eliminar&tipo=raps&codigo=<?php echo urlencode($rap['codigoDiseñoCompetenciaRap']); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Estás seguro de eliminar este RAP?')"
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
                <h3 style="margin: 0; font-size: 2rem; color: #fff;"><?php echo count($raps); ?></h3>
                <p style="margin: 0; opacity: 0.8;">RAPs</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php 
                    $totalHorasRaps = array_sum(array_map(function($rap) { 
                        return $rap['horasDesarrolloRap'] ?? 0; 
                    }, $raps));
                    echo number_format($totalHorasRaps, 0);
                    ?>
                </h3>
                <p style="margin: 0; opacity: 0.8;">Horas RAPs</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php 
                    $porcentajeCobertura = 0;
                    $horasCompetencia = $competencia_actual['horasDesarrolloCompetencia'] ?? 0;
                    if ($competencia_actual && $horasCompetencia > 0) {
                        $porcentajeCobertura = ($totalHorasRaps / $horasCompetencia) * 100;
                    }
                    echo number_format($porcentajeCobertura, 1);
                    ?>%
                </h3>
                <p style="margin: 0; opacity: 0.8;">Cobertura Competencia</p>
            </div>
            <div class="text-center">
                <h3 style="margin: 0; font-size: 2rem; color: #fff;">
                    <?php echo number_format($competencia_actual['horasDesarrolloCompetencia'] ?? 0, 0); ?>
                </h3>
                <p style="margin: 0; opacity: 0.8;">Horas Competencia</p>
            </div>
        </div>
    </div>

    <?php if ($porcentajeCobertura > 100): ?>
        <div class="alert alert-warning" style="margin-top: 1rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Atención:</strong> Las horas de los RAPs (<?php echo number_format($totalHorasRaps ?? 0, 0); ?>h) 
            superan las horas asignadas a la competencia (<?php echo number_format($competencia_actual['horasDesarrolloCompetencia'] ?? 0, 0); ?>h).
        </div>
    <?php elseif ($porcentajeCobertura < 100 && $porcentajeCobertura > 0): ?>
        <div class="alert alert-info" style="margin-top: 1rem;">
            <i class="fas fa-info-circle"></i>
            <strong>Información:</strong> Faltan <?php echo number_format(($competencia_actual['horasDesarrolloCompetencia'] ?? 0) - ($totalHorasRaps ?? 0), 0); ?> 
            horas por cubrir en esta competencia.
        </div>
    <?php endif; ?>
<?php endif; ?>
