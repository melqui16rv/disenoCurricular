<div class="card-header">
    <h2 class="card-title"><i class="fas fa-edit"></i> Editar Competencia</h2>
    <p class="card-subtitle">Modificar información de la competencia</p>
</div>

<?php if (!$competencia_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró la competencia especificada.
    </div>
    <a href="?accion=listar" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver al listado
    </a>
<?php else: ?>

<form method="POST" id="formEditarCompetencia">
    <input type="hidden" name="codigoDiseñoCompetencia" value="<?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia']); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Código de la Competencia:</strong> <?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia']); ?>
        (No se puede modificar el código)
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-hashtag"></i> Código de la Competencia</label>
            <input type="text" class="form-control" readonly 
                   value="<?php echo htmlspecialchars($competencia_actual['codigoCompetencia']); ?>"
                   style="background: #e9ecef;">
        </div>
        <div class="form-group">
            <label for="horasDesarrolloCompetencia"><i class="fas fa-clock"></i> Horas de Desarrollo *</label>
            <input type="number" id="horasDesarrolloCompetencia" name="horasDesarrolloCompetencia" class="form-control" required 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($competencia_actual['horasDesarrolloCompetencia']); ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="nombreCompetencia"><i class="fas fa-star"></i> Nombre de la Competencia *</label>
            <input type="text" id="nombreCompetencia" name="nombreCompetencia" class="form-control" required 
                   value="<?php echo htmlspecialchars($competencia_actual['nombreCompetencia']); ?>"
                   placeholder="Ejemplo: Desarrollar el sistema que cumpla con los requisitos de la solución informática">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="normaUnidadCompetencia"><i class="fas fa-file-alt"></i> Norma de Unidad de Competencia</label>
            <textarea id="normaUnidadCompetencia" name="normaUnidadCompetencia" class="form-control" rows="4" 
                      placeholder="Descripción de la norma de unidad de competencia..."><?php echo htmlspecialchars($competencia_actual['normaUnidadCompetencia']); ?></textarea>
        </div>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
        <i class="fas fa-user-tie"></i> Requisitos del Instructor
    </h3>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="requisitosAcademicosInstructor"><i class="fas fa-graduation-cap"></i> Requisitos Académicos del Instructor</label>
            <textarea id="requisitosAcademicosInstructor" name="requisitosAcademicosInstructor" class="form-control" rows="4" 
                      placeholder="Ejemplo: Profesional en Ingeniería de Sistemas, Ingeniería de Software, Tecnología en Desarrollo de Software o áreas afines..."><?php echo htmlspecialchars($competencia_actual['requisitosAcademicosInstructor']); ?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="experienciaLaboralInstructor"><i class="fas fa-briefcase"></i> Experiencia Laboral del Instructor</label>
            <textarea id="experienciaLaboralInstructor" name="experienciaLaboralInstructor" class="form-control" rows="4" 
                      placeholder="Ejemplo: Veinticuatro (24) meses de experiencia: de los cuales dieciocho (18) meses estarán relacionados con el ejercicio de la profesión u ocupación objeto de la formación profesional..."><?php echo htmlspecialchars($competencia_actual['experienciaLaboralInstructor']); ?></textarea>
        </div>
    </div>

    <div class="flex-between" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e9ecef;">
        <?php 
        // Extraer el código del diseño de la competencia
        $partes = explode('-', $competencia_actual['codigoDiseñoCompetencia']);
        $codigoDiseño = $partes[0] . '-' . $partes[1];
        ?>
        <a href="?accion=ver_competencias&codigo=<?php echo urlencode($codigoDiseño); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Actualizar Competencia
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario
    document.getElementById('formEditarCompetencia').addEventListener('submit', function(e) {
        const nombreCompetencia = document.getElementById('nombreCompetencia').value.trim();
        const horas = parseFloat(document.getElementById('horasDesarrolloCompetencia').value) || 0;
        
        if (!nombreCompetencia) {
            e.preventDefault();
            alert('Por favor, completa el nombre de la competencia.');
            return;
        }
        
        if (horas <= 0) {
            e.preventDefault();
            alert('Las horas de desarrollo deben ser mayor a cero.');
            return;
        }
        
        if (!confirm('¿Estás seguro de actualizar esta competencia?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php endif; ?>
