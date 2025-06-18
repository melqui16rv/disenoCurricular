<?php
// Vista especializada para completar información faltante en competencias
?>

<div class="card-header">
    <h2 class="card-title"><i class="fas fa-clipboard-check"></i> Completar Información de la Competencia</h2>
    <p class="card-subtitle">Complete solo los campos faltantes marcados en rojo</p>
</div>

<?php if (!$competencia_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró la competencia especificada.
    </div>
    <a href="?accion=completar_informacion" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver a Completar Información
    </a>
<?php else: ?>

    <!-- Información de la competencia actual -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Completando información para:</strong> <?php echo htmlspecialchars($competencia_actual['nombreCompetencia'] ?? 'Competencia'); ?>
        <br><strong>Código:</strong> <?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia']); ?>
    </div>

    <form method="POST" id="formCompletarCompetencia">
        <input type="hidden" name="codigoDiseñoCompetencia" value="<?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia'] ?? ''); ?>">
        <input type="hidden" name="completar_modo" value="1">
        
        <!-- Información básica de la competencia -->
        <div class="form-section">
            <h3><i class="fas fa-star"></i> Información de la Competencia</h3>
            
            <div class="form-row">
                <div class="form-group <?php echo empty($competencia_actual['nombreCompetencia']) ? 'campo-faltante' : ''; ?>">
                    <label for="nombreCompetencia">
                        <i class="fas fa-tag"></i> Nombre de la Competencia
                        <?php if (empty($competencia_actual['nombreCompetencia'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="nombreCompetencia" name="nombreCompetencia" class="form-control" 
                           value="<?php echo htmlspecialchars($competencia_actual['nombreCompetencia'] ?? ''); ?>"
                           placeholder="Ingrese el nombre de la competencia">
                </div>
                
                <div class="form-group <?php echo ($competencia_actual['horasDesarrolloCompetencia'] ?? 0) <= 0 ? 'campo-faltante' : ''; ?>">
                    <label for="horasDesarrolloCompetencia">
                        <i class="fas fa-clock"></i> Horas de Desarrollo
                        <?php if (($competencia_actual['horasDesarrolloCompetencia'] ?? 0) <= 0): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="number" id="horasDesarrolloCompetencia" name="horasDesarrolloCompetencia" 
                           class="form-control" min="0.01" step="0.01" 
                           value="<?php echo htmlspecialchars($competencia_actual['horasDesarrolloCompetencia'] ?? ''); ?>"
                           placeholder="0.00">
                </div>
            </div>
        </div>

        <!-- Norma y unidad de competencia -->
        <div class="form-section">
            <div class="form-group <?php echo empty($competencia_actual['normaUnidadCompetencia']) ? 'campo-faltante' : ''; ?>">
                <label for="normaUnidadCompetencia">
                    <i class="fas fa-file-alt"></i> Norma Unidad Competencia
                    <?php if (empty($competencia_actual['normaUnidadCompetencia'])): ?>
                        <span class="campo-requerido">* FALTA</span>
                    <?php endif; ?>
                </label>
                <textarea id="normaUnidadCompetencia" name="normaUnidadCompetencia" class="form-control" rows="5" 
                          placeholder="Describe la norma de unidad de competencia..."><?php echo htmlspecialchars($competencia_actual['normaUnidadCompetencia'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Requisitos del instructor -->
        <div class="form-section">
            <h3><i class="fas fa-user-graduate"></i> Requisitos del Instructor</h3>
            
            <div class="form-group <?php echo empty($competencia_actual['requisitosAcademicosInstructor']) ? 'campo-faltante' : ''; ?>">
                <label for="requisitosAcademicosInstructor">
                    <i class="fas fa-graduation-cap"></i> Requisitos Académicos del Instructor
                    <?php if (empty($competencia_actual['requisitosAcademicosInstructor'])): ?>
                        <span class="campo-requerido">* FALTA</span>
                    <?php endif; ?>
                </label>
                <textarea id="requisitosAcademicosInstructor" name="requisitosAcademicosInstructor" 
                          class="form-control" rows="4" 
                          placeholder="Describe los requisitos académicos del instructor..."><?php echo htmlspecialchars($competencia_actual['requisitosAcademicosInstructor'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group <?php echo empty($competencia_actual['experienciaLaboralInstructor']) ? 'campo-faltante' : ''; ?>">
                <label for="experienciaLaboralInstructor">
                    <i class="fas fa-briefcase"></i> Experiencia Laboral del Instructor
                    <?php if (empty($competencia_actual['experienciaLaboralInstructor'])): ?>
                        <span class="campo-requerido">* FALTA</span>
                    <?php endif; ?>
                </label>
                <textarea id="experienciaLaboralInstructor" name="experienciaLaboralInstructor" 
                          class="form-control" rows="4" 
                          placeholder="Describe la experiencia laboral requerida del instructor..."><?php echo htmlspecialchars($competencia_actual['experienciaLaboralInstructor'] ?? ''); ?></textarea>
            </div>
        </div>

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

.alert-info {
    border-left: 4px solid #3498db;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCompletarCompetencia');
    
    form.addEventListener('submit', function(e) {
        const nombreCompetencia = document.getElementById('nombreCompetencia').value.trim();
        const horas = parseFloat(document.getElementById('horasDesarrolloCompetencia').value) || 0;
        
        if (!nombreCompetencia) {
            e.preventDefault();
            alert('Por favor, ingrese el nombre de la competencia.');
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
</script>
