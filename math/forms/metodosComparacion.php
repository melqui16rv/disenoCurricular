<?php
require_once __DIR__ . '/../../sql/conexion.php';

class comparacion extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Obtener diseños curriculares que tienen la misma competencia
    public function obtenerDisenosConMismaCompetencia($codigoCompetencia, $disenoActual = null) {
        try {
            $sql = "SELECT DISTINCT 
                        d.codigoDiseño,
                        d.nombrePrograma,
                        d.versionPrograma,
                        c.codigoDiseñoCompetenciaReporte
                    FROM competencias c
                    INNER JOIN diseños d ON SUBSTRING_INDEX(c.codigoDiseñoCompetenciaReporte, '-', 2) = d.codigoDiseño
                    WHERE c.codigoCompetenciaReporte = :codigoCompetencia";
            
            // Excluir el diseño actual si se proporciona
            if ($disenoActual) {
                $sql .= " AND d.codigoDiseño != :disenoActual";
            }
            
            $sql .= " ORDER BY d.nombrePrograma, d.versionPrograma";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigoCompetencia', $codigoCompetencia);
            
            if ($disenoActual) {
                $stmt->bindParam(':disenoActual', $disenoActual);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener diseños con misma competencia: " . $e->getMessage());
            return [];
        }
    }

    // Obtener RAPs de una competencia específica en un diseño específico
    public function obtenerRapsPorCompetenciaDiseno($codigoDisenoCompetenciaReporte) {
        try {
            $sql = "SELECT 
                        codigoDiseñoCompetenciaReporteRap,
                        codigoRapReporte,
                        nombreRap,
                        horasDesarrolloRap
                    FROM raps 
                    WHERE codigoDiseñoCompetenciaReporteRap LIKE :codigoDisenoCompetenciaReporte
                    ORDER BY codigoDiseñoCompetenciaReporteRap";
            
            $stmt = $this->conexion->prepare($sql);
            $patron = $codigoDisenoCompetenciaReporte . '-%';
            $stmt->bindParam(':codigoDisenoCompetenciaReporte', $patron);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener RAPs por competencia diseño: " . $e->getMessage());
            return [];
        }
    }

    // Obtener comparación completa de RAPs por competencia
    public function obtenerComparacionRaps($codigoCompetencia, $disenoActual = null) {
        try {
            $disenosConMismaCompetencia = $this->obtenerDisenosConMismaCompetencia($codigoCompetencia, $disenoActual);
            $comparacion = [];
            
            foreach ($disenosConMismaCompetencia as $diseno) {
                $raps = $this->obtenerRapsPorCompetenciaDiseno($diseno['codigoDiseñoCompetenciaReporte']);
                
                if (!empty($raps)) {
                    $comparacion[] = [
                        'diseno' => $diseno,
                        'raps' => $raps
                    ];
                }
            }
            
            return $comparacion;
            
        } catch (Exception $e) {
            error_log("Error al obtener comparación de RAPs: " . $e->getMessage());
            return [];
        }
    }

    // Extraer código de competencia de un código de RAP
    public function extraerCodigoCompetencia($codigoRap) {
        $partes = explode('-', $codigoRap);
        if (count($partes) >= 3) {
            return $partes[2]; // El código de competencia está en la tercera posición
        }
        return null;
    }

    // Extraer código de diseño de un código de competencia o RAP
    public function extraerCodigoDiseno($codigo) {
        $partes = explode('-', $codigo);
        if (count($partes) >= 2) {
            return $partes[0] . '-' . $partes[1]; // codigoPrograma-version
        }
        return null;
    }

// metodos ... ... 

}