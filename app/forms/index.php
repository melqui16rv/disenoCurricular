<?php

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar configuración primero
require_once '/home/appscide/public_html/disenoCurricular/conf/config.php';

// Cargar autenticación (local temporal o viaticosApp)
if (file_exists(VIATICOS_PATH . '/conf/auth.php') && file_exists(VIATICOS_PATH . '/math/gen/user.php')) {
    // Usar autenticación de viaticosApp si está disponible
    require_once VIATICOS_PATH . '/conf/auth.php';
    require_once VIATICOS_PATH . '/math/gen/user.php';
} else {
    // Usar autenticación local temporal
    require_once DISENO_CURRICULAR_PATH . '/conf/auth_local.php';
}

// Requerir rol de administrador (9)
requireRole([9]);

// Cargar métodos específicos de diseño curricular
require_once DISENO_CURRICULAR_PATH . '/math/forms/metodosDisenos.php';

ob_end_flush();

$metodos = new MetodosDisenos();

// Manejar acciones
$accion = $_GET['accion'] ?? 'listar';
$tipo = $_GET['tipo'] ?? 'disenos';
$mensaje = '';
$tipoMensaje = '';

// Parámetros de paginación y filtros
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$registros_por_pagina = (int)($_GET['registros_por_pagina'] ?? 10);
$busqueda = $_GET['busqueda'] ?? '';
$filtro_horas_min = $_GET['horas_min'] ?? '';
$filtro_horas_max = $_GET['horas_max'] ?? '';
$filtro_meses_min = $_GET['meses_min'] ?? '';
$filtro_meses_max = $_GET['meses_max'] ?? '';
$filtro_nivel_academico = $_GET['nivel_academico'] ?? '';
$filtro_red_tecnologica = $_GET['red_tecnologica'] ?? '';
$filtro_estado_completitud = $_GET['estado_completitud'] ?? '';

// Validar registros por página
$registros_permitidos = [5, 10, 25, 50, 100];
if (!in_array($registros_por_pagina, $registros_permitidos)) {
    $registros_por_pagina = 10;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($accion === 'crear' && $tipo === 'disenos') {
            if ($metodos->insertarDiseño($_POST)) {
                $mensaje = 'Diseño creado exitosamente';
                $tipoMensaje = 'success';
                $accion = 'listar';
            }
        } elseif ($accion === 'editar' && $tipo === 'disenos') {
            if ($metodos->actualizarDiseño($_POST['codigoDiseño'], $_POST)) {
                $mensaje = 'Diseño actualizado exitosamente';
                $tipoMensaje = 'success';
                $accion = 'listar';
            }
        } elseif ($accion === 'crear' && $tipo === 'competencias') {
            if ($metodos->insertarCompetencia($_POST['codigoDiseño'], $_POST)) {
                $mensaje = 'Competencia creada exitosamente';
                $tipoMensaje = 'success';
                $accion = 'ver_competencias';

                // Establecer código del diseño para la redirección
                $_GET['codigo'] = $_POST['codigoDiseño'];
            }
        } elseif ($accion === 'editar' && $tipo === 'competencias') {
            if ($metodos->actualizarCompetencia($_POST['codigoDiseñoCompetenciaReporte'], $_POST)) {
                $mensaje = 'Competencia actualizada exitosamente';
                $tipoMensaje = 'success';
                $accion = 'ver_competencias';

                // Extraer código del diseño de la competencia para la redirección
                $partes = explode('-', $_POST['codigoDiseñoCompetenciaReporte']);
                if (count($partes) >= 3) {
                    $_GET['codigo'] = $partes[0] . '-' . $partes[1];
                }
            }
        } elseif ($accion === 'crear' && $tipo === 'raps') {
            if ($metodos->insertarRap($_POST['codigoDiseñoCompetenciaReporte'], $_POST)) {
                $mensaje = 'RAP creado exitosamente';
                $tipoMensaje = 'success';
                $accion = 'ver_raps';

                // Establecer código de la competencia para la redirección
                $_GET['codigo'] = $_POST['codigoDiseñoCompetenciaReporte'];
            }
        } elseif ($accion === 'editar' && $tipo === 'raps') {
            if ($metodos->actualizarRap($_POST['codigoDiseñoCompetenciaReporteRap'], $_POST)) {
                $mensaje = 'RAP actualizado exitosamente';
                $tipoMensaje = 'success';
                $accion = 'ver_raps';

                // Extraer código de la competencia desde el código del RAP para la redirección
                $partes = explode('-', $_POST['codigoDiseñoCompetenciaReporteRap']);
                if (count($partes) >= 4) {
                    $_GET['codigo'] = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
                }
            }
        } elseif ($accion === 'completar' && $tipo === 'disenos') {
            if ($metodos->actualizarDiseño($_POST['codigoDiseño'], $_POST)) {
                $mensaje = 'Información del diseño completada exitosamente';
                $tipoMensaje = 'success';
                $accion = 'completar_informacion';
            }
        } elseif ($accion === 'completar' && $tipo === 'competencias') {
            if ($metodos->actualizarCompetencia($_POST['codigoDiseñoCompetenciaReporte'], $_POST)) {
                $mensaje = 'Información de la competencia completada exitosamente';
                $tipoMensaje = 'success';
                $accion = 'completar_informacion';
            }
        } elseif ($accion === 'completar' && $tipo === 'raps') {
            if ($metodos->actualizarRap($_POST['codigoDiseñoCompetenciaReporteRap'], $_POST)) {
                $mensaje = 'Información del RAP completada exitosamente';
                $tipoMensaje = 'success';
                $accion = 'completar_informacion';
            }
        }
    }

    if ($accion === 'eliminar') {
        $codigo = $_GET['codigo'] ?? '';
        if ($tipo === 'disenos' && $codigo) {
            if ($metodos->eliminarDiseño($codigo)) {
                $mensaje = 'Diseño eliminado exitosamente';
                $tipoMensaje = 'success';
                $accion = 'listar';
            }
        } elseif ($tipo === 'competencias' && $codigo) {
            if ($metodos->eliminarCompetencia($codigo)) {
                $mensaje = 'Competencia eliminada exitosamente';
                $tipoMensaje = 'success';
                $accion = 'ver_competencias';

                // Extraer código del diseño de la competencia para la redirección
                $partes = explode('-', $codigo);
                if (count($partes) >= 3) {
                    $_GET['codigo'] = $partes[0] . '-' . $partes[1];
                }
            }
        } elseif ($tipo === 'raps' && $codigo) {
            if ($metodos->eliminarRap($codigo)) {
                $mensaje = 'RAP eliminado exitosamente';
                $tipoMensaje = 'success';
                $accion = 'ver_raps';

                // Extraer código de la competencia desde el código del RAP para la redirección
                $partes = explode('-', $codigo);
                if (count($partes) >= 4) {
                    $_GET['codigo'] = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
                }
            }
        }
    }

    // Obtener datos según la acción
    $diseños = [];
    $competencias = [];
    $raps = [];
    $diseño_actual = null;
    $competencia_actual = null;
    $rap_actual = null;

    if ($accion === 'listar' || $accion === 'crear') {
        // Obtener datos con paginación y filtros
        $filtros = [
            'busqueda' => $busqueda,
            'horas_min' => $filtro_horas_min,
            'horas_max' => $filtro_horas_max,
            'meses_min' => $filtro_meses_min,
            'meses_max' => $filtro_meses_max,
            'nivel_academico' => $filtro_nivel_academico,
            'red_tecnologica' => $filtro_red_tecnologica,
            'estado_completitud' => $filtro_estado_completitud
        ];
        
        $resultadoDiseños = $metodos->obtenerDiseñosConPaginacion($pagina, $registros_por_pagina, $filtros);
        $diseños = $resultadoDiseños['datos'];
        $total_registros = $resultadoDiseños['total_registros'];
        $total_paginas = ceil($total_registros / $registros_por_pagina);

        // Para crear competencias, cargar información del diseño
        if ($accion === 'crear' && $tipo === 'competencias') {
            $codigoDiseño = $_GET['codigoDiseño'] ?? '';
            if ($codigoDiseño) {
                $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
            }
        }

        // Para crear RAPs, cargar información de la competencia y el diseño
        if ($accion === 'crear' && $tipo === 'raps') {
            $codigoDiseñoCompetenciaReporte = $_GET['codigoDiseñoCompetenciaReporte'] ?? '';
            if ($codigoDiseñoCompetenciaReporte) {
                $competencia_actual = $metodos->obtenerCompetenciaPorCodigo($codigoDiseñoCompetenciaReporte);

                // Extraer código del diseño de la competencia
                $partes = explode('-', $codigoDiseñoCompetenciaReporte);
                if (count($partes) >= 3) {
                    $codigoDiseño = $partes[0] . '-' . $partes[1];
                    $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
                }
            }
        }
    } elseif ($accion === 'ver_competencias') {
        $codigoDiseño = $_GET['codigo'] ?? '';
        $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
        
        // Obtener competencias con paginación
        $filtros = [
            'busqueda' => $busqueda,
            'horas_min' => $filtro_horas_min,
            'horas_max' => $filtro_horas_max
        ];
        
        $resultadoCompetencias = $metodos->obtenerCompetenciasConPaginacion($codigoDiseño, $pagina, $registros_por_pagina, $filtros);
        $competencias = $resultadoCompetencias['datos'];
        $total_registros = $resultadoCompetencias['total_registros'];
        $total_paginas = ceil($total_registros / $registros_por_pagina);
    } elseif ($accion === 'ver_raps') {
        $codigoDiseñoCompetenciaReporte = $_GET['codigo'] ?? '';
        $competencia_actual = $metodos->obtenerCompetenciaPorCodigo($codigoDiseñoCompetenciaReporte);
        
        // Obtener RAPs con paginación
        $filtros = [
            'busqueda' => $busqueda,
            'horas_min' => $filtro_horas_min,
            'horas_max' => $filtro_horas_max
        ];
        
        $resultadoRaps = $metodos->obtenerRapsConPaginacion($codigoDiseñoCompetenciaReporte, $pagina, $registros_por_pagina, $filtros);
        $raps = $resultadoRaps['datos'];
        $total_registros = $resultadoRaps['total_registros'];
        $total_paginas = ceil($total_registros / $registros_por_pagina);
    } elseif ($accion === 'editar') {
        if ($tipo === 'disenos') {
            $diseño_actual = $metodos->obtenerDiseñoPorCodigo($_GET['codigo'] ?? '');
        } elseif ($tipo === 'competencias') {
            $competencia_actual = $metodos->obtenerCompetenciaPorCodigo($_GET['codigo'] ?? '');

            // Cargar también información del diseño para mostrar contexto
            if ($competencia_actual) {
                $partes = explode('-', $competencia_actual['codigoDiseñoCompetenciaReporte']);
                if (count($partes) >= 3) {
                    $codigoDiseño = $partes[0] . '-' . $partes[1];
                    $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
                }
            }
        } elseif ($tipo === 'raps') {
            $rap_actual = $metodos->obtenerRapPorCodigo($_GET['codigo'] ?? '');

            // Cargar también información de la competencia y el diseño para mostrar contexto
            if ($rap_actual) {
                $partes = explode('-', $rap_actual['codigoDiseñoCompetenciaReporteRap']);
                if (count($partes) >= 4) {
                    $codigoCompetencia = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
                    $competencia_actual = $metodos->obtenerCompetenciaPorCodigo($codigoCompetencia);

                    $codigoDiseño = $partes[0] . '-' . $partes[1];
                    $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
                }
            }
        }
    } elseif ($accion === 'completar') {
        if ($tipo === 'disenos') {
            $diseño_actual = $metodos->obtenerDiseñoPorCodigo($_GET['codigo'] ?? '');
        } elseif ($tipo === 'competencias') {
            $competencia_actual = $metodos->obtenerCompetenciaPorCodigo($_GET['codigo'] ?? '');

            // Cargar también información del diseño para mostrar contexto
            if ($competencia_actual) {
                $partes = explode('-', $competencia_actual['codigoDiseñoCompetenciaReporte']);
                if (count($partes) >= 3) {
                    $codigoDiseño = $partes[0] . '-' . $partes[1];
                    $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
                }
            }
        } elseif ($tipo === 'raps') {
            $rap_actual = $metodos->obtenerRapPorCodigo($_GET['codigo'] ?? '');

            // Cargar también información de la competencia y el diseño para mostrar contexto
            if ($rap_actual) {
                $partes = explode('-', $rap_actual['codigoDiseñoCompetenciaReporteRap']);
                if (count($partes) >= 4) {
                    $codigoCompetencia = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
                    $competencia_actual = $metodos->obtenerCompetenciaPorCodigo($codigoCompetencia);

                    $codigoDiseño = $partes[0] . '-' . $partes[1];
                    $diseño_actual = $metodos->obtenerDiseñoPorCodigo($codigoDiseño);
                }
            }
        }
    }

} catch (Exception $e) {
    $mensaje = 'Error: ' . $e->getMessage();
    $tipoMensaje = 'danger';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Diseños Curriculares</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/forms/estilosPrincipales.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/forms/filtros-paginacion.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo VIATICOS_PATH; ?>/assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo VIATICOS_PATH; ?>/assets/css/share/nav.css">
</head>
<body>
    <div class="container">
        <!-- Navegación -->
        <?php
            require_once VIATICOS_PATH . '/public/share/navDisenoCurricular.php';
        ?>
        <?php include 'vistas/nav.php'; ?>

        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Sistema de Diseños Curriculares</h1>
            <p>Gestión integral de programas formativos del SENA</p>
        </div>

        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <i class="fas fa-<?php echo $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Navegación por breadcrumb -->
        <?php if ($accion !== 'listar'): ?>
            <div class="breadcrumb">
                <a href="?accion=listar"><i class="fas fa-home"></i> Inicio</a>
                <span class="separator">/</span>
                <?php if ($accion === 'ver_competencias'): ?>
                    <span class="current">Competencias del Diseño <?php echo htmlspecialchars($_GET['codigo'] ?? ''); ?></span>
                <?php elseif ($accion === 'ver_raps'): ?>
                    <a href="?accion=ver_competencias&codigo=<?php echo htmlspecialchars(explode('-', $_GET['codigo'])[0] . '-' . explode('-', $_GET['codigo'])[1]); ?>">Competencias</a>
                    <span class="separator">/</span>
                    <span class="current">RAPs de la Competencia</span>
                <?php elseif ($accion === 'crear'): ?>
                    <span class="current">Crear Nuevo <?php echo ucfirst($tipo); ?></span>
                <?php elseif ($accion === 'editar'): ?>
                    <span class="current">Editar <?php echo ucfirst($tipo); ?></span>
                <?php elseif ($accion === 'completar'): ?>
                    <a href="?accion=completar_informacion">Completar Información</a>
                    <span class="separator">/</span>
                    <span class="current">Completar <?php echo ucfirst($tipo); ?></span>
                <?php elseif ($accion === 'completar_informacion'): ?>
                    <span class="current">Completar Información Faltante</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Contenido principal -->
        <div class="card fade-in">
            <?php 
            switch ($accion) {
                case 'listar':
                    include 'vistas/listar_disenos.php';
                    break;
                case 'crear':
                    include 'vistas/crear_' . $tipo . '.php';
                    break;
                case 'editar':
                    include 'vistas/editar_' . $tipo . '.php';
                    break;
                case 'ver_competencias':
                    include 'vistas/listar_competencias.php';
                    break;
                case 'ver_raps':
                    include 'vistas/listar_raps.php';
                    break;
                case 'completar_informacion':
                    include 'vistas/completar_informacion.php';
                    break;
                case 'completar':
                    include 'vistas/completar_' . $tipo . '.php';
                    break;
                default:
                    include 'vistas/listar_disenos.php';
            }
            ?>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>assets/js/forms/scripts.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/forms/completar-informacion.js"></script>
    <!-- <script src="<?php echo BASE_URL; ?>assets/js/forms/completar-informacion-mejorado.js"></script> -->
</body>
</html>
