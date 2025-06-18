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
        <br><strong>Código:</strong> <?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaRap']); ?>
    </div>

    <form method="POST" id="formCompletarRap">
        <input type="hidden" name="codigoDiseñoCompetenciaRap" value="<?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaRap'] ?? ''); ?>">
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
</script>
