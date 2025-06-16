<div class="card-header">
    <h2 class="card-title"><i class="fas fa-plus-circle"></i> Crear Nuevo RAP</h2>
    <p class="card-subtitle">Agregar Resultado de Aprendizaje a la competencia</p>
</div>

<form method="POST" id="formRap">
    <input type="hidden" name="codigoDiseñoCompetencia" value="<?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Competencia:</strong> <?php echo htmlspecialchars($_GET['codigoDiseñoCompetencia'] ?? ''); ?>
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
</script>
