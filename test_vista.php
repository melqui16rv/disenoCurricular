<?php
/**
 * Script de prueba para la vista completar_informacion
 */

// Simular entorno web
$_SERVER['HTTP_HOST'] = 'localhost:8888';
$_SERVER['REQUEST_URI'] = '/app/forms/index.php?accion=completar_informacion';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

// Simular parámetros GET
$_GET['accion'] = 'completar_informacion';

echo "=== PRUEBA DE VISTA COMPLETAR INFORMACION ===\n";

try {
    // Incluir configuración
    include_once __DIR__ . '/conf/config.php';
    echo "[OK] Configuracion cargada\n";
    
    // Incluir métodos
    include_once __DIR__ . '/math/forms/metodosDisenos.php';
    echo "[OK] MetodosDisenos cargado\n";
    
    // Crear instancia
    $metodos = new MetodosDisenos();
    echo "[OK] Instancia de MetodosDisenos creada\n";
    
    // Simular inclusión de la vista como lo hace index.php
    echo "[INFO] Incluyendo vista completar_informacion_new.php...\n";
    
    // Capturar la salida
    ob_start();
    include __DIR__ . '/app/forms/vistas/completar_informacion_new.php';
    $output = ob_get_clean();
    
    echo "[OK] Vista incluida exitosamente\n";
    echo "[INFO] Tamaño de salida HTML: " . strlen($output) . " bytes\n";
    
    // Verificar que la vista generó contenido
    if (strlen($output) > 1000) {
        echo "[OK] La vista genero contenido HTML\n";
        
        // Verificar elementos clave
        if (strpos($output, 'completar-informacion-container') !== false) {
            echo "[OK] Contenedor principal encontrado\n";
        }
        
        if (strpos($output, 'statistics-panel') !== false) {
            echo "[OK] Panel de estadisticas encontrado\n";
        }
        
        if (strpos($output, 'filters-section') !== false) {
            echo "[OK] Seccion de filtros encontrada\n";
        }
        
        if (strpos($output, 'results-section') !== false) {
            echo "[OK] Seccion de resultados encontrada\n";
        }
        
        // Contar registros en cada sección
        $disenos_count = substr_count($output, 'diseñosConFaltantes');
        $competencias_count = substr_count($output, 'competenciasConFaltantes');
        $raps_count = substr_count($output, 'rapsConFaltantes');
        
        echo "[INFO] Referencias a disenos: $disenos_count\n";
        echo "[INFO] Referencias a competencias: $competencias_count\n";
        echo "[INFO] Referencias a RAPs: $raps_count\n";
        
    } else {
        echo "[WARN] La vista genero poco contenido (posible error)\n";
    }
    
    // Verificar errores PHP
    $errors = error_get_last();
    if ($errors && $errors['message']) {
        echo "[ERROR] Ultimo error PHP: " . $errors['message'] . "\n";
    } else {
        echo "[OK] No hay errores PHP\n";
    }
    
} catch (Exception $e) {
    echo "[ERROR] Exception: " . $e->getMessage() . "\n";
    echo "[ERROR] Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
?>
