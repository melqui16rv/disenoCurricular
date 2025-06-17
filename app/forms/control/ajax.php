<?php
// Controlador AJAX para operaciones del sistema de diseños curriculares

// Iniciar buffer de salida para capturar cualquier error antes del JSON
ob_start();

ini_set('display_errors', 0); // Desactivar errores en producción
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Incluir configuración primero
    require_once $_SERVER['DOCUMENT_ROOT'] . '/disenoCurricular/conf/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosDisenos.php';
} catch (Exception $e) {
    // Limpiar buffer y enviar error JSON
    ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Error de configuración: ' . $e->getMessage(),
        'error_type' => 'config_error'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$metodos = new MetodosDisenos();
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    $accion = $_GET['accion'] ?? $_POST['accion'] ?? '';
    
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
                $codigoDiseñoCompetencia = $codigoDiseño . '-' . $codigoCompetencia;
                $competencia = $metodos->obtenerCompetenciaPorCodigo($codigoDiseñoCompetencia);
                
                if ($competencia) {
                    $response['success'] = false;
                    $response['message'] = 'Ya existe una competencia con este código';
                } else {
                    $response['success'] = true;
                    $response['message'] = 'Código disponible';
                    $response['data'] = ['codigoDiseñoCompetencia' => $codigoDiseñoCompetencia];
                }
            }
            break;
            
        case 'validar_codigo_rap':
            $codigoDiseñoCompetencia = $_GET['codigoDiseñoCompetencia'] ?? '';
            $codigoRap = $_GET['codigoRap'] ?? '';
            
            if ($codigoDiseñoCompetencia && $codigoRap) {
                $codigoDiseñoCompetenciaRap = $codigoDiseñoCompetencia . '-' . $codigoRap;
                $rap = $metodos->obtenerRapPorCodigo($codigoDiseñoCompetenciaRap);
                
                if ($rap) {
                    $response['success'] = false;
                    $response['message'] = 'Ya existe un RAP con este código';
                } else {
                    $response['success'] = true;
                    $response['message'] = 'Código disponible';
                    $response['data'] = ['codigoDiseñoCompetenciaRap' => $codigoDiseñoCompetenciaRap];
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
            $codigoDiseñoCompetencia = $_GET['codigoDiseñoCompetencia'] ?? '';
            if ($codigoDiseñoCompetencia) {
                $raps = $metodos->obtenerRapsPorCompetencia($codigoDiseñoCompetencia);
                $response['success'] = true;
                $response['data'] = $raps;
            }
            break;

        case 'obtener_comparacion_raps':
            $codigoCompetencia = $_POST['codigoCompetencia'] ?? '';
            $disenoActual = $_POST['disenoActual'] ?? '';
            
            if (!$codigoCompetencia) {
                $response['message'] = 'Código de competencia requerido';
                break;
            }
            
            try {
                // Obtener conexión a la base de datos usando la configuración
                require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';
                $conexionObj = new Conexion();
                $conexion = $conexionObj->obtenerConexion();
                
                // Obtener diseños curriculares que tienen la misma competencia
                // Simplificamos la consulta para que sea más robusta
                $sql = "SELECT DISTINCT 
                            d.codigoDiseño,
                            d.nombrePrograma,
                            d.versionPrograma,
                            d.codigoPrograma,
                            c.codigoDiseñoCompetencia,
                            c.nombreCompetencia,
                            c.horasDesarrolloCompetencia
                        FROM competencias c
                        INNER JOIN diseños d ON (
                            CONCAT(d.codigoPrograma, '-', d.versionPrograma) = d.codigoDiseño 
                            AND d.codigoDiseño = SUBSTRING_INDEX(c.codigoDiseñoCompetencia, '-', 2)
                        )
                        WHERE c.codigoCompetencia = :codigoCompetencia";
                
                // Excluir el diseño actual si se proporciona
                if ($disenoActual && trim($disenoActual) !== '') {
                    $sql .= " AND d.codigoDiseño != :disenoActual";
                }
                
                $sql .= " ORDER BY d.nombrePrograma, d.versionPrograma";
                
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':codigoCompetencia', $codigoCompetencia, PDO::PARAM_STR);
                
                if ($disenoActual && trim($disenoActual) !== '') {
                    $stmt->bindParam(':disenoActual', $disenoActual, PDO::PARAM_STR);
                }
                
                $stmt->execute();
                $disenosConMismaCompetencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $comparacion = [];
                
                // Para cada diseño, obtener sus RAPs
                foreach ($disenosConMismaCompetencia as $diseno) {
                    // Buscar RAPs que coincidan con el patrón del diseño-competencia
                    $sqlRaps = "SELECT 
                                    codigoDiseñoCompetenciaRap,
                                    codigoRapAutomatico,
                                    codigoRapDiseño,
                                    nombreRap,
                                    horasDesarrolloRap
                                FROM raps 
                                WHERE codigoDiseñoCompetenciaRap LIKE :patronCompetencia
                                ORDER BY codigoRapAutomatico";
                    
                    $stmtRaps = $conexion->prepare($sqlRaps);
                    $patronCompetencia = $diseno['codigoDiseñoCompetencia'] . '-%';
                    $stmtRaps->bindParam(':patronCompetencia', $patronCompetencia, PDO::PARAM_STR);
                    $stmtRaps->execute();
                    
                    $raps = $stmtRaps->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Siempre agregar el diseño, incluso si no tiene RAPs (para mostrar que existe)
                    $comparacion[] = [
                        'diseno' => $diseno,
                        'raps' => $raps,
                        'totalRaps' => count($raps),
                        'totalHorasRaps' => array_sum(array_column($raps, 'horasDesarrolloRap'))
                    ];
                }
                
                $response['success'] = true;
                $response['data'] = $comparacion; // Cambié 'comparacion' por 'data' para consistencia
                $response['message'] = 'Comparación obtenida exitosamente';
                $response['totalDisenos'] = count($comparacion);
                
                // Debug información más detallada
                $response['debug'] = [
                    'codigoCompetencia' => $codigoCompetencia,
                    'disenoActual' => $disenoActual,
                    'totalDisenosEncontrados' => count($disenosConMismaCompetencia),
                    'totalComparaciones' => count($comparacion),
                    'sqlUsado' => $sql,
                    'patronesBusqueda' => array_column($comparacion, 'diseno')
                ];
                
            } catch (PDOException $e) {
                error_log("Error PDO en obtener_comparacion_raps: " . $e->getMessage());
                $response['success'] = false;
                $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
                $response['error_details'] = $e->getMessage();
            } catch (Exception $e) {
                error_log("Error general en obtener_comparacion_raps: " . $e->getMessage());
                $response['success'] = false;
                $response['message'] = 'Error general: ' . $e->getMessage();
                $response['error_details'] = $e->getMessage();
            }
            break;
            
        default:
            $response['message'] = 'Acción no válida';
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Limpiar cualquier salida previa antes del JSON
ob_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
