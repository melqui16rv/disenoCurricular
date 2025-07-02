<?php
// Controlador AJAX para operaciones del sistema de diseños curriculares

// Iniciar buffer de salida para capturar cualquier error antes del JSON
ob_start();

// Configurar PHP para no mostrar errores en el output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(0); // Desactivar completamente para evitar output HTML

// Configurar headers apropiados
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Función para enviar respuesta JSON y terminar
function sendJsonResponse($response) {
    ob_clean();
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

try {
    // Determinar la ruta base correcta
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $basePath = dirname(dirname(dirname(__FILE__))); // 3 niveles arriba desde control/ajax.php
    
    // Incluir archivos necesarios con rutas absolutas
    $configPath = $basePath . '/conf/config.php';
    $metodosPath = $basePath . '/math/forms/metodosDisenos.php';
    $conexionPath = $basePath . '/sql/conexion.php';
    
    if (!file_exists($configPath)) {
        throw new Exception("No se encuentra el archivo de configuración en: $configPath");
    }
    if (!file_exists($metodosPath)) {
        throw new Exception("No se encuentra el archivo de métodos en: $metodosPath");
    }
    if (!file_exists($conexionPath)) {
        throw new Exception("No se encuentra el archivo de conexión en: $conexionPath");
    }
    
    require_once $configPath;
    require_once $metodosPath;
    require_once $conexionPath;
    
} catch (Exception $e) {
    sendJsonResponse([
        'success' => false, 
        'message' => 'Error de configuración: ' . $e->getMessage(),
        'error_type' => 'config_error',
        'debug_info' => [
            'document_root' => $_SERVER['DOCUMENT_ROOT'],
            'script_path' => __FILE__,
            'base_path' => $basePath ?? 'undefined'
        ]
    ]);
}

$metodos = new MetodosDisenos();
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    $accion = $_GET['accion'] ?? $_POST['accion'] ?? '';
    
    if (empty($accion)) {
        sendJsonResponse([
            'success' => false,
            'message' => 'No se especificó una acción válida',
            'error_type' => 'missing_action'
        ]);
    }
    
    switch ($accion) {
        case 'validar_codigo_diseño':
            $codigoPrograma = $_GET['codigoPrograma'] ?? '';
            $versionPrograma = $_GET['versionPrograma'] ?? '';
            
            if ($codigoPrograma && $versionPrograma) {
                $codigoDiseño = $codigoPrograma . '-' . $versionPrograma;
                $diseño = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
                
                if ($diseño) {
                    $response['success'] = false;
                    $response['message'] = 'Ya existe un diseño con este código y versión';
                } else {
                    $response['success'] = true;
                    $response['message'] = 'Código disponible';
                    $response['data'] = ['codigoDiseño' => $codigoDiseño];
                }
            }
            break;
            
        case 'validar_codigo_competencia':
            $codigoDiseño = $_GET['codigoDiseño'] ?? '';
            $codigoCompetencia = $_GET['codigoCompetencia'] ?? '';
            
            if ($codigoDiseño && $codigoCompetencia) {
                $codigoDiseñoCompetenciaReporte = $codigoDiseño . '-' . $codigoCompetencia;
                $competencia = $metodos->obtenerCompetenciaPorCodigo($codigoDiseñoCompetenciaReporte);
                
                if ($competencia) {
                    $response['success'] = false;
                    $response['message'] = 'Ya existe una competencia con este código';
                } else {
                    $response['success'] = true;
                    $response['message'] = 'Código disponible';
                    $response['data'] = ['codigoDiseñoCompetenciaReporte' => $codigoDiseñoCompetenciaReporte];
                }
            }
            break;
            
        case 'validar_codigo_rap':
            $codigoDiseñoCompetenciaReporte = $_GET['codigoDiseñoCompetenciaReporte'] ?? '';
            $codigoRap = $_GET['codigoRap'] ?? '';
            
            if ($codigoDiseñoCompetenciaReporte && $codigoRap) {
                $codigoDiseñoCompetenciaReporteRap = $codigoDiseñoCompetenciaReporte . '-' . $codigoRap;
                $rap = $metodos->obtenerRapPorCodigo($codigoDiseñoCompetenciaReporteRap);
                
                if ($rap) {
                    $response['success'] = false;
                    $response['message'] = 'Ya existe un RAP con este código';
                } else {
                    $response['success'] = true;
                    $response['message'] = 'Código disponible';
                    $response['data'] = ['codigoDiseñoCompetenciaReporteRap' => $codigoDiseñoCompetenciaReporteRap];
                }
            }
            break;
            
        case 'obtener_estadisticas':
            $diseños = $metodos->obtenerTodosLosDiseños();
            $totalDiseños = count($diseños);
            $totalHoras = array_sum(array_column($diseños, 'horasDesarrolloDiseño'));
            $totalMeses = array_sum(array_column($diseños, 'mesesDesarrolloDiseño'));
            
            $response['success'] = true;
            $response['data'] = [
                'totalDiseños' => $totalDiseños,
                'totalHoras' => $totalHoras,
                'totalMeses' => $totalMeses,
                'promedioHoras' => $totalDiseños > 0 ? $totalHoras / $totalDiseños : 0,
                'promedioMeses' => $totalDiseños > 0 ? $totalMeses / $totalDiseños : 0
            ];
            break;
            
        case 'obtener_competencias_diseño':
            $codigoDiseño = $_GET['codigoDiseño'] ?? '';
            if ($codigoDiseño) {
                $competencias = $metodos->obtenerCompetenciasPorDiseño($codigoDiseño);
                $response['success'] = true;
                $response['data'] = $competencias;
            }
            break;
            
        case 'obtener_raps_competencia':
            $codigoDiseñoCompetenciaReporte = $_GET['codigoDiseñoCompetenciaReporte'] ?? '';
            if ($codigoDiseñoCompetenciaReporte) {
                $raps = $metodos->obtenerRapsPorCompetencia($codigoDiseñoCompetenciaReporte);
                $response['success'] = true;
                $response['data'] = $raps;
            }
            break;

        case 'obtener_comparacion_raps':
            $codigoCompetencia = $_POST['codigoCompetencia'] ?? $_GET['codigoCompetencia'] ?? '';
            $disenoActual = $_POST['disenoActual'] ?? $_GET['disenoActual'] ?? '';
            
            if (empty($codigoCompetencia)) {
                sendJsonResponse([
                    'success' => false,
                    'message' => 'Código de competencia requerido',
                    'error_type' => 'missing_parameter'
                ]);
            }
            
            try {
                // Obtener conexión usando la clase ya incluida
                $conexionObj = new Conexion();
                $conexion = $conexionObj->obtenerConexion();
                
                if (!$conexion) {
                    throw new Exception("No se pudo establecer conexión a la base de datos");
                }
                
                // Simplificar la consulta para evitar problemas
                $sql = "SELECT DISTINCT 
                            d.codigoDiseño,
                            d.nombrePrograma,
                            d.versionPrograma,
                            d.codigoPrograma,
                            c.codigoDiseñoCompetenciaReporte,
                            c.nombreCompetencia,
                            c.horasDesarrolloCompetencia
                        FROM competencias c
                        INNER JOIN diseños d ON (
                            d.codigoDiseño = SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2)
                        )
                        WHERE c.codigoCompetencia = ?";
                
                $params = [$codigoCompetencia];
                
                // Excluir el diseño actual si se proporciona
                if (!empty($disenoActual) && trim($disenoActual) !== '') {
                    $sql .= " AND d.codigoDiseño != ?";
                    $params[] = $disenoActual;
                }
                
                $sql .= " ORDER BY d.nombrePrograma, d.versionPrograma";
                
                $stmt = $conexion->prepare($sql);
                $stmt->execute($params);
                $disenosConMismaCompetencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $comparacion = [];
                
                // Para cada diseño, obtener sus RAPs
                foreach ($disenosConMismaCompetencia as $diseno) {
                    // Buscar RAPs que coincidan con el patrón del diseño-competencia
                    $sqlRaps = "SELECT 
                                    codigoDiseñoCompetenciaReporteRap,
                                    codigoRapAutomatico,
                                    codigoRapDiseño,
                                    nombreRap,
                                    horasDesarrolloRap
                                FROM raps 
                                WHERE codigoDiseñoCompetenciaReporteRap LIKE ?
                                ORDER BY codigoRapAutomatico";
                    
                    $stmtRaps = $conexion->prepare($sqlRaps);
                    $patronCompetencia = $diseno['codigoDiseñoCompetenciaReporte'] . '-%';
                    $stmtRaps->execute([$patronCompetencia]);
                    
                    $raps = $stmtRaps->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Siempre agregar el diseño, incluso si no tiene RAPs
                    $comparacion[] = [
                        'diseno' => $diseno,
                        'raps' => $raps,
                        'totalRaps' => count($raps),
                        'totalHorasRaps' => array_sum(array_column($raps, 'horasDesarrolloRap'))
                    ];
                }
                
                sendJsonResponse([
                    'success' => true,
                    'data' => $comparacion,
                    'message' => 'Comparación obtenida exitosamente',
                    'totalDisenos' => count($comparacion),
                    'debug' => [
                        'codigoCompetencia' => $codigoCompetencia,
                        'disenoActual' => $disenoActual,
                        'totalDisenosEncontrados' => count($disenosConMismaCompetencia),
                        'totalComparaciones' => count($comparacion)
                    ]
                ]);
                
            } catch (PDOException $e) {
                error_log("Error PDO en obtener_comparacion_raps: " . $e->getMessage());
                sendJsonResponse([
                    'success' => false,
                    'message' => 'Error en la base de datos',
                    'error_type' => 'database_error',
                    'error_details' => $e->getMessage()
                ]);
            } catch (Exception $e) {
                error_log("Error general en obtener_comparacion_raps: " . $e->getMessage());
                sendJsonResponse([
                    'success' => false,
                    'message' => 'Error interno del servidor',
                    'error_type' => 'general_error',
                    'error_details' => $e->getMessage()
                ]);
            }
            break;
            
        default:
            sendJsonResponse([
                'success' => false,
                'message' => 'Acción no válida: ' . $accion,
                'error_type' => 'invalid_action'
            ]);
    }
    
} catch (Exception $e) {
    error_log("Error general en AJAX: " . $e->getMessage());
    sendJsonResponse([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error_type' => 'server_error',
        'error_details' => $e->getMessage()
    ]);
}

// Si llegamos aquí, enviamos la respuesta normal
sendJsonResponse($response);
?>
