<div class="card-header">
    <h2 class="card-title"><i class="fas fa-plus-circle"></i> Crear Nuevo RAP</h2>
    <p class="card-subtitle">Agregar Resultado de Aprendizaje a la competencia</p>
</div>

<form method="POST" id="formRap">
    <input type="hidden" name="codigoDiseñoCompetencia" value="<?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Competencia:</strong> <?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>
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
            $codigoCompetencia = $_GET['codigoDiseñoCompetencia'] ?? '';
            if ($codigoCompetencia && isset($diseño_actual) && $diseño_actual): 
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
            $codigoCompetencia = $_GET['codigoDiseñoCompetencia'] ?? '';
            if ($codigoCompetencia && isset($competencia_actual) && $competencia_actual): 
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-hashtag"></i> Código Completo:</strong> <?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia']); ?></p>
                        <p><strong><i class="fas fa-tag"></i> Código de Competencia:</strong> <?php echo htmlspecialchars($competencia_actual['codigoCompetencia']); ?></p>
                        <p><strong><i class="fas fa-clock"></i> Horas Asignadas:</strong> <span class="text-primary fw-bold"><?php echo number_format($competencia_actual['horasDesarrolloCompetencia'] ?? 0, 0); ?>h</span></p>
                    </div>
                    <div class="col-md-6">
                        <?php 
                        // Extraer información del diseño de la competencia
                        $partes = explode('-', $competencia_actual['codigoDiseñoCompetencia']);
                        $codigoDiseño = $partes[0] . '-' . $partes[1];
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
                    <p><strong><i class="fas fa-file-alt"></i> Norma de Unidad de Competencia:</strong></p>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars(substr($competencia_actual['normaUnidadCompetencia'], 0, 300))); ?><?php if (strlen($competencia_actual['normaUnidadCompetencia']) > 300) echo '... <span class="text-info">[Ver más en edición]</span>'; ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($competencia_actual['requisitosAcademicosInstructor'])): ?>
                <div class="mt-3">
                    <p><strong><i class="fas fa-user-graduate"></i> Requisitos Académicos del Instructor:</strong></p>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars(substr($competencia_actual['requisitosAcademicosInstructor'], 0, 200))); ?><?php if (strlen($competencia_actual['requisitosAcademicosInstructor']) > 200) echo '...'; ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($competencia_actual['experienciaLaboralInstructor'])): ?>
                <div class="mt-3">
                    <p><strong><i class="fas fa-briefcase"></i> Experiencia Laboral del Instructor:</strong></p>
                    <p class="text-muted small"><?php echo nl2br(htmlspecialchars(substr($competencia_actual['experienciaLaboralInstructor'], 0, 200))); ?><?php if (strlen($competencia_actual['experienciaLaboralInstructor']) > 200) echo '...'; ?></p>
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

    <div class="form-row">
        <div class="form-group">
            <label for="codigoRapDiseño"><i class="fas fa-hashtag"></i> Código del RAP (Diseño) *</label>
            <input type="text" id="codigoRapDiseño" name="codigoRapDiseño" class="form-control" required 
                   placeholder="Ejemplo: RA1" maxlength="20">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Este es el código que aparecerá en el diseño curricular. 
                El código técnico completo se generará automáticamente.
            </small>
        </div>
        <div class="form-group">
            <label for="horasDesarrolloRap"><i class="fas fa-clock"></i> Horas de Desarrollo *</label>
            <input type="number" id="horasDesarrolloRap" name="horasDesarrolloRap" class="form-control" required 
                   min="0" step="0.01" placeholder="0.00">
        </div>
    </div>

    <div class="alert alert-info" style="margin: 1rem 0;">
        <i class="fas fa-lightbulb"></i> 
        <strong>Código Automático:</strong> El sistema generará automáticamente un código técnico único.
        <br><strong>Ejemplo:</strong> Si este es el primer RAP de la competencia, el código será: 
        <code><?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>-1</code>
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

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="nombreRap"><i class="fas fa-bullseye"></i> Resultado de Aprendizaje *</label>
            <textarea id="nombreRap" name="nombreRap" class="form-control" rows="6" required 
                      placeholder="Describe detalladamente el resultado de aprendizaje que el estudiante debe lograr..."></textarea>
            <small class="text-muted">
                Describe claramente qué debe saber hacer el estudiante al finalizar este resultado de aprendizaje.
            </small>
        </div>
    </div>

    <div class="flex-between" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e9ecef;">
        <a href="?accion=ver_raps&codigo=<?php echo urlencode($_GET['codigoDiseñoCompetencia'] ?? ''); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar RAP
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario
    document.getElementById('formRap').addEventListener('submit', function(e) {
        const codigoRapDiseño = document.getElementById('codigoRapDiseño').value.trim();
        const nombreRap = document.getElementById('nombreRap').value.trim();
        const horas = parseFloat(document.getElementById('horasDesarrolloRap').value) || 0;
        
        if (!codigoRapDiseño || !nombreRap) {
            e.preventDefault();
            alert('Por favor, completa el código del RAP y resultado de aprendizaje.');
            return;
        }
        
        if (horas <= 0) {
            e.preventDefault();
            alert('Las horas de desarrollo deben ser mayor a cero.');
            return;
        }
        
        if (nombreRap.length < 20) {
            e.preventDefault();
            alert('El resultado de aprendizaje debe ser más descriptivo (mínimo 20 caracteres).');
            return;
        }
        
        if (!confirm('¿Estás seguro de crear este RAP? El código técnico se generará automáticamente.')) {
            e.preventDefault();
        }
    });
    
    // Mostrar vista previa del código completo
    document.getElementById('codigoRapDiseño').addEventListener('input', function() {
        const codigoCompetencia = '<?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>';
        const codigoRap = this.value.trim();
        
        // Actualizar el texto de ayuda
        const helpText = this.nextElementSibling;
        if (helpText) {
            if (codigoRap) {
                helpText.innerHTML = '<i class="fas fa-info-circle"></i> Código para diseño: <strong>' + codigoRap + '</strong><br>El código técnico completo se generará automáticamente al guardar.';
            } else {
                helpText.innerHTML = '<i class="fas fa-info-circle"></i> Este es el código que aparecerá en el diseño curricular. El código técnico completo se generará automáticamente.';
            }
        }
    });
    
    // Contador de caracteres para el resultado de aprendizaje
    const nombreRapTextarea = document.getElementById('nombreRap');
    const contadorDiv = document.createElement('div');
    contadorDiv.style.textAlign = 'right';
    contadorDiv.style.fontSize = '0.8rem';
    contadorDiv.style.color = '#6c757d';
    contadorDiv.style.marginTop = '5px';
    nombreRapTextarea.parentNode.appendChild(contadorDiv);
    
    function actualizarContador() {
        const texto = nombreRapTextarea.value;
        contadorDiv.textContent = `${texto.length} caracteres`;
        
        if (texto.length < 20) {
            contadorDiv.style.color = '#dc3545';
        } else if (texto.length < 50) {
            contadorDiv.style.color = '#ffc107';
        } else {
            contadorDiv.style.color = '#28a745';
        }
    }
    
    nombreRapTextarea.addEventListener('input', actualizarContador);
    actualizarContador(); // Inicializar contador
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

// Función para mostrar/ocultar sección de comparación de RAPs
function toggleComparacion() {
    const panel = document.getElementById('comparacionPanel');
    const btn = document.getElementById('btnToggleComparacion');
    
    if (panel.style.display === 'none') {
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
    const codigoCompetencia = '<?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>';
    const partes = codigoCompetencia.split('-');
    const codigoCompetenciaReal = partes[2]; // Extraer código de competencia
    const disenoActual = partes[0] + '-' + partes[1]; // Extraer código de diseño actual
    
    fetch('/app/forms/control/ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `accion=obtener_comparacion_raps&codigoCompetencia=${codigoCompetenciaReal}&disenoActual=${disenoActual}`
    })
    .then(response => response.json())
    .then(data => {
        mostrarComparacion(data);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('comparacionContent').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Error al cargar la comparación. Intenta nuevamente.
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
        
        html += `
            <div class="card mb-3" style="border-left: 4px solid #007bff;">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap"></i>
                        <strong>${diseno.nombrePrograma}</strong> 
                        <span class="badge badge-info ml-2">Versión ${diseno.versionPrograma}</span>
                    </h6>
                    <small class="text-muted">
                        Código: ${diseno.codigoDiseño} | 
                        RAPs: ${raps.length} | 
                        Total Horas: ${totalHoras}h
                    </small>
                </div>
                <div class="card-body">
        `;
        
        if (raps.length === 0) {
            html += `
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    Este diseño curricular aún no tiene RAPs definidos para esta competencia.
                </div>
            `;
        } else {
            html += `<div class="row">`;
            raps.forEach((rap, rapIndex) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card border-secondary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge badge-secondary">${rap.codigoRapDiseño}</span>
                                    <span class="badge badge-outline-primary">${rap.horasDesarrolloRap}h</span>
                                </div>
                                <p class="card-text small" style="line-height: 1.4;">
                                    ${rap.nombreRap}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;
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
</script>
