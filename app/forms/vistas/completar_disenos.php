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

    <form method="POST" id="formCompletarDiseño">
        <input type="hidden" name="codigoDiseño" value="<?php echo htmlspecialchars($diseño_actual['codigoDiseño'] ?? ''); ?>">
        <input type="hidden" name="completar_modo" value="1">
        
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
        ?>
        
        <?php if ($faltaDesarrollo): ?>
        <div class="form-section campo-faltante">
            <h3><i class="fas fa-clock"></i> Información de Desarrollo <span class="campo-requerido">* FALTA</span></h3>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Complete uno de los dos grupos:</strong> Horas de desarrollo OR Meses de desarrollo
            </div>
            
            <div class="desarrollo-opciones">
                <div class="opcion-desarrollo">
                    <h4>Opción 1: Por Horas</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="horasDesarrolloLectiva"><i class="fas fa-clock"></i> Horas Lectivas</label>
                            <input type="number" id="horasDesarrolloLectiva" name="horasDesarrolloLectiva" 
                                   class="form-control" min="0" step="0.01" 
                                   value="<?php echo $horasLectiva > 0 ? $horasLectiva : ''; ?>"
                                   placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label for="horasDesarrolloProductiva"><i class="fas fa-industry"></i> Horas Productivas</label>
                            <input type="number" id="horasDesarrolloProductiva" name="horasDesarrolloProductiva" 
                                   class="form-control" min="0" step="0.01" 
                                   value="<?php echo $horasProductiva > 0 ? $horasProductiva : ''; ?>"
                                   placeholder="0.00">
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
                            <input type="number" id="mesesDesarrolloLectiva" name="mesesDesarrolloLectiva" 
                                   class="form-control" min="0" step="0.01" 
                                   value="<?php echo $mesesLectiva > 0 ? $mesesLectiva : ''; ?>"
                                   placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label for="mesesDesarrolloProductiva"><i class="fas fa-calendar-alt"></i> Meses Productivos</label>
                            <input type="number" id="mesesDesarrolloProductiva" name="mesesDesarrolloProductiva" 
                                   class="form-control" min="0" step="0.01" 
                                   value="<?php echo $mesesProductiva > 0 ? $mesesProductiva : ''; ?>"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCompletarDiseño');
    
    form.addEventListener('submit', function(e) {
        // Validar que se haya completado al menos la información de desarrollo
        const horasLectiva = parseFloat(document.getElementById('horasDesarrolloLectiva').value) || 0;
        const horasProductiva = parseFloat(document.getElementById('horasDesarrolloProductiva').value) || 0;
        const mesesLectiva = parseFloat(document.getElementById('mesesDesarrolloLectiva').value) || 0;
        const mesesProductiva = parseFloat(document.getElementById('mesesDesarrolloProductiva').value) || 0;
        
        const tieneHoras = horasLectiva > 0 && horasProductiva > 0;
        const tieneMeses = mesesLectiva > 0 && mesesProductiva > 0;
        
        <?php if ($faltaDesarrollo): ?>
        if (!tieneHoras && !tieneMeses) {
            e.preventDefault();
            alert('Por favor, complete la información de desarrollo:\n- Horas (Lectivas + Productivas) O\n- Meses (Lectivos + Productivos)');
            return;
        }
        <?php endif; ?>
        
        if (!confirm('¿Está seguro de completar esta información?')) {
            e.preventDefault();
        }
    });
});
</script>
