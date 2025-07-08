/**
 * JavaScript CORREGIDO para completar información
 * Sistema de Gestión de Diseños Curriculares SENA
 * 
 * SOLUCIONA:
 * - Problemas de paginación duplicada
 * - Registros por página que no se respetan
 * - Pérdida de filtros (usa cookies)
 * - Altura de tablas inicial
 */

const CompletarInformacionCorregido = {
    // Estados por sección independientes
    sectionStates: {
        disenos: { currentPage: 1, recordsPerPage: 10 },
        competencias: { currentPage: 1, recordsPerPage: 10 },
        raps: { currentPage: 1, recordsPerPage: 10 }
    },
    
    // Inicialización
    init: function() {
        console.log('🚀 Inicializando Sistema Corregido...');
        this.detectInitialSelectorValues();
        this.loadFiltersFromCookies();
        this.applyInitialTableHeight();
        this.bindEvents();
        this.updateNavigationLinks();
    },
    
    // Detectar valores iniciales de los selectores de registros por página
    detectInitialSelectorValues: function() {
        ['disenos', 'competencias', 'raps'].forEach(seccion => {
            const selector = document.querySelector(`select[data-seccion="${seccion}"].ajax-records-selector`);
            if (selector && selector.value) {
                const valorInicial = parseInt(selector.value);
                this.sectionStates[seccion].recordsPerPage = valorInicial;
                console.log('🔍 Detectado valor inicial para', seccion + ':', valorInicial);
            }
        });
    },
    
    // ===============================================
    // SISTEMA DE COOKIES PARA FILTROS
    // ===============================================
    
    setCookie: function(name, value, days = 7) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + expires.toUTCString() + ";path=/";
    },
    
    getCookie: function(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
        }
        return null;
    },
    
    saveFiltersToStorage: function() {
        const form = document.getElementById('filtrosForm');
        if (!form) return;
        
        const formData = new FormData(form);
        const filters = {};
        
        // Guardar filtros del formulario
        for (let [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                filters[key] = value;
            }
        }
        
        // Guardar estados de sección
        filters['_sectionStates'] = JSON.stringify(this.sectionStates);
        
        // Guardar en cookie
        this.setCookie('completar_filtros', JSON.stringify(filters));
        console.log('💾 Filtros guardados:', Object.keys(filters).length, 'elementos');
    },
    
    loadFiltersFromCookies: function() {
        const cookieData = this.getCookie('completar_filtros');
        if (!cookieData) return;
        
        try {
            const filters = JSON.parse(cookieData);
            console.log('📋 Cargando filtros desde cookie:', Object.keys(filters).length, 'elementos');
            
            // Restaurar estados de sección
            if (filters['_sectionStates']) {
                const savedStates = JSON.parse(filters['_sectionStates']);
                this.sectionStates = { ...this.sectionStates, ...savedStates };
                delete filters['_sectionStates'];
            }
            
            // Aplicar filtros al formulario con delay
            setTimeout(() => {
                Object.entries(filters).forEach(([key, value]) => {
                    const element = document.querySelector('[name="' + key + '"]');
                    if (element) {
                        element.value = value;
                        console.log('🔄 Restaurado', key + ':', value);
                    }
                });
            }, 100);
            
        } catch (e) {
            console.warn('⚠️ Error cargando filtros:', e);
        }
    },
    
    // ===============================================
    // ALTURA ADAPTATIVA DE TABLAS
    // ===============================================
    
    applyInitialTableHeight: function() {
        // Aplicar altura a todas las tablas existentes
        document.querySelectorAll('.section-results').forEach(section => {
            const table = section.querySelector('table');
            if (table) {
                this.wrapTableWithContainer(table);
            }
        });
    },
    
    wrapTableWithContainer: function(table) {
        // Solo envolver si no está ya envuelto
        if (table.parentElement.classList.contains('table-container')) {
            return;
        }
        
        const wrapper = document.createElement('div');
        wrapper.className = 'table-container';
        
        // Contar filas para determinar altura
        const rows = table.querySelectorAll('tbody tr');
        wrapper.setAttribute('data-records', rows.length);
        
        // Envolver la tabla
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
        
        console.log('📏 Tabla envuelta con', rows.length, 'registros');
    },
    
    // ===============================================
    // EVENTOS Y NAVEGACIÓN
    // ===============================================
    
    bindEvents: function() {
        // Guardar filtros al cambiar inputs
        document.addEventListener('change', (e) => {
            if (e.target.closest('#filtrosForm')) {
                setTimeout(() => this.saveFiltersToStorage(), 100);
            }
        });
        
        // Guardar filtros al escribir (con debounce)
        document.addEventListener('input', (e) => {
            if (e.target.closest('#filtrosForm')) {
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => this.saveFiltersToStorage(), 1000);
            }
        });
        
        // Paginación AJAX
        document.addEventListener('click', (e) => {
            const ajaxBtn = e.target.closest('a[data-seccion][data-pagina]');
            if (ajaxBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const seccion = ajaxBtn.getAttribute('data-seccion');
                const pagina = parseInt(ajaxBtn.getAttribute('data-pagina'));
                
                console.log('📄 Navegando', seccion, 'a página', pagina);
                
                // Actualizar estado ANTES de la petición
                this.sectionStates[seccion].currentPage = pagina;
                
                // CRÍTICO: Incluir registros por página actuales del estado
                const registrosActuales = this.sectionStates[seccion].recordsPerPage || 10;
                
                console.log('📊 Usando', registrosActuales, 'registros por página para navegación');
                
                // Realizar petición con registros por página del estado
                this.cargarSeccionAjax(seccion, {
                    ['pagina_' + seccion]: pagina,
                    ['registros_' + seccion]: registrosActuales
                });
                
                return false;
            }
        });
        
        // Cambio de registros por página
        document.addEventListener('change', (e) => {
            const selector = e.target.closest('select[data-seccion].ajax-records-selector');
            if (selector) {
                e.preventDefault();
                
                const seccion = selector.getAttribute('data-seccion');
                const registros = parseInt(selector.value);
                
                console.log('📊 Cambiando', seccion, 'a', registros, 'registros');
                
                // Actualizar estado ANTES de la petición
                this.sectionStates[seccion].recordsPerPage = registros;
                this.sectionStates[seccion].currentPage = 1; // Reset a página 1
                
                // Para "Todos" (-1), forzar página 1
                const params = {
                    ['registros_' + seccion]: registros,
                    ['pagina_' + seccion]: 1
                };
                
                // Realizar petición
                this.cargarSeccionAjax(seccion, params);
            }
        });
    },
    
    // ===============================================
    // AJAX CORREGIDO
    // ===============================================
    
    cargarSeccionAjax: function(seccion, parametrosAdicionales = {}) {
        // Prevenir cargas múltiples
        if (this['loading_' + seccion]) {
            console.log('⏳', seccion, 'ya está cargando');
            return;
        }
        
        this['loading_' + seccion] = true;
        this.mostrarCargando(seccion);
        
        // Recopilar TODOS los filtros del formulario
        const form = document.getElementById('filtrosForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Añadir filtros del formulario
        for (let [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                params.append(key, value);
            }
        }
        
        // CRÍTICO: Asegurar que siempre se envía el número de registros por página
        const registrosKey = 'registros_' + seccion;
        if (!parametrosAdicionales[registrosKey]) {
            // Si no se especifica en parámetros adicionales, usar del estado o selector actual
            let registrosActuales = this.sectionStates[seccion]?.recordsPerPage;
            
            // Como respaldo, leer del selector actual en el DOM
            if (!registrosActuales) {
                const selector = document.querySelector(`select[data-seccion="${seccion}"].ajax-records-selector`);
                if (selector && selector.value) {
                    registrosActuales = parseInt(selector.value);
                    // Actualizar estado también
                    this.sectionStates[seccion].recordsPerPage = registrosActuales;
                }
            }
            
            // Usar 10 como último respaldo
            if (!registrosActuales) {
                registrosActuales = 10;
            }
            
            parametrosAdicionales[registrosKey] = registrosActuales;
            console.log('🔧 Forzando registros por página:', registrosActuales, 'para', seccion);
        }
        
        // Añadir parámetros específicos de la sección
        Object.entries(parametrosAdicionales).forEach(([key, value]) => {
            params.set(key, value);
        });
        
        // Parámetros AJAX requeridos
        params.set('accion_ajax', 'actualizar_seccion');
        params.set('seccion', seccion);
        
        console.log('🌐 Petición AJAX:', params.toString().substring(0, 100) + '...');
        
        // Realizar petición
        fetch('ajax.php?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.actualizarContenido(seccion, data);
                this.saveFiltersToStorage(); // Guardar después de éxito
                console.log('✅', seccion, 'actualizado correctamente');
            } else {
                throw new Error(data.message || 'Error del servidor');
            }
        })
        .catch(error => {
            console.error('❌ Error cargando', seccion + ':', error);
            this.mostrarError(seccion, error.message);
        })
        .finally(() => {
            this['loading_' + seccion] = false;
            this.ocultarCargando(seccion);
        });
    },
    
    actualizarContenido: function(seccion, data) {
        // Actualizar tabla
        const tablaContainer = document.querySelector('#tabla-' + seccion);
        if (tablaContainer && data.tabla_html) {
            tablaContainer.innerHTML = data.tabla_html;
            
            // Aplicar contenedor a la nueva tabla
            const newTable = tablaContainer.querySelector('table');
            if (newTable) {
                this.wrapTableWithContainer(newTable);
            }
        }
        
        // Actualizar paginación
        const paginacionContainer = document.querySelector('#paginacion-' + seccion);
        if (paginacionContainer && data.paginacion_html) {
            paginacionContainer.innerHTML = data.paginacion_html;
        }
        
        // Restaurar el valor del selector de registros por página
        setTimeout(() => {
            const selector = document.querySelector(`select[data-seccion="${seccion}"].ajax-records-selector`);
            if (selector) {
                const estadoSeccion = this.sectionStates[seccion];
                if (estadoSeccion && estadoSeccion.recordsPerPage !== undefined) {
                    selector.value = estadoSeccion.recordsPerPage;
                    console.log('🔄 Restaurado selector', seccion, 'a', estadoSeccion.recordsPerPage);
                }
            }
        }, 100);
        
        // Re-aplicar eventos y links con delay
        setTimeout(() => {
            this.updateNavigationLinks();
        }, 200);
    },
    
    // ===============================================
    // UTILIDADES
    // ===============================================
    
    mostrarCargando: function(seccion) {
        const container = document.querySelector('#seccion-' + seccion);
        if (container) {
            container.classList.add('loading');
        }
    },
    
    ocultarCargando: function(seccion) {
        const container = document.querySelector('#seccion-' + seccion);
        if (container) {
            container.classList.remove('loading');
        }
    },
    
    mostrarError: function(seccion, mensaje) {
        const container = document.querySelector('#tabla-' + seccion);
        if (container) {
            container.innerHTML = 
                '<div class="ajax-error-retry">' +
                    '<div class="error-message">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        'Error cargando ' + seccion + ': ' + mensaje +
                    '</div>' +
                    '<button class="retry-btn" onclick="CompletarInformacionCorregido.cargarSeccionAjax(\'' + seccion + '\')">' +
                        '<i class="fas fa-redo"></i> Reintentar' +
                    '</button>' +
                '</div>';
        }
    },
    
    updateNavigationLinks: function() {
        // Obtener filtros actuales
        const form = document.getElementById('filtrosForm');
        if (!form) return;
        
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Construir query string con filtros
        for (let [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                params.append(key, value);
            }
        }
        
        // Añadir estados de sección
        Object.entries(this.sectionStates).forEach(([seccion, state]) => {
            if (state.currentPage > 1) {
                params.set('pagina_' + seccion, state.currentPage);
            }
            if (state.recordsPerPage !== 10) {
                params.set('registros_' + seccion, state.recordsPerPage);
            }
        });
        
        const queryString = params.toString();
        
        // Actualizar links de navegación
        document.querySelectorAll('a[href*="completar_informacion"]').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.includes('completar&')) {
                const separator = href.includes('?') ? '&' : '?';
                const newHref = queryString ? href + separator + queryString : href;
                link.setAttribute('href', newHref);
            }
        });
        
        console.log('🔗 Links de navegación actualizados');
    }
};

// ===============================================
// INICIALIZACIÓN
// ===============================================

document.addEventListener('DOMContentLoaded', function() {
    CompletarInformacionCorregido.init();
    console.log('✅ Sistema Corregido inicializado');
});

// Compatibilidad con código existente
window.CompletarInformacion = CompletarInformacionCorregido;
