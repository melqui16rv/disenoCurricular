<?php
require_once __DIR__ . '/../../sql/conexion.php';

class MetodosDisenos extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // MÉTODOS PARA DISEÑOS
    public function obtenerTodosLosDiseños() {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM diseños ORDER BY codigoDiseño DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener diseños: " . $e->getMessage());
        }
    }

    public function obtenerDiseñoPorCodigo($codigoDiseño) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM diseños WHERE codigoDiseño = ?");
            $stmt->execute([$codigoDiseño]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener diseño: " . $e->getMessage());
        }
    }

    public function insertarDiseño($datos) {
        try {
            // Generar codigoDiseño concatenando codigoPrograma y versionPrograma
            $codigoDiseño = $datos['codigoPrograma'] . '-' . $datos['versionPrograma'];
            
            // Calcular campos derivados solo si ambos campos están presentes
            $horasDesarrolloDiseño = 0;
            $mesesDesarrolloDiseño = 0;
            
            if (isset($datos['horasDesarrolloLectiva']) && isset($datos['horasDesarrolloProductiva'])) {
                $horasDesarrolloDiseño = $datos['horasDesarrolloLectiva'] + $datos['horasDesarrolloProductiva'];
            }
            
            if (isset($datos['mesesDesarrolloLectiva']) && isset($datos['mesesDesarrolloProductiva'])) {
                $mesesDesarrolloDiseño = $datos['mesesDesarrolloLectiva'] + $datos['mesesDesarrolloProductiva'];
            }

            $sql = "INSERT INTO diseños (codigoDiseño, codigoPrograma, versionPrograma, nombrePrograma, 
                    lineaTecnologica, redTecnologica, redConocimiento, horasDesarrolloLectiva, 
                    horasDesarrolloProductiva, mesesDesarrolloLectiva, mesesDesarrolloProductiva, 
                    horasDesarrolloDiseño, mesesDesarrolloDiseño, nivelAcademicoIngreso, 
                    gradoNivelAcademico, formacionTrabajoDesarrolloHumano, edadMinima, requisitosAdicionales) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $codigoDiseño, $datos['codigoPrograma'], $datos['versionPrograma'], $datos['nombrePrograma'],
                $datos['lineaTecnologica'], $datos['redTecnologica'], $datos['redConocimiento'],
                $datos['horasDesarrolloLectiva'] ?? 0, $datos['horasDesarrolloProductiva'] ?? 0,
                $datos['mesesDesarrolloLectiva'] ?? 0, $datos['mesesDesarrolloProductiva'] ?? 0,
                $horasDesarrolloDiseño, $mesesDesarrolloDiseño, $datos['nivelAcademicoIngreso'],
                $datos['gradoNivelAcademico'], $datos['formacionTrabajoDesarrolloHumano'],
                $datos['edadMinima'], $datos['requisitosAdicionales']
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al insertar diseño: " . $e->getMessage());
        }
    }

    public function actualizarDiseño($codigoDiseño, $datos) {
        try {
            // Calcular campos derivados solo si ambos campos están presentes
            $horasDesarrolloDiseño = 0;
            $mesesDesarrolloDiseño = 0;
            
            if (isset($datos['horasDesarrolloLectiva']) && isset($datos['horasDesarrolloProductiva'])) {
                $horasDesarrolloDiseño = $datos['horasDesarrolloLectiva'] + $datos['horasDesarrolloProductiva'];
            }
            
            if (isset($datos['mesesDesarrolloLectiva']) && isset($datos['mesesDesarrolloProductiva'])) {
                $mesesDesarrolloDiseño = $datos['mesesDesarrolloLectiva'] + $datos['mesesDesarrolloProductiva'];
            }

            $sql = "UPDATE diseños SET nombrePrograma = ?, lineaTecnologica = ?, redTecnologica = ?, 
                    redConocimiento = ?, horasDesarrolloLectiva = ?, horasDesarrolloProductiva = ?, 
                    mesesDesarrolloLectiva = ?, mesesDesarrolloProductiva = ?, horasDesarrolloDiseño = ?, 
                    mesesDesarrolloDiseño = ?, nivelAcademicoIngreso = ?, gradoNivelAcademico = ?, 
                    formacionTrabajoDesarrolloHumano = ?, edadMinima = ?, requisitosAdicionales = ? 
                    WHERE codigoDiseño = ?";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $datos['nombrePrograma'], $datos['lineaTecnologica'], $datos['redTecnologica'],
                $datos['redConocimiento'], 
                $datos['horasDesarrolloLectiva'] ?? 0, $datos['horasDesarrolloProductiva'] ?? 0,
                $datos['mesesDesarrolloLectiva'] ?? 0, $datos['mesesDesarrolloProductiva'] ?? 0,
                $horasDesarrolloDiseño, $mesesDesarrolloDiseño, $datos['nivelAcademicoIngreso'],
                $datos['gradoNivelAcademico'], $datos['formacionTrabajoDesarrolloHumano'],
                $datos['edadMinima'], $datos['requisitosAdicionales'], $codigoDiseño
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar diseño: " . $e->getMessage());
        }
    }

    public function eliminarDiseño($codigoDiseño) {
        try {
            // Primero eliminar RAPs relacionados
            $this->eliminarRapsPorDiseño($codigoDiseño);
            // Luego eliminar competencias relacionadas
            $this->eliminarCompetenciasPorDiseño($codigoDiseño);
            // Finalmente eliminar el diseño
            $stmt = $this->conexion->prepare("DELETE FROM diseños WHERE codigoDiseño = ?");
            return $stmt->execute([$codigoDiseño]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar diseño: " . $e->getMessage());
        }
    }

    // MÉTODOS PARA COMPETENCIAS
    public function obtenerCompetenciasPorDiseño($codigoDiseño) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetencia LIKE ? ORDER BY codigoDiseñoCompetencia");
            $stmt->execute([$codigoDiseño . '-%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener competencias: " . $e->getMessage());
        }
    }

    public function insertarCompetencia($codigoDiseño, $datos) {
        try {
            $codigoDiseñoCompetencia = $codigoDiseño . '-' . $datos['codigoCompetencia'];
            
            $sql = "INSERT INTO competencias (codigoDiseñoCompetencia, codigoCompetencia, nombreCompetencia, 
                    normaUnidadCompetencia, horasDesarrolloCompetencia, requisitosAcademicosInstructor, 
                    experienciaLaboralInstructor) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $codigoDiseñoCompetencia, $datos['codigoCompetencia'], $datos['nombreCompetencia'],
                $datos['normaUnidadCompetencia'], $datos['horasDesarrolloCompetencia'],
                $datos['requisitosAcademicosInstructor'], $datos['experienciaLaboralInstructor']
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al insertar competencia: " . $e->getMessage());
        }
    }

    public function actualizarCompetencia($codigoDiseñoCompetencia, $datos) {
        try {
            $sql = "UPDATE competencias SET nombreCompetencia = ?, normaUnidadCompetencia = ?, 
                    horasDesarrolloCompetencia = ?, requisitosAcademicosInstructor = ?, 
                    experienciaLaboralInstructor = ? WHERE codigoDiseñoCompetencia = ?";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $datos['nombreCompetencia'], $datos['normaUnidadCompetencia'],
                $datos['horasDesarrolloCompetencia'], $datos['requisitosAcademicosInstructor'],
                $datos['experienciaLaboralInstructor'], $codigoDiseñoCompetencia
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar competencia: " . $e->getMessage());
        }
    }

    public function eliminarCompetencia($codigoDiseñoCompetencia) {
        try {
            // Primero eliminar RAPs relacionados
            $this->eliminarRapsPorCompetencia($codigoDiseñoCompetencia);
            // Luego eliminar la competencia
            $stmt = $this->conexion->prepare("DELETE FROM competencias WHERE codigoDiseñoCompetencia = ?");
            return $stmt->execute([$codigoDiseñoCompetencia]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar competencia: " . $e->getMessage());
        }
    }

    public function eliminarCompetenciasPorDiseño($codigoDiseño) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM competencias WHERE codigoDiseñoCompetencia LIKE ?");
            return $stmt->execute([$codigoDiseño . '-%']);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar competencias del diseño: " . $e->getMessage());
        }
    }

    // MÉTODOS PARA RAPS
    public function obtenerRapsPorCompetencia($codigoDiseñoCompetencia) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM raps WHERE codigoDiseñoCompetenciaRap LIKE ? ORDER BY codigoDiseñoCompetenciaRap");
            $stmt->execute([$codigoDiseñoCompetencia . '-%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener RAPs: " . $e->getMessage());
        }
    }

    public function insertarRap($codigoDiseñoCompetencia, $datos) {
        try {
            // Primero insertamos para obtener el codigoRapAutomatico
            $sql = "INSERT INTO raps (codigoRapDiseño, nombreRap, horasDesarrolloRap) 
                    VALUES (?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            $result = $stmt->execute([
                $datos['codigoRapDiseño'], 
                $datos['nombreRap'], 
                $datos['horasDesarrolloRap']
            ]);
            
            if ($result) {
                // Obtener el ID auto-generado
                $codigoRapAutomatico = $this->conexion->lastInsertId();
                
                // Generar el código completo
                $codigoDiseñoCompetenciaRap = $codigoDiseñoCompetencia . '-' . $codigoRapAutomatico;
                
                // Actualizar el registro con el código completo
                $sqlUpdate = "UPDATE raps SET codigoDiseñoCompetenciaRap = ? WHERE codigoRapAutomatico = ?";
                $stmtUpdate = $this->conexion->prepare($sqlUpdate);
                return $stmtUpdate->execute([$codigoDiseñoCompetenciaRap, $codigoRapAutomatico]);
            }
            
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al insertar RAP: " . $e->getMessage());
        }
    }

    public function actualizarRap($codigoDiseñoCompetenciaRap, $datos) {
        try {
            $sql = "UPDATE raps SET codigoRapDiseño = ?, nombreRap = ?, horasDesarrolloRap = ? 
                    WHERE codigoDiseñoCompetenciaRap = ?";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $datos['codigoRapDiseño'], 
                $datos['nombreRap'], 
                $datos['horasDesarrolloRap'], 
                $codigoDiseñoCompetenciaRap
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar RAP: " . $e->getMessage());
        }
    }

    public function eliminarRap($codigoDiseñoCompetenciaRap) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM raps WHERE codigoDiseñoCompetenciaRap = ?");
            return $stmt->execute([$codigoDiseñoCompetenciaRap]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar RAP: " . $e->getMessage());
        }
    }

    public function eliminarRapsPorCompetencia($codigoDiseñoCompetencia) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM raps WHERE codigoDiseñoCompetenciaRap LIKE ?");
            return $stmt->execute([$codigoDiseñoCompetencia . '-%']);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar RAPs de la competencia: " . $e->getMessage());
        }
    }

    public function eliminarRapsPorDiseño($codigoDiseño) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM raps WHERE codigoDiseñoCompetenciaRap LIKE ?");
            return $stmt->execute([$codigoDiseño . '-%']);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar RAPs del diseño: " . $e->getMessage());
        }
    }

    public function obtenerCompetenciaPorCodigo($codigoDiseñoCompetencia) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetencia = ?");
            $stmt->execute([$codigoDiseñoCompetencia]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener competencia: " . $e->getMessage());
        }
    }

    public function obtenerRapPorCodigo($codigoDiseñoCompetenciaRap) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM raps WHERE codigoDiseñoCompetenciaRap = ?");
            $stmt->execute([$codigoDiseñoCompetenciaRap]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener RAP: " . $e->getMessage());
        }
    }
}
?>
