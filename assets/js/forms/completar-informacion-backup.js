/**
 * JavaScript para la funcionalidad avanzada de completar información
 * Sistema de Gestión de Diseños Curriculares SENA
 * 
 * VERSIÓN CORREGIDA - Soluciona problemas de paginación y registros
 */

// ===============================================
// SISTEMA AJAX CON COOKIES PARA FILTROS
// ===============================================

const CompletarInformacionCorregido = {
    // Configuración
    config: {
        debounceDelay: 800,
        cookieExpiry: 7 // días
    },
    
    // Estados por sección independientes
    sectionStates: {
        disenos: {
            currentPage: 1,
            recordsPerPage: 10
        },
        competencias: {
            currentPage: 1,
            recordsPerPage: 10
        },
        raps: {
            currentPage: 1,
            recordsPerPage: 10
        }
    },
    
    // Inicialización
    init: function() {
        console.log('🚀 Inicializando Sistema Corregido...');
        this.loadFiltersFromCookies();
        this.applyInitialTableHeight();
        this.bindEvents();
    },
            recordsPerPage: 10,
            advancedFiltersOpen: false
        },
        competencias: {
            currentPage: 1,
            recordsPerPage: 10,
            advancedFiltersOpen: false
        },
        raps: {
            currentPage: 1,
            recordsPerPage: 10,
            advancedFiltersOpen: false
        },
        global: {
            formSubmitting: false,
            advancedFiltersOpen: false
        }
    },
    
    // Elementos del DOM
    elements: {},
    
    // Inicialización
    init: function() {
        console.log('Inicializando CompletarInformacion...');
        this.cacheElements();
        this.bindEvents();
        this.restoreStates();
        this.addAnimations();
        this.setupTooltips();
        this.validateForm();
        
        // Vincular eventos dinámicos inmediatamente
        setTimeout(() => {
            this.bindDynamicEvents();
        }, 100);
    },
    
    // Cache de elementos del DOM
    cacheElements: function() {
        this.elements = {
            form: document.getElementById('filtrosForm'),
            advancedFilters: document.getElementById('advanced-filters'),
            advancedArrow: document.getElementById('advanced-arrow'),
            busquedaInput: document.getElementById('busqueda'),
            submitBtn: document.querySelector('.btn-filter'),
            statCards: document.querySelectorAll('.stat-card'),
            resultSections: document.querySelectorAll('.section-results'),
            pageButtons: document.querySelectorAll('.page-btn'),
            filtersSection: document.querySelector('.filters-section'),
            horasMin: document.querySelector('input[name="horas_min"]'),
            horasMax: document.querySelector('input[name="horas_max"]'),
            seccionSelect: document.getElementById('seccion'),
            estadoSelect: document.getElementById('estado'),
            registrosPorPagina: document.querySelector('select[name="registros_por_pagina"]')
        };
    },
    
    // Vincular eventos
    bindEvents: function() {
        // Filtros avanzados
        const toggleBtn = document.querySelector('.advanced-filters-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleAdvancedFilters());
        }
        
        // Formulario principal
        if (this.elements.form) {
            this.elements.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
        
        // Búsqueda con debounce
        if (this.elements.busquedaInput) {
            this.elements.busquedaInput.addEventListener('input', (e) => this.handleSearchInput(e));
        }
        
        // Eventos AJAX para paginación y cambio de registros por página
        this.bindAjaxEvents();
        
        // Validación de rangos en tiempo real
        if (this.elements.horasMin) {
            this.elements.horasMin.addEventListener('input', () => this.validateRangeFilters());
        }
        if (this.elements.horasMax) {
            this.elements.horasMax.addEventListener('input', () => this.validateRangeFilters());
        }
        
        // Auto-submit para selects principales
        if (this.elements.seccionSelect) {
            this.elements.seccionSelect.addEventListener('change', () => this.autoSubmitForm());
        }
        
        // Indicadores de carga en paginación
        this.elements.pageButtons.forEach(btn => {
            btn.addEventListener('click', () => this.handlePageClick(btn));
        });
        
        // Limpiar filtros
        const clearBtn = document.querySelector('.filter-clear-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearAllFilters());
        }
    },
    
    // Alternar filtros avanzados
    toggleAdvancedFilters: function() {
        if (!this.elements.advancedFilters || !this.elements.advancedArrow) return;
        
        const isOpen = this.elements.advancedFilters.classList.contains('show');
        
        if (isOpen) {
            this.elements.advancedFilters.classList.remove('show');
            this.elements.advancedArrow.style.transform = 'rotate(0deg)';
            this.sectionStates.global.advancedFiltersOpen = false;
        } else {
            this.elements.advancedFilters.classList.add('show');
            this.elements.advancedArrow.style.transform = 'rotate(180deg)';
            this.sectionStates.global.advancedFiltersOpen = true;
        }
        
        // Guardar estado global
        this.saveGlobalState();
    },
    
    // Manejar envío del formulario
    handleFormSubmit: function(e) {
        if (this.sectionStates.global.formSubmitting) {
            e.preventDefault();
            return false;
        }
        
        if (!this.validateForm()) {
            e.preventDefault();
            return false;
        }
        
        this.sectionStates.global.formSubmitting = true;
        this.showLoadingState();
        
        return true;
    },
    
    // Manejar entrada de búsqueda con debounce
    handleSearchInput: function(e) {
        clearTimeout(this.searchTimeout);
        
        this.searchTimeout = setTimeout(() => {
            const value = e.target.value.trim();
            
            // Auto-submit solo si hay suficientes caracteres o está vacío
            if (value.length >= this.config.autoSubmitMinChars || value.length === 0) {
                this.autoSubmitForm();
            }
        }, this.config.debounceDelay);
    },
    
    // Auto-submit del formulario
    autoSubmitForm: function() {
        if (this.state.formSubmitting) return;
        
        // Resetear página a 1 cuando se cambian filtros
        const paginaInput = document.querySelector('input[name="pagina"]');
        if (paginaInput) {
            paginaInput.value = '1';
        }
        
        this.elements.form.submit();
    },
    
    // Manejar click en paginación
    handlePageClick: function(btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.style.pointerEvents = 'none';
    },
    
    // Cambiar registros por página
    cambiarRegistrosPorPagina: function(valor) {
        if (this.elements.registrosPorPagina) {
            this.elements.registrosPorPagina.value = valor;
        }
        
        // Resetear a página 1
        const paginaInput = document.querySelector('input[name="pagina"]');
        if (paginaInput) {
            paginaInput.value = '1';
        }
        
        this.autoSubmitForm();
    },
    
    // Validar formulario
    validateForm: function() {
        return this.validateRangeFilters();
    },
    
    // Validar filtros de rango
    validateRangeFilters: function() {
        if (!this.elements.horasMin || !this.elements.horasMax) return true;
        
        const min = parseFloat(this.elements.horasMin.value) || 0;
        const max = parseFloat(this.elements.horasMax.value) || 0;
        
        // Limpiar errores previos
        this.elements.horasMin.classList.remove('error');
        this.elements.horasMax.classList.remove('error');
        
        if (min > 0 && max > 0 && min > max) {
            this.elements.horasMin.classList.add('error');
            this.elements.horasMax.classList.add('error');
            this.showMessage('El valor mínimo no puede ser mayor que el máximo', 'warning');
            return false;
        }
        
        return true;
    },
    
    // Mostrar estado de carga
    showLoadingState: function() {
        if (this.elements.submitBtn) {
            this.elements.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Filtrando...';
            this.elements.submitBtn.disabled = true;
        }
        
        // Agregar overlay de carga
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(overlay);
    },
    
    // Restaurar estados guardados (por sección)
    restoreStates: function() {
        // Restaurar estado global de filtros avanzados
        const advancedOpen = localStorage.getItem('completarInfo_global_advancedFiltersOpen');
        if (advancedOpen === 'true') {
            this.sectionStates.global.advancedFiltersOpen = true;
            if (this.elements.advancedFilters && this.elements.advancedArrow) {
                this.elements.advancedFilters.classList.add('show');
                this.elements.advancedArrow.style.transform = 'rotate(180deg)';
            }
        }
        
        // Restaurar estados por sección
        ['disenos', 'competencias', 'raps'].forEach(section => {
            const sectionState = localStorage.getItem(`completarInfo_${section}_state`);
            if (sectionState) {
                try {
                    const state = JSON.parse(sectionState);
                    this.sectionStates[section] = { ...this.sectionStates[section], ...state };
                } catch (e) {
                    console.warn(`Error restaurando estado de ${section}:`, e);
                }
            }
        });
    },
    
    // Guardar estado global
    saveGlobalState: function() {
        localStorage.setItem('completarInfo_global_advancedFiltersOpen', this.sectionStates.global.advancedFiltersOpen);
    },
    
    // Guardar estado de una sección específica
    saveSectionState: function(section) {
        if (this.sectionStates[section]) {
            localStorage.setItem(`completarInfo_${section}_state`, JSON.stringify(this.sectionStates[section]));
        }
    },
    
    // Obtener estado de una sección
    getSectionState: function(section) {
        return this.sectionStates[section] || {
            currentPage: 1,
            recordsPerPage: 10,
            advancedFiltersOpen: false
        };
    },
    
    // Cambiar registros por página (función interna del objeto)
    cambiarRegistrosPorPaginaInterno: function(valor, seccion = null) {
        if (seccion && this.sectionStates[seccion]) {
            this.sectionStates[seccion].recordsPerPage = parseInt(valor);
            this.saveSectionState(seccion);
        }
        
        // Para compatibilidad, también actualizar el formulario si no hay sección específica
        if (!seccion) {
            const form = this.elements.form;
            const registrosPorPaginaInput = document.querySelector('select[name="registros_por_pagina"]');
            
            if (registrosPorPaginaInput) {
                registrosPorPaginaInput.value = valor;
            }
            
            if (form) {
                form.submit();
            }
        }
    },
    
    // Agregar animaciones
    addAnimations: function() {
        // Animar tarjetas de estadísticas
        this.elements.statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * this.config.animationDelay}ms`;
            card.classList.add('fade-in');
        });
        
        // Animar secciones de resultados
        this.elements.resultSections.forEach((section, index) => {
            section.style.animationDelay = `${index * (this.config.animationDelay * 2)}ms`;
            section.classList.add('slide-in');
        });
    },
    
    // Configurar tooltips
    setupTooltips: function() {
        const tooltipConfigs = [
            {
                element: this.elements.busquedaInput,
                message: 'Busca por código de diseño, nombre del programa, competencia o RAP'
            },
            {
                element: this.elements.horasMin,
                message: 'Filtrar por cantidad mínima de horas de desarrollo'
            },
            {
                element: this.elements.horasMax,
                message: 'Filtrar por cantidad máxima de horas de desarrollo'
            },
            {
                element: this.elements.estadoSelect,
                message: 'Filtrar por estado de completitud de los registros'
            }
        ];
        
        tooltipConfigs.forEach(config => {
            if (config.element) {
                config.element.setAttribute('data-tooltip', config.message);
                config.element.classList.add('tooltip');
            }
        });
    },
    
    // Mostrar mensaje
    showMessage: function(message, type = 'info') {
        if (!this.elements.filtersSection) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `validation-message ${type}`;
        messageDiv.innerHTML = `
            <i class="fas fa-${this.getIconForType(type)}"></i>
            <span>${message}</span>
            <button type="button" class="message-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        this.elements.filtersSection.appendChild(messageDiv);
        
        // Auto-remover
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, this.config.messageDisplayTime);
    },
    
    // Obtener icono para tipo de mensaje
    getIconForType: function(type) {
        const icons = {
            success: 'check-circle',
            warning: 'exclamation-triangle',
            error: 'times-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    },
    
    // Limpiar todos los filtros
    clearAllFilters: function() {
        window.location.href = '?accion=completar_informacion';
    },
    
    // Funciones para futuras mejoras
    exportResults: function(format = 'excel') {
        this.showMessage('Funcionalidad de exportación en desarrollo', 'info');
    },
    
    toggleSelectAll: function() {
        this.showMessage('Funcionalidad de selección múltiple en desarrollo', 'info');
    },
    
    // Estadísticas en tiempo real
    updateStatistics: function() {
        // Para futuras mejoras con AJAX
        this.showMessage('Actualización de estadísticas en tiempo real en desarrollo', 'info');
    }
};

// ===============================================
// FUNCIONES GLOBALES PARA COMPATIBILIDAD
// ===============================================

// Función global para cambiar registros por página con soporte para secciones
function cambiarRegistrosPorPagina(valor, seccion = null) {
    if (seccion) {
        // Cambio específico por sección
        const url = new URL(window.location);
        url.searchParams.set(`registros_${seccion}`, valor);
        url.searchParams.set(`pagina_${seccion}`, 1); // Reset página de la sección
        window.location.href = url.toString();
    } else {
        // Cambio global (compatibilidad)
        const form = document.getElementById('filtrosForm');
        const registrosPorPaginaInput = document.querySelector('select[name="registros_por_pagina"]');
        
        if (registrosPorPaginaInput) {
            registrosPorPaginaInput.value = valor;
        }
        
        if (form) {
            form.submit();
        }
    }
}

// Función global para ir a una página específica con validación
function irAPagina(pagina, seccion = null) {
    if (seccion) {
        // Navegación específica por sección
        const url = new URL(window.location);
        url.searchParams.set(`pagina_${seccion}`, Math.max(1, parseInt(pagina)));
        window.location.href = url.toString();
    } else {
        // Navegación global (compatibilidad)
        const form = document.getElementById('filtrosForm');
        const paginaInput = document.querySelector('input[name="pagina"]');
        
        if (paginaInput) {
            paginaInput.value = Math.max(1, parseInt(pagina));
        }
        
        if (form) {
            form.submit();
        }
    }
}

// Función global para alternar filtros avanzados
function toggleAdvancedFilters() {
    CompletarInformacion.toggleAdvancedFilters();
}

// Función global para exportar resultados
function exportResults(format) {
    CompletarInformacion.exportResults(format);
}

// Función global para seleccionar todos
function toggleSelectAll() {
    CompletarInformacion.toggleSelectAll();
}

// Función para validar parámetros de URL al cargar la página
function validarParametrosURL() {
    const url = new URL(window.location);
    let hayParametrosInvalidos = false;
    
    // Lista de parámetros que deben ser números positivos
    const parametrosPagina = ['pagina_disenos', 'pagina_competencias', 'pagina_raps'];
    const parametrosRegistros = ['registros_disenos', 'registros_competencias', 'registros_raps'];
    const registrosPermitidos = [5, 10, 25, 50, 100];
    
    // Validar páginas
    parametrosPagina.forEach(param => {
        const valor = url.searchParams.get(param);
        if (valor !== null) {
            const valorNum = parseInt(valor);
            if (isNaN(valorNum) || valorNum < 1) {
                url.searchParams.set(param, '1');
                hayParametrosInvalidos = true;
            }
        }
    });
    
    // Validar registros por página
    parametrosRegistros.forEach(param => {
        const valor = url.searchParams.get(param);
        if (valor !== null) {
            const valorNum = parseInt(valor);
            if (isNaN(valorNum) || !registrosPermitidos.includes(valorNum)) {
                url.searchParams.set(param, '10');
                hayParametrosInvalidos = true;
            }
        }
    });
    
    // Redirigir si hay parámetros inválidos
    if (hayParametrosInvalidos) {
        window.location.href = url.toString();
        return false;
    }
    
    return true;
}

// ===============================================
// FUNCIONALIDAD AJAX PARA RECARGA PARCIAL
// ===============================================

/**
 * Vincular eventos AJAX para paginación y cambio de registros por página
 */
CompletarInformacion.bindAjaxEvents = function() {
    // Interceptar clicks en enlaces de paginación
    document.addEventListener('click', (e) => {
        // Buscar el enlace más cercano con los atributos necesarios
        const target = e.target.closest('a[data-seccion][data-pagina]');
        if (target) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Click interceptado en paginación:', target);
            
            const seccion = target.getAttribute('data-seccion');
            const pagina = target.getAttribute('data-pagina');
            
            console.log('Cargando sección:', seccion, 'página:', pagina);
            
            this.cargarSeccionAjax(seccion, { ["pagina_" + seccion]: pagina });
            return false;
        }
    });

    // Interceptar cambios en selectores de registros por página
    document.addEventListener('change', (e) => {
        if (e.target.matches('select[data-seccion][data-registros]')) {
            e.preventDefault();
            
            console.log('Cambio interceptado en registros por página:', e.target);
            
            const seccion = e.target.getAttribute('data-seccion');
            const registros = e.target.value;
            
            console.log('Cambiando registros para sección:', seccion, 'registros:', registros);
            
            this.cargarSeccionAjax(seccion, { 
                ["registros_" + seccion]: registros,
                ["pagina_" + seccion]: 1 // Reset a página 1 al cambiar registros
            });
        }
    });
    
    // También interceptar eventos en elementos dinámicos
    document.addEventListener('DOMContentLoaded', () => {
        this.bindDynamicEvents();
    });
    
    // Bind events después de cada actualización AJAX
    setTimeout(() => {
        this.bindDynamicEvents();
    }, 500);
};

/**
 * Cargar una sección específica vía AJAX
 * @param {string} seccion - La sección a cargar (disenos, competencias, raps)
 * @param {object} parametrosAdicionales - Parámetros adicionales para la petición
 */
CompletarInformacion.cargarSeccionAjax = function(seccion, parametrosAdicionales = {}) {
    // Guardar posición de scroll de la sección antes de la petición
    const seccionContainer = document.querySelector(`#seccion-${seccion}`);
    let scrollPosition = 0;
    if (seccionContainer) {
        const rect = seccionContainer.getBoundingClientRect();
        scrollPosition = window.pageYOffset + rect.top;
    }
    
    // Mostrar indicador de carga
    this.mostrarCargandoSeccion(seccion);
    
    // Recopilar todos los filtros actuales del formulario
    const formData = new FormData(this.elements.form);
    const params = new URLSearchParams();
    
    // Añadir parámetros del formulario
    for (let [key, value] of formData.entries()) {
        params.append(key, value);
    }
    
    // Añadir parámetros adicionales (paginación, registros)
    for (let [key, value] of Object.entries(parametrosAdicionales)) {
        params.set(key, value);
    }
    
    // Añadir parámetros AJAX
    params.set('accion_ajax', 'actualizar_seccion');
    params.set('seccion', seccion);
    
    // Realizar petición AJAX con la ruta correcta
    fetch('ajax.php?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            this.actualizarSeccionHTML(seccion, data);
            this.actualizarURLHistorial(seccion, parametrosAdicionales);
            
            // Restaurar scroll suave a la sección, no al top
            setTimeout(() => {
                const nuevaSeccionContainer = document.querySelector(`#seccion-${seccion}`);
                if (nuevaSeccionContainer) {
                    // Calcular posición relativa dentro de la sección actualizada
                    const rect = nuevaSeccionContainer.getBoundingClientRect();
                    const currentScroll = window.pageYOffset;
                    const sectionTop = currentScroll + rect.top;
                    
                    // Solo hacer scroll si la sección no está visible o está muy arriba/abajo
                    const viewportHeight = window.innerHeight;
                    const sectionHeight = rect.height;
                    
                    // Si la sección está parcialmente fuera de vista, hacer scroll suave
                    if (rect.top < 0 || rect.bottom > viewportHeight) {
                        window.scrollTo({
                            top: sectionTop - 50, // 50px de offset desde el top
                            behavior: 'smooth'
                        });
                    }
                    
                    // Efecto visual sutil para indicar la actualización
                    nuevaSeccionContainer.style.transition = 'box-shadow 0.3s ease';
                    nuevaSeccionContainer.style.boxShadow = '0 0 15px rgba(0, 123, 255, 0.3)';
                    
                    setTimeout(() => {
                        nuevaSeccionContainer.style.boxShadow = '';
                    }, 1500);
                }
            }, 150);
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error en petición AJAX:', error);
        this.mostrarErrorSeccion(seccion, error.message);
    })
    .finally(() => {
        this.ocultarCargandoSeccion(seccion);
    });
};

/**
 * Mostrar indicador de carga en una sección
 * @param {string} seccion - La sección que está cargando
 */
CompletarInformacion.mostrarCargandoSeccion = function(seccion) {
    const container = document.querySelector(`#seccion-${seccion}`);
    if (container) {
        // Añadir clase de carga
        container.classList.add('loading');
        
        // Deshabilitar botones de paginación de esta sección
        const buttons = container.querySelectorAll('.pagination a, select');
        buttons.forEach(btn => {
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.6';
        });
    }
};

/**
 * Ocultar indicador de carga de una sección
 * @param {string} seccion - La sección que terminó de cargar
 */
CompletarInformacion.ocultarCargandoSeccion = function(seccion) {
    const container = document.querySelector(`#seccion-${seccion}`);
    if (container) {
        // Quitar clase de carga
        container.classList.remove('loading');
        
        // Rehabilitar botones
        const buttons = container.querySelectorAll('.pagination a, select');
        buttons.forEach(btn => {
            btn.style.pointerEvents = '';
            btn.style.opacity = '';
        });
        
        // Quitar overlay de carga
        const overlay = container.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
};

/**
 * Actualizar el HTML de una sección con los datos AJAX
 * @param {string} seccion - La sección a actualizar
 * @param {object} data - Los datos de respuesta AJAX
 */
CompletarInformacion.actualizarSeccionHTML = function(seccion, data) {
    // Actualizar tabla
    const tablaContainer = document.querySelector(`#tabla-${seccion}`);
    if (tablaContainer && data.tabla_html) {
        // Animación de salida suave
        tablaContainer.style.transition = 'opacity 0.2s ease';
        tablaContainer.style.opacity = '0.3';
        
        setTimeout(() => {
            tablaContainer.innerHTML = data.tabla_html;
            
            // Animación de entrada suave
            tablaContainer.style.opacity = '1';
        }, 100);
    }
    
    // Actualizar paginación inferior
    const paginacionContainer = document.querySelector(`#paginacion-${seccion}`);
    if (paginacionContainer && data.paginacion_html) {
        paginacionContainer.innerHTML = data.paginacion_html;
    }
    
    // Actualizar paginación superior si existe
    const paginacionSuperiorContainer = document.querySelector(`#paginacion-superior-${seccion}`);
    if (paginacionSuperiorContainer && data.paginacion_html) {
        paginacionSuperiorContainer.innerHTML = data.paginacion_html;
    }
    
    // Actualizar contador de registros si existe
    const contadorContainer = document.querySelector(`#contador-${seccion}`);
    if (contadorContainer) {
        const inicio = ((data.pagina_actual - 1) * data.registros_por_pagina) + 1;
        const fin = Math.min(data.pagina_actual * data.registros_por_pagina, data.total_registros);
        contadorContainer.textContent = `Mostrando ${inicio} a ${fin} de ${data.total_registros} registros`;
    }
    
    // Re-vincular eventos en la nueva sección (por si hay nuevos elementos)
    setTimeout(() => {
        this.bindDynamicEvents();
        console.log('Eventos re-vinculados después de actualización AJAX para sección:', seccion);
    }, 200);
};

/**
 * Mostrar error en una sección
 * @param {string} seccion - La sección donde ocurrió el error
 * @param {string} mensaje - El mensaje de error
 */
CompletarInformacion.mostrarErrorSeccion = function(seccion, mensaje) {
    const container = document.querySelector(`#seccion-${seccion}`);
    if (container) {
        // Crear o actualizar mensaje de error
        let errorDiv = container.querySelector('.error-ajax');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-ajax alert alert-danger';
            errorDiv.style.marginTop = '10px';
            container.appendChild(errorDiv);
        }
        
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            Error al cargar la sección: ${mensaje}
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i> Cerrar
            </button>
        `;
        
        // Auto-ocultar después de unos segundos
        setTimeout(() => {
            if (errorDiv.parentElement) {
                errorDiv.remove();
            }
        }, 8000);
    }
};

/**
 * Actualizar la URL del navegador para mantener el historial
 * @param {string} seccion - La sección que se actualizó
 * @param {object} parametros - Los parámetros que cambiaron
 */
CompletarInformacion.actualizarURLHistorial = function(seccion, parametros) {
    const url = new URL(window.location);
    
    // Actualizar parámetros en la URL
    for (let [key, value] of Object.entries(parametros)) {
        url.searchParams.set(key, value);
    }
    
    // Actualizar historial del navegador sin recargar
    window.history.replaceState(null, '', url.toString());
};


// ===============================================
// INICIALIZACIÓN
// ===============================================

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando CompletarInformacion...');
    
    // Validar parámetros de URL antes de inicializar
    if (!validarParametrosURL()) {
        return; // Se hará redirección, no continuar
    }
    
    CompletarInformacion.init();
    
    // Debug: mostrar información de elementos encontrados
    setTimeout(() => {
        console.log('Elementos de paginación encontrados:', document.querySelectorAll('a[data-seccion][data-pagina]').length);
        console.log('Selectores de registros encontrados:', document.querySelectorAll('select[data-seccion][data-registros]').length);
        
        // Forzar binding dinámico adicional
        CompletarInformacion.bindDynamicEvents();
    }, 500);
    
    // Asegurar que las funciones globales estén disponibles en window
    window.cambiarRegistrosPorPagina = cambiarRegistrosPorPagina;
    window.toggleAdvancedFilters = toggleAdvancedFilters;
    window.irAPagina = irAPagina;
    window.exportResults = exportResults;
    window.toggleSelectAll = toggleSelectAll;
    window.validarParametrosURL = validarParametrosURL;
});

// Manejar errores JavaScript
window.addEventListener('error', function(e) {
    console.error('Error en completar-informacion.js:', e.error);
});

// Exportar para uso en otros scripts si es necesario
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CompletarInformacion;
}

/**
 * Vincular eventos en elementos dinámicos (después de actualizaciones AJAX)
 */
CompletarInformacion.bindDynamicEvents = function() {
    console.log('Vinculando eventos dinámicos...');
    
    // Re-vincular eventos específicos para elementos recién creados
    document.querySelectorAll('a[data-seccion][data-pagina]').forEach(link => {
        if (!link.hasAttribute('data-ajax-bound')) {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Click directo en paginación:', link);
                
                const seccion = link.getAttribute('data-seccion');
                const pagina = link.getAttribute('data-pagina');
                
                this.cargarSeccionAjax(seccion, { ["pagina_" + seccion]: pagina });
                
                return false;
            });
            link.setAttribute('data-ajax-bound', 'true');
        }
    });
    
    // Re-vincular eventos para selectores de registros
    document.querySelectorAll('select[data-seccion][data-registros]').forEach(select => {
        if (!select.hasAttribute('data-ajax-bound')) {
            select.addEventListener('change', (e) => {
                e.preventDefault();
                
                console.log('Cambio directo en registros:', select);
                
                const seccion = select.getAttribute('data-seccion');
                const registros = select.value;
                
                this.cargarSeccionAjax(seccion, { 
                    ["registros_" + seccion]: registros,
                    ["pagina_" + seccion]: 1
                });
            });
            select.setAttribute('data-ajax-bound', 'true');
        }
    });
    
    // Re-aplicar estilos a botones de acción recién creados
    document.querySelectorAll('.btn-edit:not([data-styled])').forEach(btn => {
        btn.setAttribute('data-styled', 'true');
        
        // Añadir hover mejorado
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Re-aplicar animaciones a filas de tabla recién creadas
    document.querySelectorAll('table tbody tr:not([data-animated])').forEach((fila, index) => {
        fila.setAttribute('data-animated', 'true');
        fila.style.opacity = '0';
        fila.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            fila.style.transition = 'all 0.3s ease';
            fila.style.opacity = '1';
            fila.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    // Re-aplicar efectos a missing fields
    document.querySelectorAll('.missing-field:not([data-styled])').forEach(field => {
        field.setAttribute('data-styled', 'true');
        
        // Efecto sutil de pulsación
        field.addEventListener('click', function() {
            this.style.transform = 'scale(1.05)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    console.log('Eventos dinámicos vinculados correctamente');
};
