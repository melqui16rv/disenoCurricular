<?php
// Controlador AJAX para operaciones del sistema de diseños curriculares
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosDisenos.php';

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
            
        default:
            $response['message'] = 'Acción no válida';
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
