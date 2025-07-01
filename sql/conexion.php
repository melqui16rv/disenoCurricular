<?php
// Configurar para no mostrar errores en producción
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

class Conexion {
    private $host = 'localhost';
    private $dbname = 'disenos_curriculares';
    private $user = 'root';
    private $password = '';
    private $port = 3306;
    private $charset = 'utf8mb4';
    private $conexion;

    public function __construct() {
        try {
            $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=$this->charset";
            $this->conexion = new PDO($dsn, $this->user, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Establecer la collation
            $this->conexion->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        } catch (PDOException $e) {
            // Log del error en lugar de mostrarlo
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            throw new Exception('Error de conexión a la base de datos');
        }
    }

    public function obtenerConexion() {
        return $this->conexion;
    }
}
?>
