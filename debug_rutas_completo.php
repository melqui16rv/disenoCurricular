<?php
/**
 * Script de debug completo para validar el sistema de completar información
 * Diagnóstica rutas, archivos, dependencias, conexión a BD y datos
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>DEBUG COMPLETO - Sistema Completar Información</h1>";
echo "<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; }
    .section { margin: 20px 0; padding: 15px; border-radius: 8px; }
    .success { background: #d4edda; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; border-left: 4px solid #dc3545; }
    .warning { background: #fff3cd; border-left: 4px solid #ffc107; }
    .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
    .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; }
    h2 { color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 5px; }
    .icon { font-size: 1.2em; margin-right: 8px; }
</style>";

// 1. Verificar estructura de archivos
echo "<div class='section info'>";
echo "<h2>1. Verificacion de Archivos y Rutas</h2>";

$archivos_requeridos = [
    'Router Principal' => '/app/forms/index.php',
    'Vista Nueva' => '/app/forms/vistas/completar_informacion_new.php',
    'Vista Original' => '/app/forms/vistas/completar_informacion.php',
    'Métodos Diseños' => '/math/forms/metodosDisenos.php',
    'Configuración' => '/conf/config.php',
    'CSS Principal' => '/assets/css/forms/estilosPrincipales.css',
    'JS Completar' => '/assets/js/forms/completar-informacion.js',
    'Conexión BD' => '/sql/conexion.php'
];

$archivos_encontrados = 0;
foreach ($archivos_requeridos as $nombre => $ruta_relativa) {
    $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $ruta_relativa;
    $existe = file_exists($ruta_completa);
    $archivos_encontrados += $existe ? 1 : 0;
    
    echo "<div class='" . ($existe ? "success" : "error") . "'>";
    echo "<span class='icon'>" . ($existe ? "✅" : "❌") . "</span>";
    echo "<strong>$nombre:</strong> ";
    echo $existe ? "Encontrado" : "NO ENCONTRADO";
    echo " <code>$ruta_completa</code>";
    if ($existe) {
        echo " (" . number_format(filesize($ruta_completa)) . " bytes)";
    }
    echo "</div>";
}

echo "<div class='info'>";
echo "<strong>Resumen:</strong> $archivos_encontrados/" . count($archivos_requeridos) . " archivos encontrados";
echo "</div>";
echo "</div>";

// 2. Verificar configuración y conexión a BD
echo "<div class='section info'>";
echo "<h2>🔌 2. Verificación de Configuración y Base de Datos</h2>";

try {
    $config_path = $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
    if (file_exists($config_path)) {
        include_once $config_path;
        echo "<div class='success'><span class='icon'>✅</span>Archivo de configuración cargado correctamente</div>";
        
        // Verificar variables de configuración
        $config_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
        foreach ($config_vars as $var) {
            if (defined($var)) {
                echo "<div class='success'><span class='icon'>✅</span>$var: Definida</div>";
            } else {
                echo "<div class='error'><span class='icon'>❌</span>$var: NO DEFINIDA</div>";
            }
        }
    } else {
        echo "<div class='error'><span class='icon'>❌</span>Archivo de configuración no encontrado</div>";
    }
    
    // Probar conexión a BD
    if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "<div class='success'><span class='icon'>✅</span>Conexión a base de datos exitosa</div>";
            
            // Verificar tablas principales
            $tablas_requeridas = ['disenos_curriculares', 'competencias', 'raps'];
            foreach ($tablas_requeridas as $tabla) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabla LIMIT 1");
                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    echo "<div class='success'><span class='icon'>✅</span>Tabla '$tabla': $count registros</div>";
                } catch (Exception $e) {
                    echo "<div class='error'><span class='icon'>❌</span>Tabla '$tabla': Error - " . $e->getMessage() . "</div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='error'><span class='icon'>❌</span>Error de conexión a BD: " . $e->getMessage() . "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'><span class='icon'>❌</span>Error en configuración: " . $e->getMessage() . "</div>";
}
echo "</div>";

// 3. Verificar clase MetodosDisenos
echo "<div class='section info'>";
echo "<h2>🔧 3. Verificación de Clase MetodosDisenos</h2>";

try {
    $metodos_path = $_SERVER['DOCUMENT_ROOT'] . '/math/forms/metodosDisenos.php';
    if (file_exists($metodos_path)) {
        include_once $metodos_path;
        echo "<div class='success'><span class='icon'>✅</span>Archivo metodosDisenos.php cargado</div>";
        
        if (class_exists('MetodosDisenos')) {
            echo "<div class='success'><span class='icon'>✅</span>Clase MetodosDisenos encontrada</div>";
            
            try {
                $metodos = new MetodosDisenos();
                echo "<div class='success'><span class='icon'>✅</span>Instancia de MetodosDisenos creada</div>";
                
                // Verificar métodos específicos
                $metodos_requeridos = [
                    'obtenerDisenosConInformacionFaltante',
                    'obtenerCompetenciasConInformacionFaltante', 
                    'obtenerRapsConInformacionFaltante'
                ];
                
                foreach ($metodos_requeridos as $metodo) {
                    if (method_exists($metodos, $metodo)) {
                        echo "<div class='success'><span class='icon'>✅</span>Método '$metodo': Disponible</div>";
                    } else {
                        echo "<div class='error'><span class='icon'>❌</span>Método '$metodo': NO DISPONIBLE</div>";
                    }
                }
                
            } catch (Exception $e) {
                echo "<div class='error'><span class='icon'>❌</span>Error al crear instancia: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='error'><span class='icon'>❌</span>Clase MetodosDisenos no encontrada</div>";
        }
    } else {
        echo "<div class='error'><span class='icon'>❌</span>Archivo metodosDisenos.php no encontrado</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'><span class='icon'>❌</span>Error en verificación de métodos: " . $e->getMessage() . "</div>";
}
echo "</div>";

// 4. Probar consultas de datos
echo "<div class='section info'>";
echo "<h2>📊 4. Verificación de Datos y Consultas</h2>";

if (isset($metodos) && is_object($metodos)) {
    try {
        // Probar consulta de diseños
        echo "<h3>Diseños con información faltante:</h3>";
        $filtros_test = [
            'busqueda' => '',
            'estado' => '',
            'horas_min' => 0,
            'horas_max' => 0,
            'tipo_programa' => '',
            'nivel_academico' => ''
        ];
        
        $resultado_disenos = $metodos->obtenerDisenosConInformacionFaltante($filtros_test, 1, 5);
        if ($resultado_disenos && isset($resultado_disenos['datos'])) {
            echo "<div class='success'><span class='icon'>✅</span>Consulta exitosa: " . count($resultado_disenos['datos']) . " diseños encontrados</div>";
            echo "<div class='info'>Total de registros: " . ($resultado_disenos['total_registros'] ?? 0) . "</div>";
            
            if (!empty($resultado_disenos['datos'])) {
                echo "<div class='code'>";
                echo "<strong>Ejemplo de diseño:</strong><br>";
                $primer_diseno = $resultado_disenos['datos'][0];
                foreach ($primer_diseno as $campo => $valor) {
                    echo "$campo: " . (is_null($valor) ? 'NULL' : htmlspecialchars($valor)) . "<br>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='warning'><span class='icon'>⚠️</span>No se obtuvieron resultados de diseños</div>";
        }
        
        // Probar consulta de competencias
        echo "<h3>Competencias con información faltante:</h3>";
        $resultado_competencias = $metodos->obtenerCompetenciasConInformacionFaltante($filtros_test, 1, 5);
        if ($resultado_competencias && isset($resultado_competencias['datos'])) {
            echo "<div class='success'><span class='icon'>✅</span>Consulta exitosa: " . count($resultado_competencias['datos']) . " competencias encontradas</div>";
            echo "<div class='info'>Total de registros: " . ($resultado_competencias['total_registros'] ?? 0) . "</div>";
        } else {
            echo "<div class='warning'><span class='icon'>⚠️</span>No se obtuvieron resultados de competencias</div>";
        }
        
        // Probar consulta de RAPs
        echo "<h3>RAPs con información faltante:</h3>";
        $resultado_raps = $metodos->obtenerRapsConInformacionFaltante($filtros_test, 1, 5);
        if ($resultado_raps && isset($resultado_raps['datos'])) {
            echo "<div class='success'><span class='icon'>✅</span>Consulta exitosa: " . count($resultado_raps['datos']) . " RAPs encontrados</div>";
            echo "<div class='info'>Total de registros: " . ($resultado_raps['total_registros'] ?? 0) . "</div>";
        } else {
            echo "<div class='warning'><span class='icon'>⚠️</span>No se obtuvieron resultados de RAPs</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'><span class='icon'>❌</span>Error en consultas: " . $e->getMessage() . "</div>";
        echo "<div class='code'>Trace: " . $e->getTraceAsString() . "</div>";
    }
} else {
    echo "<div class='error'><span class='icon'>❌</span>Objeto \$metodos no disponible para probar consultas</div>";
}
echo "</div>";

// 5. Verificar rutas y URLs
echo "<div class='section info'>";
echo "<h2">🌐 5. Verificación de Rutas y URLs</h2>";

$url_base = 'http://' . $_SERVER['HTTP_HOST'];
$ruta_app = $url_base . '/app/forms/';

echo "<div class='info'><strong>URL Base:</strong> $url_base</div>";
echo "<div class='info'><strong>Ruta App:</strong> $ruta_app</div>";
echo "<div class='info'><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</div>";

// URLs importantes a verificar
$urls_importantes = [
    'Index Principal' => $ruta_app . 'index.php',
    'Completar Info' => $ruta_app . 'index.php?accion=completar_informacion',
    'Assets CSS' => $url_base . '/assets/css/forms/estilosPrincipales.css',
    'Assets JS' => $url_base . '/assets/js/forms/completar-informacion.js'
];

foreach ($urls_importantes as $nombre => $url) {
    echo "<div class='info'>";
    echo "<span class='icon'>🔗</span>";
    echo "<strong>$nombre:</strong> <a href='$url' target='_blank'>$url</a>";
    echo "</div>";
}
echo "</div>";

// 6. Test de acceso directo a la vista
echo "<div class='section info'>";
echo "<h2>🎯 6. Test de Acceso Directo</h2>";

echo "<div class='warning'>";
echo "<span class='icon'>⚠️</span>";
echo "<strong>Importante:</strong> Para un test completo, visita: ";
echo "<a href='" . $ruta_app . "index.php?accion=completar_informacion' target='_blank'>";
echo $ruta_app . "index.php?accion=completar_informacion";
echo "</a>";
echo "</div>";

echo "<div class='info'>";
echo "<span class='icon'>💡</span>";
echo "<strong>Pasos siguientes recomendados:</strong><br>";
echo "1. Visita la URL de arriba para probar la vista completa<br>";
echo "2. Revisa la consola del navegador para errores JavaScript<br>";
echo "3. Verifica que los estilos CSS se carguen correctamente<br>";
echo "4. Prueba los filtros y la paginación<br>";
echo "5. Revisa los logs del servidor web para errores PHP";
echo "</div>";
echo "</div>";

// 7. Información del entorno
echo "<div class='section info'>";
echo "<h2>⚙️ 7. Información del Entorno</h2>";

echo "<div class='code'>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No definido') . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
echo "<strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "</div>";
echo "</div>";

echo "<div class='section success'>";
echo "<h2>✅ Debug Completado</h2>";
echo "<p>Revisa cada sección arriba para identificar problemas. Los elementos marcados con ❌ necesitan atención.</p>";
echo "</div>";
?>
