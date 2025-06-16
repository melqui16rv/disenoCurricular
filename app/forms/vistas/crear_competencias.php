<div class="card-header">
    <h2 class="card-title"><i class="fas fa-plus-circle"></i> Crear Nueva Competencia</h2>
    <p class="card-subtitle">Agregar competencia al diseño curricular</p>
</div>

<form method="POST" id="formCompetencia">
    <input type="hidden" name="codigoDiseño" value="<?php echo htmlspecialchars($_GET['codigoDiseño'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Diseño Curricular:</strong> <?php echo htmlspecialchars($_GET['codigoDiseño'] ?? ''); ?>
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
</script>
