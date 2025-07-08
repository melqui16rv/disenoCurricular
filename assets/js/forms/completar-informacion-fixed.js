/**
 * JavaScript para la funcionalidad avanzada de completar información
 * Sistema de Gestión de Diseños Curriculares SENA
 * 
 * Características:
 * - Filtros avanzados dinámicos
 * - Paginación inteligente por sección
 * - Estados independientes por tabla
 * - Validación en tiempo real
 * - Experiencia de usuario mejorada
 */

// ===============================================
// VARIABLES GLOBALES Y CONFIGURACIÓN
// ===============================================

const CompletarInformacion = {
    // Configuración
    config: {
        debounceDelay: 800,
        autoSubmitMinChars: 3,
        messageDisplayTime: 5000,
        animationDelay: 100
    },
    
    // Estados por sección (independientes)
    sectionStates: {
        disenos: {
            currentPage: 1,
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
    }
};

// ===============================================
// INICIALIZACIÓN AUTOMÁTICA
// ===============================================

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (typeof CompletarInformacion !== 'undefined') {
        CompletarInformacion.init();
    }
});
