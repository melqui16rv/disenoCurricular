<?php
/**
 * Script de debug para validar el sistema desde línea de comandos
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir directorio base del proyecto
$proyecto_base = __DIR__;

echo "=== DEBUG SISTEMA COMPLETAR INFORMACION ===\n";
echo "Directorio base: $proyecto_base\n\n";

// 1. Verificar estructura de archivos
echo "1. VERIFICACION DE ARCHIVOS:\n";
echo "=====================================\n";

$archivos_requeridos = [
    'Router Principal' => '/app/forms/index.php',
    'Vista Nueva' => '/app/forms/vistas/completar_informacion_new.php',
    'Vista Original' => '/app/forms/vistas/completar_informacion.php',
    'Metodos Disenos' => '/math/forms/metodosDisenos.php',
    'Configuracion' => '/conf/config.php',
    'CSS Principal' => '/assets/css/forms/estilosPrincipales.css',
    'JS Completar' => '/assets/js/forms/completar-informacion.js',
    'Conexion BD' => '/sql/conexion.php'
];

$archivos_encontrados = 0;
foreach ($archivos_requeridos as $nombre => $ruta_relativa) {
    $ruta_completa = $proyecto_base . $ruta_relativa;
    $existe = file_exists($ruta_completa);
    $archivos_encontrados += $existe ? 1 : 0;
    
    $status = $existe ? "[OK]" : "[ERROR]";
    $tamanio = $existe ? " (" . number_format(filesize($ruta_completa)) . " bytes)" : "";
    
    echo "$status $nombre: $ruta_completa$tamanio\n";
}

echo "\nRESUMEN: $archivos_encontrados/" . count($archivos_requeridos) . " archivos encontrados\n\n";

// 2. Verificar configuración
echo "2. VERIFICACION DE CONFIGURACION:\n";
echo "==================================\n";

$config_path = $proyecto_base . '/conf/config.php';
if (file_exists($config_path)) {
    echo "[OK] Archivo de configuracion encontrado\n";
    
    // Capturar el contenido para verificar las constantes
    $config_content = file_get_contents($config_path);
    $config_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
    
    foreach ($config_vars as $var) {
        if (strpos($config_content, "define('$var'") !== false) {
            echo "[OK] Variable $var: Definida en el archivo\n";
        } else {
            echo "[ERROR] Variable $var: NO DEFINIDA\n";
        }
    }
    
    // Incluir configuración para probar conexión
    try {
        include_once $config_path;
        echo "[OK] Configuracion cargada correctamente\n";
        
        if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                    DB_USER,
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                echo "[OK] Conexion a base de datos exitosa\n";
                
                // Verificar tablas
                $tablas = ['disenos_curriculares', 'competencias', 'raps'];
                foreach ($tablas as $tabla) {
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabla LIMIT 1");
                        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                        echo "[OK] Tabla '$tabla': $count registros\n";
                    } catch (Exception $e) {
                        echo "[ERROR] Tabla '$tabla': " . $e->getMessage() . "\n";
                    }
                }
                
            } catch (Exception $e) {
                echo "[ERROR] Conexion BD: " . $e->getMessage() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "[ERROR] Cargando configuracion: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "[ERROR] Archivo de configuracion no encontrado: $config_path\n";
}

echo "\n";

// 3. Verificar clase MetodosDisenos
echo "3. VERIFICACION DE CLASE METODOSDISENIOS:\n";
echo "=========================================\n";

$metodos_path = $proyecto_base . '/math/forms/metodosDisenos.php';
if (file_exists($metodos_path)) {
    echo "[OK] Archivo metodosDisenos.php encontrado\n";
    
    try {
        include_once $metodos_path;
        
        if (class_exists('MetodosDisenos')) {
            echo "[OK] Clase MetodosDisenos encontrada\n";
            
            try {
                $metodos = new MetodosDisenos();
                echo "[OK] Instancia creada correctamente\n";
                
                // Verificar métodos
                $metodos_check = [
                    'obtenerDisenosConInformacionFaltante',
                    'obtenerCompetenciasConInformacionFaltante',
                    'obtenerRapsConInformacionFaltante'
                ];
                
                foreach ($metodos_check as $metodo) {
                    if (method_exists($metodos, $metodo)) {
                        echo "[OK] Metodo '$metodo': Disponible\n";
                    } else {
                        echo "[ERROR] Metodo '$metodo': NO DISPONIBLE\n";
                    }
                }
                
            } catch (Exception $e) {
                echo "[ERROR] Creando instancia: " . $e->getMessage() . "\n";
            }
        } else {
            echo "[ERROR] Clase MetodosDisenos no encontrada\n";
        }
        
    } catch (Exception $e) {
        echo "[ERROR] Incluyendo archivo: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "[ERROR] Archivo metodosDisenos.php no encontrado\n";
}

echo "\n";

// 4. Verificar contenido de index.php
echo "4. VERIFICACION DE ROUTER (index.php):\n";
echo "======================================\n";

$index_path = $proyecto_base . '/app/forms/index.php';
if (file_exists($index_path)) {
    echo "[OK] Router index.php encontrado\n";
    
    $index_content = file_get_contents($index_path);
    
    // Verificar que incluye la vista nueva
    if (strpos($index_content, 'completar_informacion_new.php') !== false) {
        echo "[OK] Router incluye completar_informacion_new.php\n";
    } else {
        echo "[ERROR] Router NO incluye completar_informacion_new.php\n";
    }
    
    // Verificar que incluye dependencias
    if (strpos($index_content, 'metodosDisenos.php') !== false) {
        echo "[OK] Router incluye metodosDisenos.php\n";
    } else {
        echo "[ERROR] Router NO incluye metodosDisenos.php\n";
    }
    
    if (strpos($index_content, 'config.php') !== false) {
        echo "[OK] Router incluye config.php\n";
    } else {
        echo "[ERROR] Router NO incluye config.php\n";
    }
    
} else {
    echo "[ERROR] Router index.php no encontrado\n";
}

echo "\n";

// 5. Verificar vista completar_informacion_new.php
echo "5. VERIFICACION DE VISTA NUEVA:\n";
echo "===============================\n";

$vista_path = $proyecto_base . '/app/forms/vistas/completar_informacion_new.php';
if (file_exists($vista_path)) {
    echo "[OK] Vista completar_informacion_new.php encontrada\n";
    
    $vista_content = file_get_contents($vista_path);
    
    // Verificar validación de $metodos
    if (strpos($vista_content, 'if (!isset($metodos))') !== false) {
        echo "[OK] Vista incluye validacion de \$metodos\n";
    } else {
        echo "[WARN] Vista NO incluye validacion de \$metodos\n";
    }
    
    // Verificar funciones de paginación
    if (strpos($vista_content, 'generarPaginacion') !== false) {
        echo "[OK] Vista incluye funciones de paginacion\n";
    } else {
        echo "[ERROR] Vista NO incluye funciones de paginacion\n";
    }
    
} else {
    echo "[ERROR] Vista completar_informacion_new.php no encontrada\n";
}

echo "\n";

// 6. Test de consultas (si está disponible)
echo "6. TEST DE CONSULTAS:\n";
echo "====================\n";

if (isset($metodos) && is_object($metodos)) {
    try {
        $filtros_test = [
            'busqueda' => '',
            'estado' => '',
            'horas_min' => 0,
            'horas_max' => 0,
            'tipo_programa' => '',
            'nivel_academico' => ''
        ];
        
        if (method_exists($metodos, 'obtenerDisenosConInformacionFaltante')) {
            $resultado = $metodos->obtenerDisenosConInformacionFaltante($filtros_test, 1, 5);
            if ($resultado && isset($resultado['datos'])) {
                echo "[OK] Consulta de disenos exitosa: " . count($resultado['datos']) . " registros\n";
            } else {
                echo "[WARN] Consulta de disenos sin resultados\n";
            }
        } else {
            echo "[ERROR] Metodo obtenerDisenosConInformacionFaltante no existe\n";
        }
        
    } catch (Exception $e) {
        echo "[ERROR] Test de consultas: " . $e->getMessage() . "\n";
    }
} else {
    echo "[SKIP] Test de consultas (objeto \$metodos no disponible)\n";
}

echo "\n";

// 7. Recomendaciones
echo "7. RECOMENDACIONES:\n";
echo "==================\n";

if ($archivos_encontrados < count($archivos_requeridos)) {
    echo "- Verificar rutas de archivos faltantes\n";
}

if (!file_exists($config_path)) {
    echo "- Crear archivo de configuracion conf/config.php\n";
}

echo "- Probar acceso via navegador: http://localhost/app/forms/index.php?accion=completar_informacion\n";
echo "- Revisar logs del servidor web para errores PHP\n";
echo "- Verificar permisos de archivos y directorios\n";

echo "\n=== DEBUG COMPLETADO ===\n";
?>
