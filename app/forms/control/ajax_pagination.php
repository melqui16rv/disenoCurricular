<?php
/**
 * AJAX handler para paginación sin recarga de página
 * Implementa carga parcial de resultados manteniendo filtros y estado de tabla
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Verificar que es una petición AJAX (o permitir para pruebas)
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
$is_test = isset($_GET['test']) && $_GET['test'] === '1';

if (!$is_ajax && !$is_test) {
    http_response_code(400);
    echo json_encode(['error' => 'Petición inválida']);
    exit;
}

// Incluir archivos necesarios
require_once dirname(dirname(dirname(__DIR__))) . '/conf/config.php';
require_once dirname(dirname(dirname(__DIR__))) . '/sql/conexion.php';
require_once dirname(dirname(dirname(__DIR__))) . '/math/forms/metodosDisenos.php';
require_once dirname(__DIR__) . '/vistas/completar_informacion_funciones.php';

try {
    // Instanciar método de diseños
    $metodos = new MetodosDisenos();
    
    // Obtener parámetros
    $seccion = $_GET['seccion'] ?? '';
    $pagina = max(1, intval($_GET['pagina'] ?? 1));
    $registros_por_pagina = max(5, min(100, intval($_GET['registros'] ?? 10)));
    
    // Obtener filtros de búsqueda
    $filtro_busqueda = $_GET['search'] ?? '';
    $filtro_estado = $_GET['estado'] ?? '';
    
    // Validar sección
    $secciones_validas = ['disenos', 'competencias', 'raps'];
    if (!in_array($seccion, $secciones_validas)) {
        throw new Exception('Sección inválida');
    }
    
    // Construir array de filtros (misma estructura que el archivo principal)
    $filtros_array = [
        'accion' => 'completar_informacion',
        'seccion' => $seccion,
        'busqueda' => $filtro_busqueda,
        'estado' => $filtro_estado,
        "pagina_{$seccion}" => $pagina,
        "registros_{$seccion}" => $registros_por_pagina
    ];
    
    // Obtener datos según la sección usando las mismas funciones
    $resultado = [];
    
    switch ($seccion) {
        case 'disenos':
            $resultado = obtenerDisenosConCamposFaltantes($metodos, $filtros_array, $pagina, $registros_por_pagina);
            break;
        case 'competencias':
            $resultado = obtenerCompetenciasConCamposFaltantes($metodos, $filtros_array, $pagina, $registros_por_pagina);
            break;
        case 'raps':
            $resultado = obtenerRapsConCamposFaltantes($metodos, $filtros_array, $pagina, $registros_por_pagina);
            break;
    }
    
    // Generar HTML de la tabla usando la misma función que la vista principal
    $html_tabla = generarTablaSeccion($seccion, $resultado['datos']);
    
    // Generar HTML de paginación usando la misma función que la vista principal
    $html_paginacion = generarPaginacion($resultado, $filtros_array, $seccion);
    
    // Respuesta JSON
    echo json_encode([
        'success' => true,
        'seccion' => $seccion,
        'pagina' => $pagina,
        'total_paginas' => $resultado['total_paginas'],
        'total_registros' => $resultado['total_registros'],
        'registros_mostrados' => count($resultado['datos']),
        'html_tabla' => $html_tabla,
        'html_paginacion' => $html_paginacion
    ]);
    
} catch (Exception $e) {
    error_log("Error en AJAX pagination: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
?>
