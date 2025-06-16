<div class="card-header">
    <h2 class="card-title"><i class="fas fa-plus-circle"></i> Crear Nueva Competencia</h2>
    <p class="card-subtitle">Agregar competencia al diseño curricular</p>
</div>

<form method="POST" id="formCompetencia">
    <input type="hidden" name="codigoDiseño" value="<?php echo htmlspecialchars($_GET['codigoDiseño'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Diseño Curricular:</strong> <?php echo htmlspecialchars($_GET['codigoDiseño'] ?? ''); ?>
        <button type="button" class="btn btn-sm btn-outline-primary btn-toggle ms-2" onclick="toggleDiseñoInfo()" id="btnToggleDiseño">
            <i class="fas fa-eye"></i> Ver detalles del diseño
        </button>
    </div>

    <!-- Panel desplegable con información del diseño -->
    <div id="diseñoInfoPanel" class="card info-panel diseño-panel" style="display: none;">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-graduation-cap"></i> 
                Información del Diseño Curricular
            </h5>
            <?php 
            $codigoDiseño = $_GET['codigoDiseño'] ?? '';
            if ($codigoDiseño && isset($diseño_actual) && $diseño_actual): 
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

    <div class="form-row">
        <div class="form-group">
            <label for="codigoCompetencia"><i class="fas fa-hashtag"></i> Código de la Competencia *</label>
            <input type="text" id="codigoCompetencia" name="codigoCompetencia" class="form-control" required 
                   placeholder="Ejemplo: 220201501" maxlength="50">
            <small class="text-muted">
                El código final será: <?php echo htmlspecialchars($_GET['codigoDiseño'] ?? ''); ?>-[Código competencia]
            </small>
        </div>
        <div class="form-group">
            <label for="horasDesarrolloCompetencia"><i class="fas fa-clock"></i> Horas de Desarrollo *</label>
            <input type="number" id="horasDesarrolloCompetencia" name="horasDesarrolloCompetencia" class="form-control" required 
                   min="0" step="0.01" placeholder="0.00">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="nombreCompetencia"><i class="fas fa-star"></i> Nombre de la Competencia *</label>
            <input type="text" id="nombreCompetencia" name="nombreCompetencia" class="form-control" required 
                   placeholder="Ejemplo: Desarrollar el sistema que cumpla con los requisitos de la solución informática">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="normaUnidadCompetencia"><i class="fas fa-file-alt"></i> Norma de Unidad de Competencia</label>
            <textarea id="normaUnidadCompetencia" name="normaUnidadCompetencia" class="form-control" rows="4" 
                      placeholder="Descripción de la norma de unidad de competencia..."></textarea>
        </div>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
        <i class="fas fa-user-tie"></i> Requisitos del Instructor
    </h3>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="requisitosAcademicosInstructor"><i class="fas fa-graduation-cap"></i> Requisitos Académicos del Instructor</label>
            <textarea id="requisitosAcademicosInstructor" name="requisitosAcademicosInstructor" class="form-control" rows="4" 
                      placeholder="Ejemplo: Profesional en Ingeniería de Sistemas, Ingeniería de Software, Tecnología en Desarrollo de Software o áreas afines..."></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="experienciaLaboralInstructor"><i class="fas fa-briefcase"></i> Experiencia Laboral del Instructor</label>
            <textarea id="experienciaLaboralInstructor" name="experienciaLaboralInstructor" class="form-control" rows="4" 
                      placeholder="Ejemplo: Veinticuatro (24) meses de experiencia: de los cuales dieciocho (18) meses estarán relacionados con el ejercicio de la profesión u ocupación objeto de la formación profesional..."></textarea>
        </div>
    </div>

    <div class="flex-between" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e9ecef;">
        <a href="?accion=ver_competencias&codigo=<?php echo urlencode($_GET['codigoDiseño'] ?? ''); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar Competencia
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario
    document.getElementById('formCompetencia').addEventListener('submit', function(e) {
        const codigoCompetencia = document.getElementById('codigoCompetencia').value.trim();
        const nombreCompetencia = document.getElementById('nombreCompetencia').value.trim();
        const horas = parseFloat(document.getElementById('horasDesarrolloCompetencia').value) || 0;
        
        if (!codigoCompetencia || !nombreCompetencia) {
            e.preventDefault();
            alert('Por favor, completa el código y nombre de la competencia.');
            return;
        }
        
        if (horas <= 0) {
            e.preventDefault();
            alert('Las horas de desarrollo deben ser mayor a cero.');
            return;
        }
        
        if (!confirm('¿Estás seguro de crear esta competencia?')) {
            e.preventDefault();
        }
    });
    
    // Mostrar vista previa del código completo
    document.getElementById('codigoCompetencia').addEventListener('input', function() {
        const codigoDiseño = '<?php echo htmlspecialchars($_GET['codigoDiseño'] ?? ''); ?>';
        const codigoCompetencia = this.value.trim();
        const codigoCompleto = codigoDiseño + (codigoCompetencia ? '-' + codigoCompetencia : '');
        
        // Actualizar el texto de ayuda
        const helpText = this.nextElementSibling;
        if (helpText) {
            helpText.textContent = 'El código final será: ' + codigoCompleto;
        }
    });
});

// Función para mostrar/ocultar información del diseño
function toggleDiseñoInfo() {
    const panel = document.getElementById('diseñoInfoPanel');
    const btn = document.getElementById('btnToggleDiseño');
    const icon = btn.querySelector('i');
    
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar detalles del diseño';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        panel.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye"></i> Ver detalles del diseño';
    }
}
</script>
