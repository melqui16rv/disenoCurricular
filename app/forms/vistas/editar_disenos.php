<div class="card-header">
    <h2 class="card-title"><i class="fas fa-edit"></i> Editar Diseño Curricular</h2>
    <p class="card-subtitle">Modificar información del programa formativo</p>
</div>

<?php if (!$diseño_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró el diseño curricular especificado.
    </div>
    <a href="?accion=listar" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver al listado
    </a>
<?php else: ?>

<form method="POST" id="formEditarDiseño">
    <input type="hidden" name="codigoDiseño" value="<?php echo htmlspecialchars($diseño_actual['codigoDiseño'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Código del Diseño:</strong> <?php echo htmlspecialchars($diseño_actual['codigoDiseño'] ?? ''); ?>
        (No se puede modificar el código del programa ni la versión)
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-hashtag"></i> Código del Programa</label>
            <input type="text" class="form-control" readonly 
                   value="<?php echo htmlspecialchars($diseño_actual['codigoPrograma'] ?? ''); ?>"
                   style="background: #e9ecef;">
        </div>
        <div class="form-group">
            <label><i class="fas fa-code-branch"></i> Versión del Programa</label>
            <input type="text" class="form-control" readonly 
                   value="<?php echo htmlspecialchars($diseño_actual['versionPrograma'] ?? ''); ?>"
                   style="background: #e9ecef;">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="nombrePrograma"><i class="fas fa-graduation-cap"></i> Nombre del Programa *</label>
            <input type="text" id="nombrePrograma" name="nombrePrograma" class="form-control" required 
                   value="<?php echo htmlspecialchars($diseño_actual['nombrePrograma'] ?? ''); ?>"
                   placeholder="Ejemplo: Tecnólogo en Desarrollo de Software">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="lineaTecnologica"><i class="fas fa-sitemap"></i> Línea Tecnológica *</label>
            <input type="text" id="lineaTecnologica" name="lineaTecnologica" class="form-control" required 
                   value="<?php echo htmlspecialchars($diseño_actual['lineaTecnologica'] ?? ''); ?>"
                   placeholder="Ejemplo: Tecnologías de la Información y las Comunicaciones">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Campo abierto - Escriba la línea tecnológica correspondiente
            </small>
        </div>
        <div class="form-group">
            <label for="redTecnologica"><i class="fas fa-network-wired"></i> Red Tecnológica *</label>
            <input type="text" id="redTecnologica" name="redTecnologica" class="form-control" required 
                   value="<?php echo htmlspecialchars($diseño_actual['redTecnologica'] ?? ''); ?>"
                   placeholder="Ejemplo: Tecnologías de la Información y las Comunicaciones">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Campo abierto - Escriba la red tecnológica correspondiente
            </small>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="redConocimiento"><i class="fas fa-brain"></i> Red de Conocimiento *</label>
            <input type="text" id="redConocimiento" name="redConocimiento" class="form-control" required 
                   value="<?php echo htmlspecialchars($diseño_actual['redConocimiento'] ?? ''); ?>"
                   placeholder="Ejemplo: Red de Conocimiento en Tecnologías de la Información">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Campo abierto - Escriba la red de conocimiento correspondiente
            </small>
        </div>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
        <i class="fas fa-clock"></i> Duración del Programa
    </h3>
    
    <div class="alert alert-info" style="margin-bottom: 1rem;">
        <i class="fas fa-info-circle"></i>
        <strong>Información:</strong> Los campos de horas y meses pueden dejarse vacíos si no aplican al programa. Permite cualquier valor numérico decimal.
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="horasDesarrolloLectiva"><i class="fas fa-book"></i> Horas Etapa Lectiva</label>
            <input type="number" id="horasDesarrolloLectiva" name="horasDesarrolloLectiva" class="form-control" 
                   step="0.01" value="<?php echo htmlspecialchars($diseño_actual['horasDesarrolloLectiva'] ?? ''); ?>"
                   placeholder="Ejemplo: 1440.50 (puede dejarse vacío)">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Permite decimales. Puede dejarse vacío si no aplica.
            </small>
        </div>
        <div class="form-group">
            <label for="horasDesarrolloProductiva"><i class="fas fa-industry"></i> Horas Etapa Productiva</label>
            <input type="number" id="horasDesarrolloProductiva" name="horasDesarrolloProductiva" class="form-control" 
                   step="0.01" value="<?php echo htmlspecialchars($diseño_actual['horasDesarrolloProductiva'] ?? ''); ?>"
                   placeholder="Ejemplo: 880.75 (puede dejarse vacío)">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Permite decimales. Puede dejarse vacío si no aplica.
            </small>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="mesesDesarrolloLectiva"><i class="fas fa-calendar-alt"></i> Meses Etapa Lectiva</label>
            <input type="number" id="mesesDesarrolloLectiva" name="mesesDesarrolloLectiva" class="form-control" 
                   step="0.01" value="<?php echo htmlspecialchars($diseño_actual['mesesDesarrolloLectiva'] ?? ''); ?>"
                   placeholder="Ejemplo: 18.5 (puede dejarse vacío)">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Permite decimales. Puede dejarse vacío si no aplica.
            </small>
        </div>
        <div class="form-group">
            <label for="mesesDesarrolloProductiva"><i class="fas fa-calendar-check"></i> Meses Etapa Productiva</label>
            <input type="number" id="mesesDesarrolloProductiva" name="mesesDesarrolloProductiva" class="form-control" 
                   step="0.01" value="<?php echo htmlspecialchars($diseño_actual['mesesDesarrolloProductiva'] ?? ''); ?>"
                   placeholder="Ejemplo: 6.0 (puede dejarse vacío)">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Permite decimales. Puede dejarse vacío si no aplica.
            </small>
        </div>
    </div>

    <div class="form-row" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #28a745;">
        <div class="form-group">
            <label><i class="fas fa-calculator"></i> Total Horas de Desarrollo</label>
            <input type="text" id="totalHoras" name="horasDesarrolloDiseño" class="form-control" readonly 
                   value="<?php echo number_format($diseño_actual['horasDesarrolloDiseño'] ?? 0, 2); ?>"
                   style="background: #e9ecef; font-weight: bold; color: #28a745;">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Se calcula automáticamente: Horas Lectiva + Horas Productiva
            </small>
        </div>
        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Total Meses de Desarrollo</label>
            <input type="text" id="totalMeses" name="mesesDesarrolloDiseño" class="form-control" readonly 
                   value="<?php echo number_format($diseño_actual['mesesDesarrolloDiseño'] ?? 0, 2); ?>"
                   style="background: #e9ecef; font-weight: bold; color: #28a745;">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Se calcula automáticamente: Meses Lectiva + Meses Productiva
            </small>
        </div>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
        <i class="fas fa-user-graduate"></i> Requisitos de Ingreso
    </h3>

    <div class="form-row">
        <div class="form-group">
            <label for="nivelAcademicoIngreso"><i class="fas fa-school"></i> Nivel Académico de Ingreso *</label>
            <select id="nivelAcademicoIngreso" name="nivelAcademicoIngreso" class="form-control" required>
                <option value="">Seleccionar nivel académico</option>
                <option value="Primaria" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Primaria' ? 'selected' : ''; ?>>Primaria</option>
                <option value="Básica Secundaria" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Básica Secundaria' ? 'selected' : ''; ?>>Básica Secundaria</option>
                <option value="Media Académica" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Media Académica' ? 'selected' : ''; ?>>Media Académica</option>
                <option value="Media Técnica" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Media Técnica' ? 'selected' : ''; ?>>Media Técnica</option>
            </select>
        </div>
        <div class="form-group">
            <label for="gradoNivelAcademico"><i class="fas fa-medal"></i> Grado del Nivel Académico *</label>
            <input type="number" id="gradoNivelAcademico" name="gradoNivelAcademico" class="form-control" required 
                   min="1" max="15" value="<?php echo htmlspecialchars($diseño_actual['gradoNivelAcademico'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="formacionTrabajoDesarrolloHumano"><i class="fas fa-users"></i> Formación en Trabajo y Desarrollo Humano *</label>
            <select id="formacionTrabajoDesarrolloHumano" name="formacionTrabajoDesarrolloHumano" class="form-control" required>
                <option value="">Seleccionar opción</option>
                <option value="Si" <?php echo ($diseño_actual['formacionTrabajoDesarrolloHumano'] ?? '') === 'Si' ? 'selected' : ''; ?>>Sí</option>
                <option value="No" <?php echo ($diseño_actual['formacionTrabajoDesarrolloHumano'] ?? '') === 'No' ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        <div class="form-group">
            <label for="edadMinima"><i class="fas fa-birthday-cake"></i> Edad Mínima *</label>
            <input type="number" id="edadMinima" name="edadMinima" class="form-control" required 
                   min="14" max="80" value="<?php echo htmlspecialchars($diseño_actual['edadMinima'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="requisitosAdicionales"><i class="fas fa-clipboard-list"></i> Requisitos Adicionales</label>
            <textarea id="requisitosAdicionales" name="requisitosAdicionales" class="form-control" rows="4" 
                      placeholder="Describe cualquier requisito adicional para el ingreso al programa..."><?php echo htmlspecialchars($diseño_actual['requisitosAdicionales'] ?? ''); ?></textarea>
        </div>
    </div>

    <div class="flex-between" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e9ecef;">
        <a href="?accion=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Actualizar Diseño
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular totales automáticamente en tiempo real
    function calcularTotales() {
        const horasLectivasVal = document.getElementById('horasDesarrolloLectiva').value;
        const horasProductivasVal = document.getElementById('horasDesarrolloProductiva').value;
        const mesesLectivosVal = document.getElementById('mesesDesarrolloLectiva').value;
        const mesesProductivosVal = document.getElementById('mesesDesarrolloProductiva').value;
        
        // Convertir a números, permitiendo campos vacíos (null/0)
        const horasLectivas = horasLectivasVal === '' ? 0 : parseFloat(horasLectivasVal) || 0;
        const horasProductivas = horasProductivasVal === '' ? 0 : parseFloat(horasProductivasVal) || 0;
        const mesesLectivos = mesesLectivosVal === '' ? 0 : parseFloat(mesesLectivosVal) || 0;
        const mesesProductivos = mesesProductivosVal === '' ? 0 : parseFloat(mesesProductivosVal) || 0;
        
        // Calcular totales según especificación de BD: suma de lectiva + productiva
        const totalHoras = horasLectivas + horasProductivas;
        const totalMeses = mesesLectivos + mesesProductivos;
        
        // Actualizar campos calculados
        document.getElementById('totalHoras').value = totalHoras.toFixed(2);
        document.getElementById('totalMeses').value = totalMeses.toFixed(2);
        
        // Cambiar color según si hay valores
        const campoHoras = document.getElementById('totalHoras');
        const campoMeses = document.getElementById('totalMeses');
        
        campoHoras.style.color = totalHoras > 0 ? '#28a745' : '#6c757d';
        campoMeses.style.color = totalMeses > 0 ? '#28a745' : '#6c757d';
    }
    
    // Agregar eventos para calcular totales en tiempo real
    ['horasDesarrolloLectiva', 'horasDesarrolloProductiva', 'mesesDesarrolloLectiva', 'mesesDesarrolloProductiva'].forEach(id => {
        const campo = document.getElementById(id);
        campo.addEventListener('input', calcularTotales);
        campo.addEventListener('change', calcularTotales);
        campo.addEventListener('keyup', calcularTotales);
    });
    
    // Calcular al cargar la página
    calcularTotales();
    
    // Validación de formulario
    document.getElementById('formEditarDiseño').addEventListener('submit', function(e) {
        // Permitir que los campos de horas puedan estar vacíos o ser 0
        // No validar que el total sea mayor a cero
        
        if (!confirm('¿Estás seguro de actualizar este diseño curricular?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php endif; ?>
