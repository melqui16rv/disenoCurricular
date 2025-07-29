<?php
// Vista especializada para completar información faltante en diseños
?>

<div class="card-header">
    <h2 class="card-title"><i class="fas fa-clipboard-check"></i> Completar Información del Diseño</h2>
    <p class="card-subtitle">Complete solo los campos faltantes marcados en rojo</p>
</div>

<?php if (!$diseño_actual): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        No se encontró el diseño curricular especificado.
    </div>
    <a href="?accion=completar_informacion" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver a Completar Información
    </a>
<?php else: ?>

    <!-- Información del diseño actual -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Completando información para:</strong> <?php echo htmlspecialchars($diseño_actual['nombrePrograma'] ?? 'Diseño'); ?>
        <br><strong>Código:</strong> <?php echo htmlspecialchars($diseño_actual['codigoDiseño']); ?>
    </div>

    <form method="POST" id="formCompletarDiseño" class="form-completar" novalidate data-skip-validation="true" data-no-validation="true">
        <input type="hidden" name="codigoDiseño" value="<?php echo htmlspecialchars($diseño_actual['codigoDiseño'] ?? ''); ?>">
        <input type="hidden" name="completar_modo" value="1">
        <input type="hidden" name="accion" value="completar">
        <input type="hidden" name="tipo" value="disenos">
        
        <!-- Campos de tecnología y redes -->
        <div class="form-section">
            <h3><i class="fas fa-network-wired"></i> Información Tecnológica</h3>
            
            <div class="form-row">
                <div class="form-group <?php echo empty($diseño_actual['lineaTecnologica']) ? 'campo-faltante' : ''; ?>">
                    <label for="lineaTecnologica">
                        <i class="fas fa-sitemap"></i> Línea Tecnológica
                        <?php if (empty($diseño_actual['lineaTecnologica'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="lineaTecnologica" name="lineaTecnologica" class="form-control" 
                           value="<?php echo htmlspecialchars($diseño_actual['lineaTecnologica'] ?? ''); ?>"
                           placeholder="Ingrese la línea tecnológica">
                </div>
                
                <div class="form-group <?php echo empty($diseño_actual['redTecnologica']) ? 'campo-faltante' : ''; ?>">
                    <label for="redTecnologica">
                        <i class="fas fa-network-wired"></i> Red Tecnológica
                        <?php if (empty($diseño_actual['redTecnologica'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="redTecnologica" name="redTecnologica" class="form-control" 
                           value="<?php echo htmlspecialchars($diseño_actual['redTecnologica'] ?? ''); ?>"
                           placeholder="Ingrese la red tecnológica">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group <?php echo empty($diseño_actual['redConocimiento']) ? 'campo-faltante' : ''; ?>">
                    <label for="redConocimiento">
                        <i class="fas fa-brain"></i> Red de Conocimiento
                        <?php if (empty($diseño_actual['redConocimiento'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="redConocimiento" name="redConocimiento" class="form-control" 
                           value="<?php echo htmlspecialchars($diseño_actual['redConocimiento'] ?? ''); ?>"
                           placeholder="Ingrese la red de conocimiento">
                </div>
            </div>
        </div>

        <!-- Campos de horas y meses -->
        <?php 
        $horasLectiva = (float)($diseño_actual['horasDesarrolloLectiva'] ?? 0);
        $horasProductiva = (float)($diseño_actual['horasDesarrolloProductiva'] ?? 0);
        $mesesLectiva = (float)($diseño_actual['mesesDesarrolloLectiva'] ?? 0);
        $mesesProductiva = (float)($diseño_actual['mesesDesarrolloProductiva'] ?? 0);
        
        $tieneHorasCompletas = ($horasLectiva > 0 && $horasProductiva > 0);
        $tieneMesesCompletos = ($mesesLectiva > 0 && $mesesProductiva > 0);
        $faltaDesarrollo = (!$tieneHorasCompletas && !$tieneMesesCompletos);
        
        // Debug temporal - remover después de verificar
        if (isset($_GET['debug'])) {
            echo "<div class='alert alert-info'>";
            echo "<strong>DEBUG:</strong><br>";
            echo "Horas Lectiva: " . $horasLectiva . "<br>";
            echo "Horas Productiva: " . $horasProductiva . "<br>";
            echo "Meses Lectiva: " . $mesesLectiva . "<br>";
            echo "Meses Productiva: " . $mesesProductiva . "<br>";
            echo "Tiene Horas Completas: " . ($tieneHorasCompletas ? 'SÍ' : 'NO') . "<br>";
            echo "Tiene Meses Completos: " . ($tieneMesesCompletos ? 'SÍ' : 'NO') . "<br>";
            echo "Falta Desarrollo: " . ($faltaDesarrollo ? 'SÍ' : 'NO') . "<br>";
            echo "</div>";
        }
        ?>
        
        <?php if ($faltaDesarrollo): ?>
        <div class="form-section campo-faltante">
            <h3><i class="fas fa-clock"></i> Información de Desarrollo <span class="campo-requerido">* FALTA</span></h3>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Complete uno de los dos grupos:</strong> Horas de desarrollo O Meses de desarrollo
            </div>
            
            <div class="desarrollo-opciones">
                <div class="opcion-desarrollo">
                    <h4>Opción 1: Por Horas</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="horasDesarrolloLectiva"><i class="fas fa-clock"></i> Horas Lectivas</label>
                            <input type="text" id="horasDesarrolloLectiva" name="horasDesarrolloLectiva" 
                                   class="form-control desarrollo-input" 
                                   value="<?php echo $horasLectiva > 0 ? $horasLectiva : ''; ?>"
                                   placeholder="Ej: 1200">
                        </div>
                        <div class="form-group">
                            <label for="horasDesarrolloProductiva"><i class="fas fa-industry"></i> Horas Productivas</label>
                            <input type="text" id="horasDesarrolloProductiva" name="horasDesarrolloProductiva" 
                                   class="form-control desarrollo-input" 
                                   value="<?php echo $horasProductiva > 0 ? $horasProductiva : ''; ?>"
                                   placeholder="Ej: 880">
                        </div>
                    </div>
                </div>
                
                <div class="separador-opciones">
                    <span>O</span>
                </div>
                
                <div class="opcion-desarrollo">
                    <h4>Opción 2: Por Meses</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="mesesDesarrolloLectiva"><i class="fas fa-calendar"></i> Meses Lectivos</label>
                            <input type="text" id="mesesDesarrolloLectiva" name="mesesDesarrolloLectiva" 
                                   class="form-control desarrollo-input" 
                                   value="<?php echo $mesesLectiva > 0 ? $mesesLectiva : ''; ?>"
                                   placeholder="Ej: 18">
                        </div>
                        <div class="form-group">
                            <label for="mesesDesarrolloProductiva"><i class="fas fa-calendar-alt"></i> Meses Productivos</label>
                            <input type="text" id="mesesDesarrolloProductiva" name="mesesDesarrolloProductiva" 
                                   class="form-control desarrollo-input" 
                                   value="<?php echo $mesesProductiva > 0 ? $mesesProductiva : ''; ?>"
                                   placeholder="Ej: 6">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Información de desarrollo ya completada -->
        <div class="form-section">
            <h3><i class="fas fa-check-circle text-success"></i> Información de Desarrollo</h3>
            <div class="alert alert-success">
                <i class="fas fa-info-circle"></i>
                <strong>Información completa:</strong> 
                <?php if ($tieneHorasCompletas): ?>
                    Horas: <?php echo $horasLectiva; ?> lectivas + <?php echo $horasProductiva; ?> productivas = <?php echo ($horasLectiva + $horasProductiva); ?> total
                    <!-- Mantener los valores existentes como campos ocultos -->
                    <input type="hidden" name="horasDesarrolloLectiva" value="<?php echo $horasLectiva; ?>">
                    <input type="hidden" name="horasDesarrolloProductiva" value="<?php echo $horasProductiva; ?>">
                <?php elseif ($tieneMesesCompletos): ?>
                    Meses: <?php echo $mesesLectiva; ?> lectivos + <?php echo $mesesProductiva; ?> productivos = <?php echo ($mesesLectiva + $mesesProductiva); ?> total
                    <!-- Mantener los valores existentes como campos ocultos -->
                    <input type="hidden" name="mesesDesarrolloLectiva" value="<?php echo $mesesLectiva; ?>">
                    <input type="hidden" name="mesesDesarrolloProductiva" value="<?php echo $mesesProductiva; ?>">
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Campos académicos -->
        <div class="form-section">
            <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
            
            <div class="form-row">
                <div class="form-group <?php echo empty($diseño_actual['nivelAcademicoIngreso']) ? 'campo-faltante' : ''; ?>">
                    <label for="nivelAcademicoIngreso">
                        <i class="fas fa-school"></i> Nivel Académico de Ingreso
                        <?php if (empty($diseño_actual['nivelAcademicoIngreso'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <select id="nivelAcademicoIngreso" name="nivelAcademicoIngreso" class="form-control">
                        <option value="">Seleccione...</option>
                        <option value="Primaria" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Primaria' ? 'selected' : ''; ?>>Primaria</option>
                        <option value="Bachillerato" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Bachillerato' ? 'selected' : ''; ?>>Bachillerato</option>
                        <option value="Técnico" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Técnico' ? 'selected' : ''; ?>>Técnico</option>
                        <option value="Tecnológico" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Tecnológico' ? 'selected' : ''; ?>>Tecnológico</option>
                        <option value="Profesional" <?php echo ($diseño_actual['nivelAcademicoIngreso'] ?? '') === 'Profesional' ? 'selected' : ''; ?>>Profesional</option>
                    </select>
                </div>
                
                <div class="form-group <?php echo (empty($diseño_actual['gradoNivelAcademico']) || $diseño_actual['gradoNivelAcademico'] == 0) ? 'campo-faltante' : ''; ?>">
                    <label for="gradoNivelAcademico">
                        <i class="fas fa-medal"></i> Grado del Nivel Académico
                        <?php if (empty($diseño_actual['gradoNivelAcademico']) || $diseño_actual['gradoNivelAcademico'] == 0): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="number" id="gradoNivelAcademico" name="gradoNivelAcademico" class="form-control" 
                           min="1" max="20" value="<?php echo htmlspecialchars($diseño_actual['gradoNivelAcademico'] ?? ''); ?>"
                           placeholder="Ej: 11, 9, etc.">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group <?php echo empty($diseño_actual['formacionTrabajoDesarrolloHumano']) ? 'campo-faltante' : ''; ?>">
                    <label for="formacionTrabajoDesarrolloHumano">
                        <i class="fas fa-users"></i> Formación en Trabajo y Desarrollo Humano
                        <?php if (empty($diseño_actual['formacionTrabajoDesarrolloHumano'])): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <select id="formacionTrabajoDesarrolloHumano" name="formacionTrabajoDesarrolloHumano" class="form-control">
                        <option value="">Seleccione...</option>
                        <option value="Si" <?php echo ($diseño_actual['formacionTrabajoDesarrolloHumano'] ?? '') === 'Si' ? 'selected' : ''; ?>>Sí</option>
                        <option value="No" <?php echo ($diseño_actual['formacionTrabajoDesarrolloHumano'] ?? '') === 'No' ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
                
                <div class="form-group <?php echo (empty($diseño_actual['edadMinima']) || $diseño_actual['edadMinima'] == 0) ? 'campo-faltante' : ''; ?>">
                    <label for="edadMinima">
                        <i class="fas fa-birthday-cake"></i> Edad Mínima
                        <?php if (empty($diseño_actual['edadMinima']) || $diseño_actual['edadMinima'] == 0): ?>
                            <span class="campo-requerido">* FALTA</span>
                        <?php endif; ?>
                    </label>
                    <input type="number" id="edadMinima" name="edadMinima" class="form-control" 
                           min="10" max="100" value="<?php echo htmlspecialchars($diseño_actual['edadMinima'] ?? ''); ?>"
                           placeholder="Ej: 16, 18, etc.">
                </div>
            </div>
        </div>

        <!-- Requisitos adicionales -->
        <div class="form-section">
            <div class="form-group <?php echo empty($diseño_actual['requisitosAdicionales']) ? 'campo-faltante' : ''; ?>">
                <label for="requisitosAdicionales">
                    <i class="fas fa-clipboard-list"></i> Requisitos Adicionales
                    <?php if (empty($diseño_actual['requisitosAdicionales'])): ?>
                        <span class="campo-requerido">* FALTA</span>
                    <?php endif; ?>
                </label>
                <textarea id="requisitosAdicionales" name="requisitosAdicionales" class="form-control" rows="4" 
                          placeholder="Describe cualquier requisito adicional para el ingreso al programa..."><?php echo htmlspecialchars($diseño_actual['requisitosAdicionales'] ?? ''); ?></textarea>
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

.desarrollo-opciones {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 2rem;
    align-items: center;
}

.opcion-desarrollo {
    padding: 1rem;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    background: #f8f9fa;
}

.opcion-desarrollo h4 {
    color: #3498db;
    margin-bottom: 1rem;
    text-align: center;
}

.separador-opciones {
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: #7f8c8d;
    background: white;
    border: 2px solid #bdc3c7;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    line-height: 36px;
}

.text-success {
    color: #27ae60 !important;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.desarrollo-input:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
    cursor: not-allowed;
}

.campo-deshabilitado {
    background-color: #f8f9fa !important;
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    border-color: #dee2e6 !important;
}

.form-group:has(.desarrollo-input:disabled),
.form-group:has(.campo-deshabilitado) {
    opacity: 0.6;
}

.form-group:has(.desarrollo-input:disabled) label,
.form-group:has(.campo-deshabilitado) label {
    color: #6c757d;
}

/* Eliminar cualquier indicador de validación visual */
.desarrollo-input:invalid,
.campo-deshabilitado:invalid {
    border-color: #ced4da !important;
    box-shadow: none !important;
}

.desarrollo-input:focus,
.campo-deshabilitado:focus {
    outline: none !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    border-color: #80bdff !important;
}

/* Ocultar tooltips de validación del navegador */
.desarrollo-input[data-no-validate="true"]:invalid {
    box-shadow: none !important;
}

[role="tooltip"],
.validation-message,
[data-original-title],
[class*="tooltip"],
[class*="popover"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

@media (max-width: 768px) {
    .desarrollo-opciones {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .separador-opciones {
        justify-self: center;
    }
}
</style>

<script>
// Interceptor GLOBAL para prevenir cualquier modal de validación en este formulario
(function() {
    // Interceptar ANTES de que el DOM se cargue
    const originalAlert = window.alert;
    window.alert = function(message) {
        // Si el mensaje contiene palabras relacionadas con validación, no mostrarlo
        if (message && (
            message.includes('válido') || 
            message.includes('required') || 
            message.includes('número') ||
            message.includes('campo') ||
            message.includes('debe ser')
        )) {
            console.log('Modal de validación interceptado y bloqueado:', message);
            return;
        }
        // Si no es un modal de validación, mostrarlo normalmente
        return originalAlert.call(this, message);
    };
})();

// Solución SIMPLE: Solo validar que se complete una opción, sin interferir con validación HTML5
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCompletarDiseño');
    if (!form) return;
    
    // Deshabilitar COMPLETAMENTE la validación HTML5
    form.noValidate = true;
    form.setAttribute('novalidate', 'novalidate');
    
    // Obtener elementos
    const horasLectivaInput = document.getElementById('horasDesarrolloLectiva');
    const horasProductivaInput = document.getElementById('horasDesarrolloProductiva');
    const mesesLectivaInput = document.getElementById('mesesDesarrolloLectiva');
    const mesesProductivaInput = document.getElementById('mesesDesarrolloProductiva');
    
    // Función simple para gestionar el estado visual
    function gestionarCampos() {
        if (!horasLectivaInput || !horasProductivaInput || !mesesLectivaInput || !mesesProductivaInput) {
            return;
        }
        
        const horasLectiva = horasLectivaInput.value.trim();
        const horasProductiva = horasProductivaInput.value.trim();
        const mesesLectiva = mesesLectivaInput.value.trim();
        const mesesProductiva = mesesProductivaInput.value.trim();
        
        const usandoHoras = horasLectiva !== '' || horasProductiva !== '';
        const usandoMeses = mesesLectiva !== '' || mesesProductiva !== '';
        
        // Gestionar estado visual
        if (usandoHoras && !usandoMeses) {
            // Si está usando horas, deshabilitar visualmente los meses
            mesesLectivaInput.disabled = true;
            mesesProductivaInput.disabled = true;
            mesesLectivaInput.classList.add('campo-deshabilitado');
            mesesProductivaInput.classList.add('campo-deshabilitado');
        } else if (usandoMeses && !usandoHoras) {
            // Si está usando meses, deshabilitar visualmente las horas
            horasLectivaInput.disabled = true;
            horasProductivaInput.disabled = true;
            horasLectivaInput.classList.add('campo-deshabilitado');
            horasProductivaInput.classList.add('campo-deshabilitado');
        } else {
            // Si no está usando ninguno o ambos están vacíos, habilitar todos
            [horasLectivaInput, horasProductivaInput, mesesLectivaInput, mesesProductivaInput].forEach(campo => {
                if (campo) {
                    campo.disabled = false;
                    campo.classList.remove('campo-deshabilitado');
                }
            });
        }
    }
    
    // Agregar listeners a los campos
    [horasLectivaInput, horasProductivaInput, mesesLectivaInput, mesesProductivaInput].forEach(campo => {
        if (campo) {
            campo.addEventListener('input', gestionarCampos);
            campo.addEventListener('change', gestionarCampos);
            
            // Interceptar eventos de validación específicamente en cada campo
            campo.addEventListener('invalid', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
        }
    });
    
    // Ejecutar gestión inicial
    gestionarCampos();
    
    // Interceptar eventos de validación del formulario
    form.addEventListener('invalid', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        return false;
    }, true);
    
    // Manejar el envío del formulario con validación SIMPLE
    form.addEventListener('submit', function(e) {
        <?php if ($faltaDesarrollo): ?>
        // Solo validar si falta información de desarrollo
        
        // Habilitar todos los campos temporalmente para obtener sus valores
        [horasLectivaInput, horasProductivaInput, mesesLectivaInput, mesesProductivaInput].forEach(campo => {
            if (campo) campo.disabled = false;
        });
        
        const horasLectiva = horasLectivaInput ? horasLectivaInput.value.trim() : '';
        const horasProductiva = horasProductivaInput ? horasProductivaInput.value.trim() : '';
        const mesesLectiva = mesesLectivaInput ? mesesLectivaInput.value.trim() : '';
        const mesesProductiva = mesesProductivaInput ? mesesProductivaInput.value.trim() : '';
        
        // Verificar que se haya completado al menos una opción
        const tieneHoras = horasLectiva !== '' && horasProductiva !== '';
        const tieneMeses = mesesLectiva !== '' && mesesProductiva !== '';
        
        if (!tieneHoras && !tieneMeses) {
            e.preventDefault();
            
            // Usar setTimeout para evitar que otros scripts interfieran
            setTimeout(function() {
                alert('Por favor, complete al menos una opción:\n\n• Ambos campos de HORAS (Lectivas + Productivas)\n• O ambos campos de MESES (Lectivos + Productivos)');
            }, 10);
            
            // Restaurar estado visual
            setTimeout(gestionarCampos, 50);
            return false;
        }
        
        // Limpiar los campos de la opción que NO se está usando
        if (tieneHoras && !tieneMeses) {
            // Usando horas, limpiar meses
            if (mesesLectivaInput) mesesLectivaInput.value = '';
            if (mesesProductivaInput) mesesProductivaInput.value = '';
        } else if (tieneMeses && !tieneHoras) {
            // Usando meses, limpiar horas
            if (horasLectivaInput) horasLectivaInput.value = '';
            if (horasProductivaInput) horasProductivaInput.value = '';
        }
        <?php endif; ?>
        
        // Confirmación antes de enviar
        if (!confirm('¿Está seguro de guardar esta información?')) {
            e.preventDefault();
            setTimeout(gestionarCampos, 50);
            return false;
        }
        
        // Deshabilitar el botón para evitar envíos duplicados
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        }
    });
});
</script>
