// Scripts para el Sistema de Diseños Curriculares
document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar si hay una configuración global para saltar validación
    if (window.SKIP_GLOBAL_VALIDATION) {
        return;
    }
    
    // Confirmaciones de eliminación más elaboradas
    const botonesEliminar = document.querySelectorAll('a[href*="eliminar"]');
    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const tipo = href.includes('tipo=diseños') ? 'diseño' : 
                        href.includes('tipo=competencias') ? 'competencia' : 'RAP';
            
            let mensaje = `¿Estás seguro de eliminar este ${tipo}?`;
            
            if (tipo === 'diseño') {
                mensaje += '\n\nEsta acción eliminará también todas las competencias y RAPs asociados.';
            } else if (tipo === 'competencia') {
                mensaje += '\n\nEsta acción eliminará también todos los RAPs asociados.';
            }
            
            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
    
    // Validaciones mejoradas para formularios
    const formularios = document.querySelectorAll('form');
    formularios.forEach(form => {
        form.addEventListener('submit', function(e) {
            // No validar formularios con novalidate, data-skip-validation, o form específicos
            if (this.hasAttribute('novalidate') || 
                this.noValidate || 
                this.getAttribute('data-skip-validation') === 'true' ||
                this.id === 'formCompletarDiseño' ||
                this.classList.contains('form-completar') ||
                this.hasAttribute('data-no-validation')) {
                return;
            }
            
            // Validación para campos requeridos vacíos
            const camposRequeridos = this.querySelectorAll('[required]:not([disabled])');
            let camposVacios = [];
            
            camposRequeridos.forEach(campo => {
                if (!campo.value.trim()) {
                    camposVacios.push(campo.previousElementSibling?.textContent || campo.getAttribute('name'));
                }
            });
            
            if (camposVacios.length > 0) {
                e.preventDefault();
                alert(`Por favor, completa los siguientes campos requeridos:\n- ${camposVacios.join('\n- ')}`);
                return;
            }
            
            // Validación específica para números (solo para campos habilitados y requeridos)
            const camposNumero = this.querySelectorAll('input[type="number"]:not([disabled]):not(.desarrollo-input)');
            camposNumero.forEach(campo => {
                // Solo validar si el campo tiene valor o es requerido
                if (campo.value !== '' && (campo.hasAttribute('required') || campo.value.trim() !== '')) {
                    const valor = parseFloat(campo.value);
                    if (isNaN(valor) || valor < 0) {
                        e.preventDefault();
                        alert(`El campo "${campo.previousElementSibling?.textContent || campo.getAttribute('name')}" debe ser un número válido mayor o igual a cero.`);
                        campo.focus();
                        return;
                    }
                }
            });
        });
    });
    
    // Efecto hover mejorado para botones
    const botones = document.querySelectorAll('.btn');
    botones.forEach(boton => {
        boton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.2)';
        });
        
        boton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Animación para las filas de las tablas
    const filasTabla = document.querySelectorAll('.table tbody tr');
    filasTabla.forEach((fila, index) => {
        fila.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s both`;
        
        fila.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            this.style.zIndex = '1';
        });
        
        fila.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
            this.style.zIndex = '';
        });
    });
    
    // Guardar estado del formulario (prevenir pérdida de datos)
    const formulariosPrincipales = document.querySelectorAll('form[id]');
    formulariosPrincipales.forEach(form => {
        const formId = form.getAttribute('id');
        
        // Cargar datos guardados
        const datosGuardados = localStorage.getItem(`form_${formId}`);
        if (datosGuardados) {
            try {
                const datos = JSON.parse(datosGuardados);
                Object.keys(datos).forEach(campo => {
                    const elemento = form.querySelector(`[name="${campo}"]`);
                    if (elemento && elemento.type !== 'hidden') {
                        elemento.value = datos[campo];
                    }
                });
            } catch (e) {
                console.log('Error al cargar datos del formulario:', e);
            }
        }
        
        // Guardar datos mientras se escribe
        const campos = form.querySelectorAll('input, textarea, select');
        campos.forEach(campo => {
            if (campo.type !== 'hidden' && campo.type !== 'submit') {
                campo.addEventListener('input', function() {
                    guardarEstadoFormulario(form, formId);
                });
            }
        });
        
        // Limpiar datos al enviar exitosamente
        form.addEventListener('submit', function() {
            localStorage.removeItem(`form_${formId}`);
        });
    });
    
    function guardarEstadoFormulario(form, formId) {
        const datos = {};
        const campos = form.querySelectorAll('input, textarea, select');
        campos.forEach(campo => {
            if (campo.name && campo.type !== 'hidden' && campo.type !== 'submit') {
                datos[campo.name] = campo.value;
            }
        });
        localStorage.setItem(`form_${formId}`, JSON.stringify(datos));
    }
    
    // Mostrar indicador de carga en formularios
    const formulariosSubmit = document.querySelectorAll('form');
    formulariosSubmit.forEach(form => {
        form.addEventListener('submit', function() {
            const botonSubmit = this.querySelector('button[type="submit"]');
            if (botonSubmit) {
                const textoOriginal = botonSubmit.innerHTML;
                botonSubmit.innerHTML = '<span class="loading"></span> Procesando...';
                botonSubmit.disabled = true;
                
                // Restaurar botón después de 3 segundos (en caso de error)
                setTimeout(() => {
                    botonSubmit.innerHTML = textoOriginal;
                    botonSubmit.disabled = false;
                }, 3000);
            }
        });
    });
    
    // Tooltip simple para elementos con título
    const elementosConTitulo = document.querySelectorAll('[title]');
    elementosConTitulo.forEach(elemento => {
        elemento.addEventListener('mouseenter', function(e) {
            const titulo = this.getAttribute('title');
            if (titulo) {
                const tooltip = document.createElement('div');
                tooltip.textContent = titulo;
                tooltip.style.cssText = `
                    position: absolute;
                    background: #333;
                    color: white;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    z-index: 1000;
                    pointer-events: none;
                    white-space: nowrap;
                `;
                
                document.body.appendChild(tooltip);
                
                const mover = (e) => {
                    tooltip.style.left = e.pageX + 10 + 'px';
                    tooltip.style.top = e.pageY - 30 + 'px';
                };
                
                mover(e);
                this.addEventListener('mousemove', mover);
                this.setAttribute('data-tooltip', 'active');
                
                this.addEventListener('mouseleave', function() {
                    tooltip.remove();
                    this.removeEventListener('mousemove', mover);
                    this.removeAttribute('data-tooltip');
                }, { once: true });
            }
        });
    });
    
    // Función para búsqueda en tiempo real en tablas
    function crearBuscadorTabla(tabla) {
        const contenedor = tabla.parentElement;
        const buscador = document.createElement('input');
        buscador.type = 'text';
        buscador.placeholder = 'Buscar en la tabla...';
        buscador.className = 'form-control';
        buscador.style.marginBottom = '1rem';
        
        contenedor.insertBefore(buscador, tabla);
        
        buscador.addEventListener('input', function() {
            const termino = this.value.toLowerCase();
            const filas = tabla.querySelectorAll('tbody tr');
            
            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(termino) ? '' : 'none';
            });
        });
    }
    
    // Agregar buscadores a tablas grandes
    const tablasGrandes = document.querySelectorAll('.table');
    tablasGrandes.forEach(tabla => {
        const filas = tabla.querySelectorAll('tbody tr');
        if (filas.length > 5) {
            crearBuscadorTabla(tabla);
        }
    });
    
    // Función para copiar códigos al clipboard
    const codigosCopiables = document.querySelectorAll('[style*="monospace"]');
    codigosCopiables.forEach(codigo => {
        codigo.style.cursor = 'pointer';
        codigo.title = 'Hacer clic para copiar';
        
        codigo.addEventListener('click', function() {
            const texto = this.textContent;
            navigator.clipboard.writeText(texto).then(() => {
                // Feedback visual
                const original = this.style.backgroundColor;
                this.style.backgroundColor = '#28a745';
                this.style.color = 'white';
                
                setTimeout(() => {
                    this.style.backgroundColor = original;
                    this.style.color = '';
                }, 500);
            }).catch(() => {
                alert('Código copiado: ' + texto);
            });
        });
    });
    
    // Validaciones AJAX para códigos únicos
    const codigoProgramaInput = document.getElementById('codigoPrograma');
    const versionProgramaInput = document.getElementById('versionPrograma');
    
    if (codigoProgramaInput && versionProgramaInput) {
        let timeoutId;
        
        function validarCodigoDiseño() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const codigoPrograma = codigoProgramaInput.value.trim();
                const versionPrograma = versionProgramaInput.value.trim();
                
                if (codigoPrograma && versionPrograma) {
                    fetch(`control/ajax.php?accion=validar_codigo_diseño&codigoPrograma=${encodeURIComponent(codigoPrograma)}&versionPrograma=${encodeURIComponent(versionPrograma)}`)
                        .then(response => response.json())
                        .then(data => {
                            mostrarValidacion(versionProgramaInput, data.success, data.message);
                        })
                        .catch(error => console.log('Error de validación:', error));
                }
            }, 500);
        }
        
        codigoProgramaInput.addEventListener('input', validarCodigoDiseño);
        versionProgramaInput.addEventListener('input', validarCodigoDiseño);
    }
    
    // Validación para códigos de competencia
    const codigoCompetenciaInput = document.getElementById('codigoCompetencia');
    if (codigoCompetenciaInput) {
        let timeoutId;
        
        codigoCompetenciaInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const codigoCompetencia = this.value.trim();
                const codigoDiseño = document.querySelector('input[name="codigoDiseño"]')?.value;
                
                if (codigoCompetencia && codigoDiseño) {
                    fetch(`control/ajax.php?accion=validar_codigo_competencia&codigoDiseño=${encodeURIComponent(codigoDiseño)}&codigoCompetencia=${encodeURIComponent(codigoCompetencia)}`)
                        .then(response => response.json())
                        .then(data => {
                            mostrarValidacion(this, data.success, data.message);
                        })
                        .catch(error => console.log('Error de validación:', error));
                }
            }, 500);
        });
    }
    
    // Validación para códigos de RAP
    const codigoRapInput = document.getElementById('codigoRap');
    if (codigoRapInput) {
        let timeoutId;
        
        codigoRapInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const codigoRap = this.value.trim();
                const codigoDiseñoCompetencia = document.querySelector('input[name="codigoDiseñoCompetencia"]')?.value;
                
                if (codigoRap && codigoDiseñoCompetencia) {
                    fetch(`control/ajax.php?accion=validar_codigo_rap&codigoDiseñoCompetencia=${encodeURIComponent(codigoDiseñoCompetencia)}&codigoRap=${encodeURIComponent(codigoRap)}`)
                        .then(response => response.json())
                        .then(data => {
                            mostrarValidacion(this, data.success, data.message);
                        })
                        .catch(error => console.log('Error de validación:', error));
                }
            }, 500);
        });
    }
    
    function mostrarValidacion(input, esValido, mensaje) {
        // Remover validaciones anteriores
        const validacionAnterior = input.parentNode.querySelector('.validacion-mensaje');
        if (validacionAnterior) {
            validacionAnterior.remove();
        }
        
        // Crear nuevo mensaje de validación
        const mensajeDiv = document.createElement('div');
        mensajeDiv.className = 'validacion-mensaje';
        mensajeDiv.style.cssText = `
            margin-top: 5px;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: ${esValido ? '#155724' : '#721c24'};
            background: ${esValido ? '#d4edda' : '#f8d7da'};
            border: 1px solid ${esValido ? '#c3e6cb' : '#f5c6cb'};
        `;
        mensajeDiv.innerHTML = `<i class="fas fa-${esValido ? 'check' : 'times'}"></i> ${mensaje}`;
        
        // Cambiar estilo del input
        input.style.borderColor = esValido ? '#28a745' : '#dc3545';
        
        input.parentNode.appendChild(mensajeDiv);
    }
});

// Validación específica para formularios de diseños curriculares
function validarFormularioDiseño(form) {
    // Verificar que al menos se llenen horas o meses
    const horasLectiva = parseFloat(form.querySelector('#horasDesarrolloLectiva')?.value || 0);
    const horasProductiva = parseFloat(form.querySelector('#horasDesarrolloProductiva')?.value || 0);
    const mesesLectiva = parseFloat(form.querySelector('#mesesDesarrolloLectiva')?.value || 0);
    const mesesProductiva = parseFloat(form.querySelector('#mesesDesarrolloProductiva')?.value || 0);
    
    const tieneHoras = horasLectiva > 0 || horasProductiva > 0;
    const tieneMeses = mesesLectiva > 0 || mesesProductiva > 0;
    
    if (!tieneHoras && !tieneMeses) {
        mostrarNotificacion('Debe llenar al menos las horas O los meses de desarrollo del programa', 'danger');
        return false;
    }
    
    return true;
}

// Cálculos automáticos para formularios de diseños
function configurarCalculosAutomaticos() {
    const camposHoras = ['horasDesarrolloLectiva', 'horasDesarrolloProductiva'];
    const camposMeses = ['mesesDesarrolloLectiva', 'mesesDesarrolloProductiva'];
    const totalHorasField = document.getElementById('totalHoras');
    const totalMesesField = document.getElementById('totalMeses');
    
    function calcularTotales() {
        if (!totalHorasField || !totalMesesField) return;
        
        let totalHoras = 0;
        let totalMeses = 0;
        
        camposHoras.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo && campo.value) {
                totalHoras += parseFloat(campo.value) || 0;
            }
        });
        
        camposMeses.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo && campo.value) {
                totalMeses += parseFloat(campo.value) || 0;
            }
        });
        
        totalHorasField.value = totalHoras.toFixed(2);
        totalMesesField.value = totalMeses.toFixed(2);
    }
    
    // Agregar listeners a todos los campos de duración
    [...camposHoras, ...camposMeses].forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('input', calcularTotales);
            campo.addEventListener('change', calcularTotales);
        }
    });
    
    // Calcular totales al cargar la página
    calcularTotales();
}

// Aplicar validaciones específicas según el tipo de formulario
document.addEventListener('DOMContentLoaded', function() {
    // Configurar cálculos automáticos si estamos en un formulario de diseños
    if (document.getElementById('horasDesarrolloLectiva')) {
        configurarCalculosAutomaticos();
    }
    
    // Agregar validación específica a formularios de diseño
    const formulariosDiseño = document.querySelectorAll('form[action*="diseños"]');
    formulariosDiseño.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioDiseño(this)) {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Función global para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo}`;
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: fadeIn 0.3s ease-out;
    `;
    notificacion.innerHTML = `
        <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${mensaje}
    `;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
}

// Añadir estilos de animación CSS si no existen
if (!document.querySelector('#animaciones-css')) {
    const estilos = document.createElement('style');
    estilos.id = 'animaciones-css';
    estilos.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100%); }
        }
    `;
    document.head.appendChild(estilos);
}
