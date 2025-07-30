<?php
/**
 * Archivo de autenticación local temporal para diseño curricular
 * Este archivo proporciona funciones básicas de autenticación mientras se configura viaticosApp
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Función temporal para verificar si el usuario está logueado
 */
function isLoggedIn() {
    // Por ahora, permitir acceso (desarrollo)
    // TODO: Implementar verificación real cuando viaticosApp esté disponible
    return true;
}

/**
 * Función temporal para requerir login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        // Redirigir a login cuando esté disponible
        // header('Location: ' . BASE_URL . 'login.php');
        // exit();
    }
}

/**
 * Función temporal para requerir roles específicos
 * @param array $roles Array de roles permitidos
 */
function requireRole($roles) {
    requireLogin();
    
    // Por ahora, permitir acceso para desarrollo
    // TODO: Implementar verificación real de roles
    if (!isset($_SESSION['id_rol'])) {
        // Simular usuario con rol 9 para desarrollo
        $_SESSION['id_rol'] = 9;
        $_SESSION['usuario_temporal'] = true;
    }
    
    // Verificar si el rol del usuario está en los roles permitidos
    if (!in_array($_SESSION['id_rol'], $roles)) {
        // Por ahora, solo registrar en log pero permitir acceso
        error_log("Usuario con rol {$_SESSION['id_rol']} intentó acceder a recurso que requiere roles: " . implode(', ', $roles));
        // TODO: Descomentar cuando la autenticación esté lista
        // header('Location: ' . BASE_URL . 'acceso_denegado.php');
        // exit();
    }
}

/**
 * Clase temporal user para compatibilidad
 */
if (!class_exists('user')) {
    class user {
        public function buscar_usuario($filtro = '') {
            // Datos temporales para desarrollo
            return [
                'id' => 1,
                'nombre' => 'Usuario Temporal',
                'rol' => 9,
                'activo' => true
            ];
        }
        
        public function obtener_usuario_actual() {
            return [
                'id' => 1,
                'nombre' => 'Usuario Temporal',
                'email' => 'temporal@sena.edu.co',
                'rol' => 9
            ];
        }
    }
}

// Variables globales para compatibilidad
if (!isset($miClase)) {
    $miClase = new user();
}

if (!isset($datos)) {
    $datos = $miClase->buscar_usuario('');
}
?>
