<div class="card-header">
    <h2 class="card-title"><i class="fas fa-edit"></i> Editar RAP</h2>
    <p class="card-subtitle">Modificar Resultado de Aprendizaje</p>
</div>

<?php if (!$rap_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró el RAP especificado.
    </div>
    <a href="?accion=listar" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver al listado
    </a>
<?php else: ?>

<form method="POST" id="formEditarRap">
    <input type="hidden" name="codigoDiseñoCompetenciaRap" value="<?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaRap'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Código del RAP:</strong> <?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaRap'] ?? ''); ?>
        (No se puede modificar el código)
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-hashtag"></i> Código Técnico del RAP</label>
            <input type="text" class="form-control" readonly 
                   value="<?php echo htmlspecialchars($rap_actual['codigoDiseñoCompetenciaRap'] ?? ''); ?>"
                   style="background: #e9ecef; font-family: monospace;">
            <small class="text-muted">Este código técnico no se puede modificar</small>
        </div>
        <div class="form-group">
            <label for="horasDesarrolloRap"><i class="fas fa-clock"></i> Horas de Desarrollo *</label>
            <input type="number" id="horasDesarrolloRap" name="horasDesarrolloRap" class="form-control" required 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($rap_actual['horasDesarrolloRap'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="codigoRapDiseño"><i class="fas fa-tag"></i> Código del RAP (Diseño) *</label>
            <input type="text" id="codigoRapDiseño" name="codigoRapDiseño" class="form-control" required 
                   value="<?php echo htmlspecialchars($rap_actual['codigoRapDiseño'] ?? ''); ?>"
                   placeholder="Ejemplo: RA1" maxlength="20">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Este es el código que aparece en el diseño curricular
            </small>
        </div>
        <div class="form-group">
            <label><i class="fas fa-key"></i> ID Automático</label>
            <input type="text" class="form-control" readonly 
                   value="<?php echo htmlspecialchars($rap_actual['codigoRapAutomatico'] ?? ''); ?>"
                   style="background: #e9ecef; text-align: center; font-weight: bold;">
            <small class="text-muted">ID generado automáticamente</small>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="nombreRap"><i class="fas fa-bullseye"></i> Resultado de Aprendizaje *</label>
            <textarea id="nombreRap" name="nombreRap" class="form-control" rows="6" required 
                      placeholder="Describe detalladamente el resultado de aprendizaje que el estudiante debe lograr..."><?php echo htmlspecialchars($rap_actual['nombreRap'] ?? ''); ?></textarea>
            <small class="text-muted">
                Describe claramente qué debe saber hacer el estudiante al finalizar este resultado de aprendizaje.
            </small>
        </div>
    </div>

    <div class="flex-between" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e9ecef;">
        <?php 
        // Extraer el código de la competencia del RAP
        $partes = explode('-', $rap_actual['codigoDiseñoCompetenciaRap']);
        $codigoCompetencia = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
        ?>
        <a href="?accion=ver_raps&codigo=<?php echo urlencode($codigoCompetencia); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Actualizar RAP
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario
    document.getElementById('formEditarRap').addEventListener('submit', function(e) {
        const nombreRap = document.getElementById('nombreRap').value.trim();
        const codigoRapDiseño = document.getElementById('codigoRapDiseño').value.trim();
        const horas = parseFloat(document.getElementById('horasDesarrolloRap').value) || 0;
        
        if (!nombreRap || !codigoRapDiseño) {
            e.preventDefault();
            alert('Por favor, completa el resultado de aprendizaje y el código del RAP.');
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
        
        if (!confirm('¿Estás seguro de actualizar este RAP?')) {
            e.preventDefault();
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
</script>

<?php endif; ?>
