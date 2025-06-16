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
            <select id="lineaTecnologica" name="lineaTecnologica" class="form-control" required>
                <option value="">Seleccionar línea tecnológica</option>
                <option value="Tecnologías de la Información y las Comunicaciones" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Tecnologías de la Información y las Comunicaciones' ? 'selected' : ''; ?>>Tecnologías de la Información y las Comunicaciones</option>
                <option value="Producción Industrial" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Producción Industrial' ? 'selected' : ''; ?>>Producción Industrial</option>
                <option value="Diseño" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Diseño' ? 'selected' : ''; ?>>Diseño</option>
                <option value="Logística y Transporte" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Logística y Transporte' ? 'selected' : ''; ?>>Logística y Transporte</option>
                <option value="Gestión de la Información" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Gestión de la Información' ? 'selected' : ''; ?>>Gestión de la Información</option>
                <option value="Cliente" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Cliente' ? 'selected' : ''; ?>>Cliente</option>
                <option value="Materiales y Herramientas" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Materiales y Herramientas' ? 'selected' : ''; ?>>Materiales y Herramientas</option>
                <option value="Tecnologías del Ambiente, la Salud y la Seguridad" <?php echo ($diseño_actual['lineaTecnologica'] ?? '') === 'Tecnologías del Ambiente, la Salud y la Seguridad' ? 'selected' : ''; ?>>Tecnologías del Ambiente, la Salud y la Seguridad</option>
            </select>
        </div>
        <div class="form-group">
            <label for="redTecnologica"><i class="fas fa-network-wired"></i> Red Tecnológica *</label>
            <input type="text" id="redTecnologica" name="redTecnologica" class="form-control" required 
                   value="<?php echo htmlspecialchars($diseño_actual['redTecnologica'] ?? ''); ?>"
                   placeholder="Ejemplo: Tecnologías de la Información y las Comunicaciones">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="redConocimiento"><i class="fas fa-brain"></i> Red de Conocimiento *</label>
            <input type="text" id="redConocimiento" name="redConocimiento" class="form-control" required 
                   value="<?php echo htmlspecialchars($diseño_actual['redConocimiento'] ?? ''); ?>"
                   placeholder="Ejemplo: Red de Conocimiento en Tecnologías de la Información">
        </div>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
        <i class="fas fa-clock"></i> Duración del Programa
    </h3>
    
    <div class="alert alert-info" style="margin-bottom: 1rem;">
        <i class="fas fa-info-circle"></i>
        <strong>Información:</strong> Puede llenar las horas O los meses de desarrollo, no es necesario completar ambos campos.
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="horasDesarrolloLectiva"><i class="fas fa-book"></i> Horas Etapa Lectiva</label>
            <input type="number" id="horasDesarrolloLectiva" name="horasDesarrolloLectiva" class="form-control" 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($diseño_actual['horasDesarrolloLectiva'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="horasDesarrolloProductiva"><i class="fas fa-industry"></i> Horas Etapa Productiva</label>
            <input type="number" id="horasDesarrolloProductiva" name="horasDesarrolloProductiva" class="form-control" 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($diseño_actual['horasDesarrolloProductiva'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="mesesDesarrolloLectiva"><i class="fas fa-calendar-alt"></i> Meses Etapa Lectiva</label>
            <input type="number" id="mesesDesarrolloLectiva" name="mesesDesarrolloLectiva" class="form-control" 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($diseño_actual['mesesDesarrolloLectiva'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="mesesDesarrolloProductiva"><i class="fas fa-calendar-check"></i> Meses Etapa Productiva</label>
            <input type="number" id="mesesDesarrolloProductiva" name="mesesDesarrolloProductiva" class="form-control" 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($diseño_actual['mesesDesarrolloProductiva'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-row" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
        <div class="form-group">
            <label><i class="fas fa-calculator"></i> Total Horas (Calculado automáticamente)</label>
            <input type="text" id="totalHoras" class="form-control" readonly 
                   value="<?php echo number_format($diseño_actual['horasDesarrolloDiseño'] ?? 0, 2); ?>"
                   style="background: #e9ecef; font-weight: bold;">
        </div>
        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Total Meses (Calculado automáticamente)</label>
            <input type="text" id="totalMeses" class="form-control" readonly 
                   value="<?php echo number_format($diseño_actual['mesesDesarrolloDiseño'] ?? 0, 2); ?>"
                   style="background: #e9ecef; font-weight: bold;">
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
                <option value="Técnico Profesional" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Técnico Profesional' ? 'selected' : ''; ?>>Técnico Profesional</option>
                <option value="Tecnológico" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Tecnológico' ? 'selected' : ''; ?>>Tecnológico</option>
                <option value="Universitario" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Universitario' ? 'selected' : ''; ?>>Universitario</option>
                <option value="Especialización" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Especialización' ? 'selected' : ''; ?>>Especialización</option>
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
    // Calcular totales automáticamente
    function calcularTotales() {
        const horasLectivas = parseFloat(document.getElementById('horasDesarrolloLectiva').value) || 0;
        const horasProductivas = parseFloat(document.getElementById('horasDesarrolloProductiva').value) || 0;
        const mesesLectivos = parseFloat(document.getElementById('mesesDesarrolloLectiva').value) || 0;
        const mesesProductivos = parseFloat(document.getElementById('mesesDesarrolloProductiva').value) || 0;
        
        const totalHoras = horasLectivas + horasProductivas;
        const totalMeses = mesesLectivos + mesesProductivos;
        
        document.getElementById('totalHoras').value = totalHoras.toFixed(2);
        document.getElementById('totalMeses').value = totalMeses.toFixed(2);
    }
    
    // Agregar eventos para calcular totales
    ['horasDesarrolloLectiva', 'horasDesarrolloProductiva', 'mesesDesarrolloLectiva', 'mesesDesarrolloProductiva'].forEach(id => {
        document.getElementById(id).addEventListener('input', calcularTotales);
    });
    
    // Validación de formulario
    document.getElementById('formEditarDiseño').addEventListener('submit', function(e) {
        const totalHoras = parseFloat(document.getElementById('totalHoras').value) || 0;
        if (totalHoras <= 0) {
            e.preventDefault();
            alert('El total de horas debe ser mayor a cero.');
            return;
        }
        
        if (!confirm('¿Estás seguro de actualizar este diseño curricular?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php endif; ?>
