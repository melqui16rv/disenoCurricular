<?php
// Vista especializada para completar información faltante en RAPs
?>

<div class="card-header">
    <h2 class="card-title"><i class="fas fa-clipboard-check"></i> Completar Información del RAP</h2>
    <p class="card-subtitle">Complete solo los campos faltantes marcados en rojo</p>
</div>

<?php if (!$rap_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró el RAP especificado.
    </div>
    <a href="?accion=completar_informacion" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver a Completar Información
    </a>
<?php else: ?>

    <!-- Información del RAP actual -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Completando información para:</strong> <?php echo htmlspecialchars($rap_actual['nombreRap'] ?? 'RAP'); ?>
        <br><strong>Código:</strong> <?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaReporteRap']); ?>
        (No se puede modificar el código)
        <div class="d-flex gap-2 mt-2">
            <button type="button" class="btn btn-sm btn-outline-primary btn-toggle" onclick="toggleDiseñoInfo()" id="btnToggleDiseño">
                <i class="fas fa-eye"></i> Ver diseño curricular
            </button>
            <button type="button" class="btn btn-sm btn-outline-success btn-toggle" onclick="toggleCompetenciaInfo()" id="btnToggleCompetencia">
                <i class="fas fa-eye"></i> Ver competencia
            </button>
        </div>
    </div>

    <!-- Panel desplegable con información del diseño curricular -->
    <div id="diseñoInfoPanel" class="card info-panel diseño-panel" style="display: none;">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-graduation-cap"></i> 
                Información del Diseño Curricular
            </h5>
            <?php 
            // Extraer código de la competencia del RAP
            $partes = explode('-', $rap_actual['codigoDiseñoCompetenciaReporteRap']);
            $codigoCompetencia = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
            
            if (isset($diseño_actual) && $diseño_actual): 
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-hashtag"></i> Código del Diseño:</strong> <?php echo htmlspecialchars($diseño_actual['codigoDiseño']); ?></p>
                        <p><strong><i class="fas fa-tag"></i> Código del Programa:</strong> <?php echo htmlspecialchars($diseño_actual['codigoPrograma']); ?></p>
                        <p><strong><i class="fas fa-code-branch"></i> Versión:</strong> <?php echo htmlspecialchars($diseño_actual['versionPrograma']); ?></p>
                        <p><strong><i class="fas fa-school"></i> Nivel de Ingreso:</strong> <?php echo htmlspecialchars($diseño_actual['nivelAcademicoIngreso'] ?? ''); ?></p>
                        <p><strong><i class="fas fa-medal"></i> Grado:</strong> <?php echo htmlspecialchars($diseño_actual['gradoNivelAcademico'] ?? ''); ?></p>
                        <p><strong><i class="fas fa-birthday-cake"></i> Edad Mínima:</strong> <?php echo htmlspecialchars($diseño_actual['edadMinima'] ?? ''); ?> años</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-clock"></i> Horas Lectivas:</strong> <?php echo number_format($diseño_actual['horasDesarrolloLectiva'] ?? 0, 0); ?>h</p>
                        <p><strong><i class="fas fa-industry"></i> Horas Productivas:</strong> <?php echo number_format($diseño_actual['horasDesarrolloProductiva'] ?? 0, 0); ?>h</p>
                        <p><strong><i class="fas fa-calculator"></i> Total Horas:</strong> <span class="text-success fw-bold"><?php echo number_format($diseño_actual['horasDesarrolloDiseño'] ?? 0, 0); ?>h</span></p>
                        <p><strong><i class="fas fa-calendar"></i> Meses Lectivos:</strong> <?php echo number_format($diseño_actual['mesesDesarrolloLectiva'] ?? 0, 1); ?></p>
                        <p><strong><i class="fas fa-calendar-alt"></i> Meses Productivos:</strong> <?php echo number_format($diseño_actual['mesesDesarrolloProductiva'] ?? 0, 1); ?></p>
                        <p><strong><i class="fas fa-calendar-check"></i> Total Meses:</strong> <span class="text-success fw-bold"><?php echo number_format($diseño_actual['mesesDesarrolloDiseño'] ?? 0, 1); ?></span></p>
                    </div>
                </div>
                <div class="mt-3">
                    <p><strong><i class="fas fa-graduation-cap"></i> Nombre del Programa:</strong></p>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($diseño_actual['nombrePrograma'] ?? '')); ?></p>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <p><strong><i class="fas fa-sitemap"></i> Línea Tecnológica:</strong></p>
                        <p class="text-muted small"><?php echo htmlspecialchars($diseño_actual['lineaTecnologica'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong><i class="fas fa-network-wired"></i> Red Tecnológica:</strong></p>
                        <p class="text-muted small"><?php echo htmlspecialchars($diseño_actual['redTecnologica'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong><i class="fas fa-brain"></i> Red de Conocimiento:</strong></p>
                        <p class="text-muted small"><?php echo htmlspecialchars($diseño_actual['redConocimiento'] ?? ''); ?></p>
                    </div>
                </div>
                <?php if (!empty($diseño_actual['formacionTrabajoDesarrolloHumano'])): ?>
                <div class="mt-2">
                    <p><strong><i class="fas fa-users"></i> Formación en Trabajo y Desarrollo Humano:</strong> 
                    <span class="badge <?php echo $diseño_actual['formacionTrabajoDesarrolloHumano'] === 'Si' ? 'bg-success' : 'bg-secondary'; ?>">
                        <?php echo htmlspecialchars($diseño_actual['formacionTrabajoDesarrolloHumano']); ?>
                    </span></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($diseño_actual['requisitosAdicionales'])): ?>
                <div class="mt-2">
                    <p><strong><i class="fas fa-clipboard-list"></i> Requisitos Adicionales:</strong></p>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars(substr($diseño_actual['requisitosAdicionales'], 0, 300))); ?><?php if (strlen($diseño_actual['requisitosAdicionales']) > 300) echo '...'; ?></p>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se pudo cargar la información del diseño curricular.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel desplegable con información de la competencia -->
    <div id="competenciaInfoPanel" class="card info-panel competencia-panel" style="display: none;">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-star"></i> 
                Información de la Competencia
            </h5>
            <?php 
            // Extraer código de la competencia del RAP
            $partes = explode('-', $rap_actual['codigoDiseñoCompetenciaReporteRap']);
            $codigoCompetencia = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
            
            if (isset($competencia_actual) && $competencia_actual): 
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-hashtag"></i> Código Completo:</strong> <?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetenciaReporte']); ?></p>
                        <p><strong><i class="fas fa-tag"></i> Código de Competencia:</strong> <?php echo htmlspecialchars($competencia_actual['codigoCompetenciaReporte'] ?? ''); ?></p>
                        <p><strong><i class="fas fa-clock"></i> Horas Asignadas:</strong> <span class="text-primary fw-bold"><?php echo number_format($competencia_actual['horasDesarrolloCompetencia'] ?? 0, 0); ?>h</span></p>
                    </div>
                    <div class="col-md-6">
                        <?php 
                        // Extraer información del diseño de la competencia
                        $partesDiseño = explode('-', $competencia_actual['codigoDiseñoCompetenciaReporte']);
                        $codigoDiseño = $partesDiseño[0] . '-' . $partesDiseño[1];
                        ?>
                        <p><strong><i class="fas fa-file-alt"></i> Pertenece al Diseño:</strong> <?php echo htmlspecialchars($codigoDiseño); ?></p>
                        <?php if (isset($diseño_actual) && $diseño_actual): ?>
                        <p><strong><i class="fas fa-graduation-cap"></i> Programa:</strong> <?php echo htmlspecialchars($diseño_actual['codigoPrograma']); ?> (v<?php echo htmlspecialchars($diseño_actual['versionPrograma']); ?>)</p>
                        <p><strong><i class="fas fa-percentage"></i> Cobertura del Diseño:</strong> 
                            <?php 
                            $totalHorasDiseño = $diseño_actual['horasDesarrolloDiseño'] ?? 1;
                            $horasCompetencia = $competencia_actual['horasDesarrolloCompetencia'] ?? 0;
                            $porcentaje = $totalHorasDiseño > 0 ? ($horasCompetencia / $totalHorasDiseño) * 100 : 0;
                            ?>
                            <span class="badge bg-info"><?php echo number_format($porcentaje, 1); ?>%</span>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-3">
                    <p><strong><i class="fas fa-star"></i> Nombre de la Competencia:</strong></p>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($competencia_actual['nombreCompetencia'] ?? '')); ?></p>
                </div>
                <?php if (!empty($competencia_actual['normaUnidadCompetencia'])): ?>
                <div class="mt-3">
                    <p><strong><i class="fas fa-file-contract"></i> Norma Unidad Competencia:</strong></p>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars(substr($competencia_actual['normaUnidadCompetencia'], 0, 200))); ?><?php if (strlen($competencia_actual['normaUnidadCompetencia']) > 200) echo '...'; ?></p>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se pudo cargar la información de la competencia.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sección de Comparación de RAPs -->
    <div class="card mb-4" style="border: 2px solid #17a2b8;">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-balance-scale"></i> Comparar RAPs de la misma competencia
                <button type="button" class="btn btn-sm btn-outline-light float-right" onclick="toggleComparacion()" id="btnToggleComparacion">
                    <i class="fas fa-eye"></i> Ver comparación
                </button>
            </h5>
        </div>
        <div id="comparacionPanel" class="card-body" style="display: none;">
            <p class="text-muted mb-3">
                <i class="fas fa-info-circle"></i> 
                Consulta cómo otros diseños curriculares han definido los RAPs para esta misma competencia.
            </p>
            
            <div id="comparacionContent">
                <div class="text-center">
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only">Cargando comparación...</span>
                    </div>
                    <p class="mt-2">Cargando datos de comparación...</p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" id="formCompletarRap">
        <input type="hidden" name="codigoDiseñoCompetenciaReporteRap" value="<?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaReporteRap'] ?? ''); ?>">
        <input type="hidden" name="completar_modo" value="1">
        
        <!-- Información básica del RAP -->
        <div class="form-section">
            <h3><i class="fas fa-list-ul"></i> Información del RAP</h3>
            
            <div class="form-row">
                <div class="form-group <?php echo empty($rap_actual['codigoRapDiseño']) ? 'campo-faltante' : ''; ?>">
                    <label for="codigoRapDiseño">
                        <i class="fas fa-hashtag"></i> Código RAP Diseño
                        <?php if (empty($rap_actual['codigoRapDiseño'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="codigoRapDiseño" name="codigoRapDiseño" class="form-control" 
                           value="<?php echo htmlspecialchars($rap_actual['codigoRapDiseño'] ?? ''); ?>"
                           placeholder="Ingrese el código RAP diseño">
                </div>
                
                <div class="form-group <?php echo ($rap_actual['horasDesarrolloRap'] ?? 0) <= 0 ? 'campo-faltante' : ''; ?>">
                    <label for="horasDesarrolloRap">
                        <i class="fas fa-clock"></i> Horas de Desarrollo
                        <?php if (($rap_actual['horasDesarrolloRap'] ?? 0) <= 0): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="number" id="horasDesarrolloRap" name="horasDesarrolloRap" 
                           class="form-control" min="0.01" step="0.01" 
                           value="<?php echo htmlspecialchars($rap_actual['horasDesarrolloRap'] ?? ''); ?>"
                           placeholder="0.00">
                </div>
            </div>
        </div>

        <!-- Nombre del RAP -->
        <div class="form-section">
            <div class="form-group <?php echo empty($rap_actual['nombreRap']) ? 'campo-faltante' : ''; ?>">
                <label for="nombreRap">
                    <i class="fas fa-tag"></i> Nombre del RAP
                    <?php if (empty($rap_actual['nombreRap'])): ?>
                        <span class="campo-requerido">* FALTA</span>
                    <?php endif; ?>
                </label>
                <textarea id="nombreRap" name="nombreRap" class="form-control" rows="3" 
                          placeholder="Describe el nombre completo del RAP..."><?php echo htmlspecialchars($rap_actual['nombreRap'] ?? ''); ?></textarea>
                <small class="text-muted">Describa de manera clara y completa el Resultado de Aprendizaje Previsto (RAP)</small>
            </div>
        </div>

        <!-- Información contextual (solo lectura) -->
        <?php if (isset($competencia_actual) && $competencia_actual): ?>
        <div class="form-section info-contextual">
            <h3><i class="fas fa-info-circle"></i> Información Contextual</h3>
            <div class="alert alert-light">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-star"></i> Competencia:</strong><br>
                        <?php echo htmlspecialchars($competencia_actual['nombreCompetencia'] ?? 'No especificada'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-clock"></i> Horas de la Competencia:</strong><br>
                        <?php echo number_format($competencia_actual['horasDesarrolloCompetencia'] ?? 0, 2); ?> horas</p>
                    </div>
                </div>
                <?php if (isset($diseño_actual) && $diseño_actual): ?>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <p><strong><i class="fas fa-graduation-cap"></i> Programa:</strong><br>
                        <?php echo htmlspecialchars($diseño_actual['nombrePrograma'] ?? 'No especificado'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Botones de acción -->
        <div class="flex-between" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e9ecef;">
            <a href="?accion=completar_informacion" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver sin Guardar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Completar Información
            </button>
        </div>
    </form>

<?php endif; ?>

<style>
.campo-faltante {
    border-left: 4px solid #e74c3c;
    background-color: #fdf2f2;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    margin-bottom: 1rem;
}

.campo-requerido {
    background: #e74c3c;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
    margin-left: 0.5rem;
}

.form-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-section h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #ecf0f1;
}

.info-contextual {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.info-contextual h3 {
    color: #495057;
}

.alert-info {
    border-left: 4px solid #3498db;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCompletarRap');
    
    form.addEventListener('submit', function(e) {
        const nombreRap = document.getElementById('nombreRap').value.trim();
        const horas = parseFloat(document.getElementById('horasDesarrolloRap').value) || 0;
        
        if (!nombreRap) {
            e.preventDefault();
            alert('Por favor, ingrese el nombre del RAP.');
            return;
        }
        
        if (horas <= 0) {
            e.preventDefault();
            alert('Las horas de desarrollo deben ser mayor a cero.');
            return;
        }
        
        if (!confirm('¿Está seguro de completar esta información?')) {
            e.preventDefault();
        }
    });
});

// Función para mostrar/ocultar información del diseño curricular
function toggleDiseñoInfo() {
    const panel = document.getElementById('diseñoInfoPanel');
    const btn = document.getElementById('btnToggleDiseño');
    
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar diseño curricular';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        panel.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye"></i> Ver diseño curricular';
    }
}

// Función para mostrar/ocultar información de la competencia
function toggleCompetenciaInfo() {
    const panel = document.getElementById('competenciaInfoPanel');
    const btn = document.getElementById('btnToggleCompetencia');
    
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar competencia';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        panel.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye"></i> Ver competencia';
    }
}

// Función para mostrar/ocultar sección de comparación
function toggleComparacion() {
    const panel = document.getElementById('comparacionPanel');
    const btn = document.getElementById('btnToggleComparacion');
    
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar comparación';
        
        // Cargar datos de comparación si no se han cargado
        if (!panel.dataset.loaded) {
            cargarComparacion();
            panel.dataset.loaded = 'true';
        }
        
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        panel.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye"></i> Ver comparación';
    }
}

// Función para cargar datos de comparación via AJAX
function cargarComparacion() {
    const codigoRapCompleto = '<?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaReporteRap'] ?? ''); ?>';
    const partes = codigoRapCompleto.split('-');
    const codigoCompetenciaReal = partes[2]; // Extraer código de competencia
    const disenoActual = partes[0] + '-' + partes[1]; // Extraer código de diseño actual
    
    // Debug: mostrar los valores extraídos
    console.log('Debug - Código RAP completo:', codigoRapCompleto);
    console.log('Debug - Partes:', partes);
    console.log('Debug - Código competencia extraído:', codigoCompetenciaReal);
    console.log('Debug - Diseño actual extraído:', disenoActual);
    
    // Validar que tenemos los datos necesarios
    if (!codigoCompetenciaReal || partes.length < 3) {
        document.getElementById('comparacionContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error:</strong> No se pudo extraer el código de competencia del RAP.
                <br><small>Código RAP: ${codigoRapCompleto}</small>
            </div>
        `;
        return;
    }
    
    // Mostrar indicador de carga
    document.getElementById('comparacionContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Cargando comparación...</span>
            </div>
            <p class="mt-2">Cargando datos de comparación...</p>
        </div>
    `;
    
    fetch('ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `accion_ajax=obtener_comparacion_raps&codigoCompetencia=${codigoCompetenciaReal}&disenoActual=${disenoActual}`
    })
    .then(response => {
        // Verificar si la respuesta es válida
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text(); // Obtener como texto primero
    })
    .then(responseText => {
        // Intentar parsear como JSON
        try {
            const data = JSON.parse(responseText);
            
            // Verificar que los datos sean válidos
            if (typeof data !== 'object' || data === null) {
                throw new Error('Los datos recibidos no son válidos');
            }
            
            mostrarComparacion(data);
        } catch (parseError) {
            console.error('Error al parsear JSON:', parseError);
            console.error('Respuesta recibida:', responseText.substring(0, 500));
            throw new Error('El servidor no devolvió datos en formato válido.');
        }
    })
    .catch(error => {
        console.error('Error en cargarComparacion:', error);
        
        // Mostrar error específico basado en el tipo
        let errorMessage = '';
        if (error.name === 'SyntaxError') {
            errorMessage = 'Error de formato en la respuesta del servidor. Posible error de configuración.';
        } else if (error.message.includes('HTTP')) {
            errorMessage = `Error del servidor: ${error.message}`;
        } else if (error.message.includes('JSON')) {
            errorMessage = 'Error: El servidor no devolvió datos en formato válido.';
        } else {
            errorMessage = `Error de conexión: ${error.message}`;
        }
        
        document.getElementById('comparacionContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error al cargar la comparación:</strong><br>
                ${errorMessage}
                <br><br>
                <button class="btn btn-sm btn-outline-primary" onclick="cargarComparacion()">
                    <i class="fas fa-redo"></i> Reintentar
                </button>
            </div>
        `;
    });
}

// Función para mostrar los datos de comparación
function mostrarComparacion(data) {
    const content = document.getElementById('comparacionContent');
    
    if (!data.success || !data.data || data.data.length === 0) {
        content.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i>
                <strong>Sin resultados:</strong> No se encontraron otros diseños curriculares con la misma competencia.
                <br><small class="text-muted">Esto es normal si es el primer diseño que usa esta competencia.</small>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="alert alert-success mb-3">
            <i class="fas fa-check-circle"></i>
            <strong>¡Excelente!</strong> Se encontraron ${data.data.length} diseño(s) curricular(es) con la misma competencia para comparar.
        </div>
    `;
    
    data.data.forEach((item, index) => {
        const diseno = item.diseno;
        const raps = item.raps;
        const totalHoras = item.totalHorasRaps || 0;
        const disenoId = `diseno-${index}`;
        
        html += `
            <div class="card mb-3" style="border-left: 4px solid #007bff;">
                <div class="card-header bg-light p-2" style="cursor: pointer;" onclick="toggleDisenoRaps('${disenoId}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-graduation-cap text-primary"></i>
                                <strong>${diseno.nombrePrograma}</strong> 
                                <span class="badge badge-info ml-2">Versión ${diseno.versionPrograma}</span>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-code"></i> Código: ${diseno.codigoDiseño} | 
                                <i class="fas fa-list"></i> RAPs: ${raps.length} | 
                                <i class="fas fa-clock"></i> Total Horas: ${totalHoras}h
                            </small>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-chevron-down text-primary" id="chevron-${disenoId}"></i>
                            <br><small class="text-muted">Click para expandir</small>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="${disenoId}" style="display: none;">
        `;
        
        if (raps.length === 0) {
            html += `
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    Este diseño curricular aún no tiene RAPs definidos para esta competencia.
                </div>
            `;
        } else {
            html += `
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 100px;"><i class="fas fa-hashtag"></i> Código</th>
                                <th><i class="fas fa-bullseye"></i> Resultado de Aprendizaje</th>
                                <th style="width: 80px;"><i class="fas fa-clock"></i> Horas</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            raps.forEach((rap, rapIndex) => {
                html += `
                    <tr>
                        <td>
                            <span class="badge badge-secondary">${rap.codigoRapDiseño}</span>
                        </td>
                        <td class="small" style="line-height: 1.4;">
                            ${rap.nombreRap}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-outline-primary">${rap.horasDesarrolloRap}h</span>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        html += `
                </div>
            </div>
        `;
    });
    
    // Agregar información de debug si está disponible
    if (data.debug) {
        html += `
            <details class="mt-3">
                <summary class="text-muted small" style="cursor: pointer;">
                    <i class="fas fa-info-circle"></i> Información de depuración
                </summary>
                <div class="mt-2 p-2 bg-light small">
                    <strong>Competencia buscada:</strong> ${data.debug.codigoCompetencia}<br>
                    <strong>Diseño actual excluido:</strong> ${data.debug.disenoActual || 'Ninguno'}<br>
                    <strong>Diseños encontrados:</strong> ${data.debug.totalDisenosEncontrados}<br>
                    <strong>Comparaciones procesadas:</strong> ${data.debug.totalComparaciones}
                </div>
            </details>
        `;
    }
    
    content.innerHTML = html;
}

// Función para expandir/colapsar RAPs de un diseño
function toggleDisenoRaps(disenoId) {
    const panel = document.getElementById(disenoId);
    const chevron = document.getElementById(`chevron-${disenoId}`);
    
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        chevron.className = 'fas fa-chevron-up text-primary';
        chevron.parentElement.querySelector('small').textContent = 'Click para colapsar';
    } else {
        panel.style.display = 'none';
        chevron.className = 'fas fa-chevron-down text-primary';
        chevron.parentElement.querySelector('small').textContent = 'Click para expandir';
    }
}
</script>
