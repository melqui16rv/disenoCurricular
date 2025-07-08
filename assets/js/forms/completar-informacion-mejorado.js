/**
 * JavaScript Mejorado para Completar Información
 * Sistema de Gestión de Diseños Curriculares SENA
 * 
 * Características:
 * - Cache inteligente por sección independiente
 * - Historial de navegador integrado
 * - Skeleton loading avanzado
 * - Retry automático con backoff
 * - Estados independientes por tabla
 * - Pre-carga inteligente
 */

// ===============================================
// SISTEMA AJAX MEJORADO CON CACHE E INDEPENDENCIA
// ===============================================

const CompletarInformacionMejorado = {
    // Configuración
    config: {
        debounceDelay: 800,
        autoSubmitMinChars: 3,
        messageDisplayTime: 5000,
        animationDelay: 100,
        cacheExpiry: 300000, // 5 minutos
        maxRetries: 3,
        retryDelay: 1000
    },
    
    // Cache independiente por sección
    cache: {
        disenos: new Map(),
        competencias: new Map(),
        raps: new Map()
    },
    
    // Estados independientes por sección
    sectionStates: {
        disenos: {
            currentPage: 1,
            recordsPerPage: 10,
            lastUpdate: null,
            loading: false
        },
        competencias: {
            currentPage: 1,
            recordsPerPage: 10,
            lastUpdate: null,
            loading: false
        },
        raps: {
            currentPage: 1,
            recordsPerPage: 10,
            lastUpdate: null,
            loading: false
        }
    },
    
    // Inicialización
    init: function() {
        console.log('🚀 Inicializando Sistema AJAX Mejorado...');
        this.bindEvents();
        this.setupHistoryNavigation();
        this.restoreFromURL();
        
        // Actualizar links inicialmente con un retraso para asegurar que el DOM está listo
        setTimeout(() => {
            this.updateNavigationLinks();
        }, 500);
    },
    
    // ===============================================
    // CACHE INTELIGENTE POR SECCIÓN
    // ===============================================
    
    /**
     * Generar clave de cache única por sección
     */
    generateCacheKey: function(seccion, params) {
        const filtrosGlobales = this.getGlobalFilters();
        const parametrosSeccion = {
            ...filtrosGlobales,
            ...params,
            seccion: seccion
        };
        
        // Crear hash de parámetros para cache
        return JSON.stringify(parametrosSeccion);
    },
    
    /**
     * Obtener datos del cache si están disponibles y válidos
     */
    getCachedData: function(seccion, params) {
        const cacheKey = this.generateCacheKey(seccion, params);
        const cached = this.cache[seccion].get(cacheKey);
        
        if (cached && (Date.now() - cached.timestamp) < this.config.cacheExpiry) {
            console.log(`📋 Cache hit para ${seccion}:`, cacheKey.substring(0, 50) + '...');
            return cached.data;
        }
        
        return null;
    },
    
    /**
     * Guardar datos en cache
     */
    setCachedData: function(seccion, params, data) {
        const cacheKey = this.generateCacheKey(seccion, params);
        this.cache[seccion].set(cacheKey, {
            data: data,
            timestamp: Date.now()
        });
        
        console.log(`💾 Datos guardados en cache para ${seccion}`);
    },
    
    // ===============================================
    // CARGA AJAX MEJORADA CON RETRY
    // ===============================================
    
    /**
     * Cargar sección con todas las mejoras
     */
    cargarSeccionMejorada: function(seccion, parametrosAdicionales = {}) {
        // Prevenir múltiples cargas simultáneas de la misma sección
        if (this.sectionStates[seccion].loading) {
            console.log(`⏳ ${seccion} ya está cargando, ignorando petición duplicada`);
            return;
        }
        
        // Actualizar estado local ANTES de cargar
        if (parametrosAdicionales[`pagina_${seccion}`]) {
            this.sectionStates[seccion].currentPage = parseInt(parametrosAdicionales[`pagina_${seccion}`]);
        }
        if (parametrosAdicionales[`registros_${seccion}`]) {
            this.sectionStates[seccion].recordsPerPage = parseInt(parametrosAdicionales[`registros_${seccion}`]);
            console.log(`📊 Actualizando ${seccion} a ${this.sectionStates[seccion].recordsPerPage} registros por página`);
        }
        
        // Verificar cache primero
        const cachedData = this.getCachedData(seccion, parametrosAdicionales);
        if (cachedData) {
            this.actualizarSeccionConAnimacion(seccion, cachedData);
            this.updateBrowserHistory(seccion, parametrosAdicionales);
            return;
        }
        
        // Marcar como cargando
        this.sectionStates[seccion].loading = true;
        
        // Mostrar skeleton loading
        this.mostrarSkeletonLoading(seccion);
        
        // Pre-cargar páginas adyacentes en background
        this.preloadAdjacentPages(seccion, parametrosAdicionales);
        
        // Realizar petición con retry
        this.fetchWithRetry(seccion, parametrosAdicionales, 0);
    },
    
    /**
     * Petición AJAX con retry automático
     */
    fetchWithRetry: function(seccion, params, retryCount) {
        const formData = new FormData(document.getElementById('filtrosForm'));
        const urlParams = new URLSearchParams();
        
        // Añadir parámetros del formulario (filtros globales)
        for (let [key, value] of formData.entries()) {
            urlParams.append(key, value);
        }
        
        // Añadir parámetros específicos de la sección
        for (let [key, value] of Object.entries(params)) {
            urlParams.set(key, value);
        }
        
        // Parámetros AJAX
        urlParams.set('accion_ajax', 'actualizar_seccion');
        urlParams.set('seccion', seccion);
        
        const startTime = Date.now();
        
        fetch('ajax.php?' + urlParams.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            const loadTime = Date.now() - startTime;
            console.log(`✅ ${seccion} cargado en ${loadTime}ms`);
            
            if (data.success) {
                // Guardar en cache
                this.setCachedData(seccion, params, data);
                
                // Actualizar sección
                this.actualizarSeccionConAnimacion(seccion, data);
                
                // Actualizar historial
                this.updateBrowserHistory(seccion, params);
                
                // Actualizar estado
                this.sectionStates[seccion].lastUpdate = Date.now();
            } else {
                throw new Error(data.message || 'Error desconocido del servidor');
            }
        })
        .catch(error => {
            console.error(`❌ Error en ${seccion}:`, error);
            
            if (retryCount < this.config.maxRetries) {
                const nextRetry = retryCount + 1;
                const delay = this.config.retryDelay * Math.pow(2, retryCount); // Backoff exponencial
                
                console.log(`🔄 Reintentando ${seccion} (${nextRetry}/${this.config.maxRetries}) en ${delay}ms`);
                
                setTimeout(() => {
                    this.fetchWithRetry(seccion, params, nextRetry);
                }, delay);
            } else {
                this.mostrarErrorInteligente(seccion, error);
            }
        })
        .finally(() => {
            this.sectionStates[seccion].loading = false;
            this.ocultarSkeletonLoading(seccion);
        });
    },
    
    // ===============================================
    // SKELETON LOADING AVANZADO
    // ===============================================
    
    /**
     * Mostrar skeleton loading específico por sección
     */
    mostrarSkeletonLoading: function(seccion) {
        const container = document.querySelector(`#tabla-${seccion}`);
        if (!container) return;
        
        // Crear skeleton basado en la estructura de la tabla
        const numColumns = this.getColumnCount(seccion);
        const numRows = this.sectionStates[seccion].recordsPerPage || 10;
        
        let skeletonHTML = `
            <div class="skeleton-container">
                <table class="skeleton-table">
                    <thead>
                        <tr>
                            ${Array(numColumns).fill('<th><div class="skeleton-header"></div></th>').join('')}
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        for (let i = 0; i < Math.min(numRows, 5); i++) {
            skeletonHTML += `
                <tr>
                    ${Array(numColumns).fill('<td><div class="skeleton-cell"></div></td>').join('')}
                </tr>
            `;
        }
        
        skeletonHTML += `
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = skeletonHTML;
        container.classList.add('skeleton-loading');
    },
    
    /**
     * Ocultar skeleton loading
     */
    ocultarSkeletonLoading: function(seccion) {
        const container = document.querySelector(`#tabla-${seccion}`);
        if (container) {
            container.classList.remove('skeleton-loading');
        }
    },
    
    /**
     * Obtener número de columnas por sección
     */
    getColumnCount: function(seccion) {
        const columnCounts = {
            'disenos': 5,
            'competencias': 5,
            'raps': 6
        };
        return columnCounts[seccion] || 5;
    },
    
    // ===============================================
    // PRE-CARGA INTELIGENTE
    // ===============================================
    
    /**
     * Pre-cargar páginas adyacentes en background
     */
    preloadAdjacentPages: function(seccion, currentParams) {
        const currentPage = parseInt(currentParams[`pagina_${seccion}`] || 1);
        
        // Pre-cargar página siguiente
        if (currentPage > 1) {
            setTimeout(() => {
                const prevParams = { ...currentParams };
                prevParams[`pagina_${seccion}`] = currentPage - 1;
                this.preloadPage(seccion, prevParams);
            }, 500);
        }
        
        // Pre-cargar página anterior
        setTimeout(() => {
            const nextParams = { ...currentParams };
            nextParams[`pagina_${seccion}`] = currentPage + 1;
            this.preloadPage(seccion, nextParams);
        }, 1000);
    },
    
    /**
     * Pre-cargar una página específica
     */
    preloadPage: function(seccion, params) {
        // Solo pre-cargar si no está en cache
        if (!this.getCachedData(seccion, params)) {
            console.log(`🔮 Pre-cargando ${seccion} página ${params[`pagina_${seccion}`]}`);
            
            // Petición silenciosa
            this.fetchWithRetry(seccion, params, 0);
        }
    },
    
    // ===============================================
    // HISTORIAL DE NAVEGADOR
    // ===============================================
    
    /**
     * Configurar navegación por historial
     */
    setupHistoryNavigation: function() {
        window.addEventListener('popstate', (event) => {
            if (event.state && event.state.completarInformacion) {
                console.log('🔙 Navegando por historial:', event.state);
                this.restoreFromState(event.state);
            }
        });
    },
    
    /**
     * Actualizar URL del navegador
     */
    updateBrowserHistory: function(seccion, params) {
        const currentUrl = new URL(window.location);
        
        // Actualizar parámetros de la sección específica
        for (let [key, value] of Object.entries(params)) {
            currentUrl.searchParams.set(key, value);
        }
        
        const state = {
            completarInformacion: true,
            seccion: seccion,
            params: params,
            timestamp: Date.now()
        };
        
        // Actualizar historial sin recargar
        history.pushState(state, '', currentUrl.toString());
    },
    
    /**
     * Restaurar estado desde URL
     */
    restoreFromURL: function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Restaurar estados de cada sección desde URL
        ['disenos', 'competencias', 'raps'].forEach(seccion => {
            const pagina = urlParams.get(`pagina_${seccion}`);
            const registros = urlParams.get(`registros_${seccion}`);
            
            if (pagina || registros) {
                this.sectionStates[seccion].currentPage = parseInt(pagina) || 1;
                this.sectionStates[seccion].recordsPerPage = parseInt(registros) || 10;
            }
        });
    },
    
    /**
     * Restaurar desde estado del historial
     */
    restoreFromState: function(state) {
        if (state.params) {
            this.cargarSeccionMejorada(state.seccion, state.params);
        }
    },
    
    // ===============================================
    // UTILIDADES
    // ===============================================
    
    /**
     * Obtener filtros globales del formulario
     */
    getGlobalFilters: function() {
        const form = document.getElementById('filtrosForm');
        if (!form) return {};
        
        const formData = new FormData(form);
        const filters = {};
        
        for (let [key, value] of formData.entries()) {
            if (value && !key.startsWith('pagina_') && !key.startsWith('registros_')) {
                filters[key] = value;
            }
        }
        
        return filters;
    },
    
    /**
     * Actualizar sección con animaciones
     */
    actualizarSeccionConAnimacion: function(seccion, data) {
        const container = document.querySelector(`#tabla-${seccion}`);
        if (!container) return;
        
        // Animación de salida
        container.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
        container.style.opacity = '0.3';
        container.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            // Actualizar contenido
            if (data.tabla_html) {
                container.innerHTML = data.tabla_html;
            }
            
            // Actualizar paginación
            const paginacionContainer = document.querySelector(`#paginacion-${seccion}`);
            if (paginacionContainer && data.paginacion_html) {
                paginacionContainer.innerHTML = data.paginacion_html;
            }
            
            // Animación de entrada
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
            
            // Re-vincular eventos
            setTimeout(() => {
                this.bindDynamicEvents();
                // Actualizar links de navegación para preservar filtros
                this.updateNavigationLinks();
            }, 100);
            
        }, 100);
    },
    
    /**
     * Mostrar error inteligente con opciones
     */
    mostrarErrorInteligente: function(seccion, error) {
        const container = document.querySelector(`#seccion-${seccion}`);
        if (!container) return;
        
        const errorHTML = `
            <div class="error-inteligente">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="error-content">
                    <h4>Error al cargar ${seccion}</h4>
                    <p>${error.message}</p>
                    <div class="error-actions">
                        <button onclick="CompletarInformacionMejorado.retrySection('${seccion}')" class="btn-retry">
                            <i class="fas fa-redo"></i> Reintentar
                        </button>
                        <button onclick="window.location.reload()" class="btn-reload">
                            <i class="fas fa-refresh"></i> Recargar página
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const tableContainer = container.querySelector(`#tabla-${seccion}`);
        if (tableContainer) {
            tableContainer.innerHTML = errorHTML;
        }
    },
    
    /**
     * Reintentar carga de una sección específica
     */
    retrySection: function(seccion) {
        const params = {};
        params[`pagina_${seccion}`] = this.sectionStates[seccion].currentPage;
        params[`registros_${seccion}`] = this.sectionStates[seccion].recordsPerPage;
        
        this.cargarSeccionMejorada(seccion, params);
    },
    
    /**
     * Actualizar links para preservar estado de filtros
     */
    updateNavigationLinks: function() {
        const currentFilters = this.getGlobalFilters();
        const currentStates = this.getCurrentSectionStates();
        
        // Crear URL con todos los filtros y estados actuales
        const params = new URLSearchParams();
        
        // Añadir filtros globales
        Object.entries(currentFilters).forEach(([key, value]) => {
            if (value && value !== '') {
                params.set(key, value);
            }
        });
        
        // Añadir estados de secciones
        Object.entries(currentStates).forEach(([seccion, state]) => {
            if (state.currentPage > 1) {
                params.set(`pagina_${seccion}`, state.currentPage);
            }
            if (state.recordsPerPage !== 10) {
                params.set(`registros_${seccion}`, state.recordsPerPage);
            }
        });
        
        const queryString = params.toString();
        
        // Actualizar todos los links de navegación
        document.querySelectorAll('a[href*="completar_informacion"]').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.includes('completar&')) {
                const separator = href.includes('?') ? '&' : '?';
                const newHref = queryString ? `${href}${separator}${queryString}` : href;
                link.setAttribute('href', newHref);
                console.log('🔗 Actualizado link de navegación:', newHref);
            }
        });
    },
    
    /**
     * Obtener estados actuales de todas las secciones
     */
    getCurrentSectionStates: function() {
        return {
            disenos: { ...this.sectionStates.disenos },
            competencias: { ...this.sectionStates.competencias },
            raps: { ...this.sectionStates.raps }
        };
    },

    /**
     * Vincular eventos dinámicos
     */
    bindEvents: function() {
        // Event delegation para paginación AJAX
        document.addEventListener('click', (e) => {
            const ajaxBtn = e.target.closest('a[data-seccion][data-pagina]');
            if (ajaxBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const seccion = ajaxBtn.getAttribute('data-seccion');
                const pagina = ajaxBtn.getAttribute('data-pagina');
                
                console.log(`📄 Cargando ${seccion} página ${pagina}`);
                
                const params = {};
                params[`pagina_${seccion}`] = pagina;
                
                this.cargarSeccionMejorada(seccion, params);
                return false;
            }
        });
        
        // Event delegation para cambio de registros por página
        document.addEventListener('change', (e) => {
            const selector = e.target.closest('select[data-seccion].ajax-records-selector');
            if (selector) {
                e.preventDefault();
                
                const seccion = selector.getAttribute('data-seccion');
                const registros = selector.value;
                
                console.log(`📊 Cambiando ${seccion} a ${registros} registros por página`);
                
                const params = {};
                params[`registros_${seccion}`] = registros;
                params[`pagina_${seccion}`] = 1; // Reset a página 1
                
                this.cargarSeccionMejorada(seccion, params);
            }
        });
    },
    
    /**
     * Re-vincular eventos en elementos dinámicos
     */
    bindDynamicEvents: function() {
        // Re-aplicar estilos a elementos recién creados
        document.querySelectorAll('.btn-edit:not([data-enhanced])').forEach(btn => {
            btn.setAttribute('data-enhanced', 'true');
            btn.style.transition = 'all 0.3s ease';
        });
        
        // Re-aplicar animaciones a filas
        document.querySelectorAll('table tbody tr:not([data-animated])').forEach((row, index) => {
            row.setAttribute('data-animated', 'true');
            row.style.animation = `fadeInUp 0.3s ease ${index * 0.05}s both`;
        });
    }
};

// ===============================================
// INICIALIZACIÓN AUTOMÁTICA
// ===============================================

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Mantener compatibilidad con el sistema anterior
    if (typeof CompletarInformacion !== 'undefined') {
        console.log('🔄 Migrando al sistema mejorado...');
    }
    
    CompletarInformacionMejorado.init();
    
    console.log('✅ Sistema AJAX Mejorado inicializado correctamente');
});

// Exponer para compatibilidad
window.CompletarInformacion = CompletarInformacionMejorado;
