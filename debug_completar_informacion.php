<?php
// Archivo de debug para verificar la funcionalidad

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debug de Completar Información</h2>";

// Verificar configuración
$ruta_config = $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
echo "<p><strong>Ruta de config:</strong> " . $ruta_config . "</p>";
echo "<p><strong>Archivo existe:</strong> " . (file_exists($ruta_config) ? 'SÍ' : 'NO') . "</p>";

if (file_exists($ruta_config)) {
    require_once $ruta_config;
    echo "<p><strong>Config cargado:</strong> SÍ</p>";
    
    // Verificar métodos
    $ruta_metodos = $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosDisenos.php';
    echo "<p><strong>Ruta de métodos:</strong> " . $ruta_metodos . "</p>";
    echo "<p><strong>Archivo métodos existe:</strong> " . (file_exists($ruta_metodos) ? 'SÍ' : 'NO') . "</p>";
    
    if (file_exists($ruta_metodos)) {
        require_once $ruta_metodos;
        echo "<p><strong>Métodos cargados:</strong> SÍ</p>";
        
        try {
            $metodos = new MetodosDisenos();
            echo "<p><strong>Instancia de métodos creada:</strong> SÍ</p>";
            
            // Probar consulta simple
            $sql = "SELECT COUNT(*) as total FROM diseños LIMIT 1";
            try {
                $resultado = $metodos->ejecutarConsulta($sql, []);
                echo "<p><strong>Conexión a BD:</strong> SÍ</p>";
                echo "<p><strong>Total diseños en BD:</strong> " . ($resultado[0]['total'] ?? 'No disponible') . "</p>";
            } catch (Exception $e) {
                echo "<p><strong>Error en BD:</strong> " . $e->getMessage() . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p><strong>Error creando métodos:</strong> " . $e->getMessage() . "</p>";
        }
    }
}

// Verificar archivos de vista
$vista_original = $_SERVER['DOCUMENT_ROOT'] . '/app/forms/vistas/completar_informacion.php';
$vista_nueva = $_SERVER['DOCUMENT_ROOT'] . '/app/forms/vistas/completar_informacion_new.php';

echo "<p><strong>Vista original existe:</strong> " . (file_exists($vista_original) ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>Vista nueva existe:</strong> " . (file_exists($vista_nueva) ? 'SÍ' : 'NO') . "</p>";

// Verificar archivos CSS y JS
$css_principal = $_SERVER['DOCUMENT_ROOT'] . '/assets/css/forms/estilosPrincipales.css';
$js_completar = $_SERVER['DOCUMENT_ROOT'] . '/assets/js/forms/completar-informacion.js';

echo "<p><strong>CSS principal existe:</strong> " . (file_exists($css_principal) ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>JS completar existe:</strong> " . (file_exists($js_completar) ? 'SÍ' : 'NO') . "</p>";

// Simular datos de prueba si hay métodos
if (isset($metodos)) {
    echo "<hr><h3>Prueba de funciones:</h3>";
    
    // Simular parámetros
    $filtros_array = [
        'accion' => 'completar_informacion',
        'seccion' => 'todas',
        'busqueda' => '',
        'registros_por_pagina' => 10,
        'horas_min' => 0,
        'horas_max' => 0,
        'tipo_programa' => '',
        'nivel_academico' => '',
        'estado' => '',
        'fecha_desde' => '',
        'fecha_hasta' => ''
    ];
    
    // Incluir las funciones de la vista
    try {
        include 'vistas/completar_informacion_new.php';
        echo "<p><strong>Vista incluida:</strong> SÍ</p>";
    } catch (Exception $e) {
        echo "<p><strong>Error incluyendo vista:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p><strong>No se puede probar sin métodos</strong></p>";
}
?>
