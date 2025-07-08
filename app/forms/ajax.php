<?php
/**
 * Controlador AJAX para actualizaciones dinámicas por secciones
 * Mantiene la misma lógica de rutas que index.php
 * Sistema de Gestión de Diseños Curriculares SENA
 */

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Headers para AJAX
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Verificar que sea una petición AJAX (opcional pero recomendado)
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solo se permiten peticiones AJAX']);
    exit;
}

// Incluir dependencias (mismo patrón que index.php)
$ruta_config = $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $ruta_config;
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosDisenos.php';

// Inicializar métodos (mismo patrón que index.php)
$metodos = new MetodosDisenos();

try {
    // Obtener acción AJAX
    $accion_ajax = $_GET['accion_ajax'] ?? '';
    $seccion = $_GET['seccion'] ?? '';
    
    // Validar parámetros requeridos
    if (empty($accion_ajax)) {
        throw new Exception('Acción AJAX no especificada');
    }
    
    // Manejar acciones AJAX (similar al switch en index.php)
    switch ($accion_ajax) {
        case 'actualizar_seccion':
            if (empty($seccion)) {
                throw new Exception('Sección no especificada');
            }
            
            // Validar secciones permitidas
            $secciones_validas = ['disenos', 'competencias', 'raps'];
            if (!in_array($seccion, $secciones_validas)) {
                throw new Exception('Sección no válida');
            }
            
            // Obtener filtros (mismo patrón que completar_informacion_new.php)
            $filtros_array = [
                'accion' => 'completar_informacion',
                'seccion' => $_GET['filtro_seccion'] ?? 'todas',
                'busqueda' => $_GET['busqueda'] ?? '',
                'horas_min' => (float)($_GET['horas_min'] ?? 0),
                'horas_max' => (float)($_GET['horas_max'] ?? 0),
                'tipo_programa' => $_GET['tipo_programa'] ?? '',
                'nivel_academico' => $_GET['nivel_academico'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'fecha_desde' => $_GET['fecha_desde'] ?? '',
                'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
            ];
            
            // Obtener parámetros de paginación específicos por sección
            $pagina = max(1, (int)($_GET["pagina_{$seccion}"] ?? 1));
            $registros_param = $_GET["registros_{$seccion}"] ?? 10;
            
            // Manejar opción "Todos" (-1)
            if ($registros_param == -1) {
                $registros = -1; // Señal para mostrar todos
            } else {
                $registros = max(5, min(100, (int)$registros_param));
            }
            
            // LOGGING DETALLADO PARA DEBUG
            error_log("🔍 AJAX DEBUG - Sección: $seccion");
            error_log("🔍 AJAX DEBUG - Página solicitada: $pagina");
            error_log("🔍 AJAX DEBUG - Registros solicitados: $registros");
            error_log("🔍 AJAX DEBUG - Parámetros GET: " . json_encode($_GET));
            
            // Validar registros por página (excepto para "Todos")
            if ($registros != -1) {
                $registros_permitidos = [5, 10, 25, 50, 100];
                if (!in_array($registros, $registros_permitidos)) {
                    $registros = 10;
                    error_log("⚠️ AJAX DEBUG - Registros corregidos a: $registros");
                }
            }
            
            // Incluir funciones necesarias de la vista (mismo patrón)
            include_once 'vistas/completar_informacion_funciones.php';
            
            $resultado = [];
            
            // Obtener datos según la sección (mismo patrón que en la vista)
            switch ($seccion) {
                case 'disenos':
                    $resultado = obtenerDisenosConCamposFaltantes($metodos, $filtros_array, $pagina, $registros);
                    break;
                case 'competencias':
                    $resultado = obtenerCompetenciasConCamposFaltantes($metodos, $filtros_array, $pagina, $registros);
                    break;
                case 'raps':
                    $resultado = obtenerRapsConCamposFaltantes($metodos, $filtros_array, $pagina, $registros);
                    break;
            }
            
            // LOGGING DE RESULTADO
            error_log("📊 AJAX DEBUG - Total registros: " . $resultado['total_registros']);
            error_log("📊 AJAX DEBUG - Total páginas: " . $resultado['total_paginas']);
            error_log("📊 AJAX DEBUG - Página actual: " . $resultado['pagina_actual']);
            error_log("📊 AJAX DEBUG - Registros por página: " . $resultado['registros_por_pagina']);
            error_log("📊 AJAX DEBUG - Registros devueltos: " . count($resultado['datos']));
            
            // Generar HTML usando funciones existentes
            $tabla_html = generarTablaSeccion($seccion, $resultado['datos']);
            $paginacion_html = generarPaginacion($resultado, $filtros_array, $seccion);
            
            // Respuesta exitosa
            echo json_encode([
                'success' => true,
                'seccion' => $seccion,
                'tabla_html' => $tabla_html,
                'paginacion_html' => $paginacion_html,
                'total_registros' => $resultado['total_registros'],
                'pagina_actual' => $resultado['pagina_actual'],
                'total_paginas' => $resultado['total_paginas'],
                'registros_por_pagina' => $resultado['registros_por_pagina']
            ]);
            break;
            
        case 'obtener_estadisticas':
            // Obtener estadísticas generales sin paginación
            $filtros_array = [
                'accion' => 'completar_informacion',
                'seccion' => $_GET['filtro_seccion'] ?? 'todas',
                'busqueda' => $_GET['busqueda'] ?? '',
                'horas_min' => (float)($_GET['horas_min'] ?? 0),
                'horas_max' => (float)($_GET['horas_max'] ?? 0),
                'tipo_programa' => $_GET['tipo_programa'] ?? '',
                'nivel_academico' => $_GET['nivel_academico'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'fecha_desde' => $_GET['fecha_desde'] ?? '',
                'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
            ];
            
            // Incluir funciones necesarias
            include_once 'vistas/completar_informacion_funciones.php';
            
            // Obtener totales sin paginación
            $resultadoDiseños = obtenerDisenosConCamposFaltantes($metodos, $filtros_array, 1, 999999);
            $resultadoCompetencias = obtenerCompetenciasConCamposFaltantes($metodos, $filtros_array, 1, 999999);
            $resultadoRaps = obtenerRapsConCamposFaltantes($metodos, $filtros_array, 1, 999999);
            
            $estadisticas = [
                'total_disenos' => $resultadoDiseños['total_registros'],
                'total_competencias' => $resultadoCompetencias['total_registros'],
                'total_raps' => $resultadoRaps['total_registros'],
                'total_general' => $resultadoDiseños['total_registros'] + $resultadoCompetencias['total_registros'] + $resultadoRaps['total_registros']
            ];
            
            echo json_encode([
                'success' => true,
                'estadisticas' => $estadisticas
            ]);
            break;
            
        default:
            throw new Exception('Acción AJAX no reconocida: ' . $accion_ajax);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => 'server_error'
    ]);
}
?>
