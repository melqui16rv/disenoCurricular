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
    <input type="hidden" name="codigoDiseñoCompetencia" value="<?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia'] ?? ''); ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Código de la Competencia:</strong> <?php echo htmlspecialchars($competencia_actual['codigoDiseñoCompetencia'] ?? ''); ?>
        (No se puede modificar el código)
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
            // Extraer código del diseño de la competencia
            $partes = explode('-', $competencia_actual['codigoDiseñoCompetencia']);
            $codigoDiseño = $partes[0] . '-' . $partes[1];
            
            if (isset($diseño_actual) && $diseño_actual): 
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
                   value="<?php echo htmlspecialchars($competencia_actual['codigoCompetencia'] ?? ''); ?>"
                   placeholder="Ejemplo: 220201501" maxlength="50">
            <small class="text-muted" id="codigoPreview">
                Código completo: <?php 
                $partes = explode('-', $competencia_actual['codigoDiseñoCompetencia']);
                $codigoDiseño = $partes[0] . '-' . $partes[1];
                echo $codigoDiseño . '-' . htmlspecialchars($competencia_actual['codigoCompetencia'] ?? '');
                ?>
            </small>
            <div id="codigoValidacion" class="mt-1"></div>
        </div>
        <div class="form-group">
            <label for="horasDesarrolloCompetencia"><i class="fas fa-clock"></i> Horas de Desarrollo *</label>
            <input type="number" id="horasDesarrolloCompetencia" name="horasDesarrolloCompetencia" class="form-control" required 
                   min="0" step="0.01" value="<?php echo htmlspecialchars($competencia_actual['horasDesarrolloCompetencia'] ?? ''); ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="nombreCompetencia"><i class="fas fa-star"></i> Nombre de la Competencia *</label>
            <input type="text" id="nombreCompetencia" name="nombreCompetencia" class="form-control" required 
                   value="<?php echo htmlspecialchars($competencia_actual['nombreCompetencia'] ?? ''); ?>"
                   placeholder="Ejemplo: Desarrollar el sistema que cumpla con los requisitos de la solución informática">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="normaUnidadCompetencia"><i class="fas fa-file-alt"></i> Norma de Unidad de Competencia</label>
            <textarea id="normaUnidadCompetencia" name="normaUnidadCompetencia" class="form-control" rows="4" 
                      placeholder="Descripción de la norma de unidad de competencia..."><?php echo htmlspecialchars($competencia_actual['normaUnidadCompetencia'] ?? ''); ?></textarea>
        </div>
    </div>

    <h3 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
        <i class="fas fa-user-tie"></i> Requisitos del Instructor
    </h3>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="requisitosAcademicosInstructor"><i class="fas fa-graduation-cap"></i> Requisitos Académicos del Instructor</label>
            <textarea id="requisitosAcademicosInstructor" name="requisitosAcademicosInstructor" class="form-control" rows="4" 
                      placeholder="Ejemplo: Profesional en Ingeniería de Sistemas, Ingeniería de Software, Tecnología en Desarrollo de Software o áreas afines..."><?php echo htmlspecialchars($competencia_actual['requisitosAcademicosInstructor'] ?? ''); ?></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label for="experienciaLaboralInstructor"><i class="fas fa-briefcase"></i> Experiencia Laboral del Instructor</label>
            <textarea id="experienciaLaboralInstructor" name="experienciaLaboralInstructor" class="form-control" rows="4" 
                      placeholder="Ejemplo: Veinticuatro (24) meses de experiencia: de los cuales dieciocho (18) meses estarán relacionados con el ejercicio de la profesión u ocupación objeto de la formación profesional..."><?php echo htmlspecialchars($competencia_actual['experienciaLaboralInstructor'] ?? ''); ?></textarea>
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
        
        // Verificar si el código ha cambiado y está validado
        const codigoInput = document.getElementById('codigoCompetencia');
        const codigoValidacion = document.getElementById('codigoValidacion');
        
        if (codigoInput.dataset.validado !== 'true') {
            e.preventDefault();
            alert('Por favor, espera a que se valide el código de competencia.');
            return;
        }
        
        if (codigoValidacion.classList.contains('text-danger')) {
            e.preventDefault();
            alert('El código de competencia no es válido. Por favor, corrige los errores.');
            return;
        }
        
        if (!confirm('¿Estás seguro de actualizar esta competencia?')) {
            e.preventDefault();
        }
    });

    // Validación en tiempo real del código de competencia
    const codigoInput = document.getElementById('codigoCompetencia');
    const codigoPreview = document.getElementById('codigoPreview');
    const codigoValidacion = document.getElementById('codigoValidacion');
    const codigoOriginal = codigoInput.value; // Código original para comparar
    let validationTimeout;
    
    // Obtener el código del diseño desde el contexto PHP
    const codigoDiseño = '<?php 
        $partes = explode('-', $competencia_actual['codigoDiseñoCompetencia']);
        echo $partes[0] . '-' . $partes[1];
    ?>';
    
    codigoInput.addEventListener('input', function() {
        const nuevoCodigo = this.value.trim();
        
        // Actualizar preview inmediatamente
        codigoPreview.textContent = `Código completo: ${codigoDiseño}-${nuevoCodigo}`;
        
        // Limpiar timeout anterior
        clearTimeout(validationTimeout);
        
        // Limpiar validación anterior
        codigoValidacion.innerHTML = '';
        this.dataset.validado = 'false';
        
        if (nuevoCodigo === '') {
            codigoValidacion.innerHTML = '<small class="text-danger"><i class="fas fa-exclamation-triangle"></i> El código de competencia es requerido</small>';
            return;
        }
        
        // Mostrar indicador de validación
        codigoValidacion.innerHTML = '<small class="text-info"><i class="fas fa-spinner fa-spin"></i> Validando código...</small>';
        
        // Validar después de 500ms de inactividad
        validationTimeout = setTimeout(() => {
            validarCodigoCompetencia(nuevoCodigo, codigoOriginal);
        }, 500);
    });
    
    function validarCodigoCompetencia(nuevoCodigo, codigoOriginal) {
        fetch(`control/ajax.php?accion=validar_edicion_codigo_competencia&codigoDiseño=${encodeURIComponent(codigoDiseño)}&codigoCompetencia=${encodeURIComponent(nuevoCodigo)}&codigoCompetenciaOriginal=${encodeURIComponent(codigoOriginal)}`)
            .then(response => response.json())
            .then(data => {
                const codigoInput = document.getElementById('codigoCompetencia');
                const codigoValidacion = document.getElementById('codigoValidacion');
                
                if (data.success) {
                    codigoValidacion.innerHTML = `<small class="text-success"><i class="fas fa-check-circle"></i> ${data.message}</small>`;
                    codigoInput.dataset.validado = 'true';
                } else {
                    codigoValidacion.innerHTML = `<small class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${data.message}</small>`;
                    codigoInput.dataset.validado = 'false';
                }
            })
            .catch(error => {
                console.error('Error en validación:', error);
                const codigoValidacion = document.getElementById('codigoValidacion');
                codigoValidacion.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Error al validar código</small>';
                document.getElementById('codigoCompetencia').dataset.validado = 'false';
            });
    }
    
    // Validar código inicial si existe
    if (codigoInput.value.trim() !== '') {
        codigoInput.dataset.validado = 'true'; // El código actual es válido por defecto
    }
});

// Función para mostrar/ocultar información del diseño
function toggleDiseñoInfo() {
    const panel = document.getElementById('diseñoInfoPanel');
    const btn = document.getElementById('btnToggleDiseño');
    
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

<?php endif; ?>
