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

    public function obtenerDiseñosConPaginacion($pagina = 1, $registros_por_pagina = 10, $filtros = []) {
        try {
            $offset = ($pagina - 1) * $registros_por_pagina;
            
            $where_conditions = [];
            $params = [];
            
            // Aplicar filtros basados en las columnas reales
            if (!empty($filtros['busqueda'])) {
                $where_conditions[] = "(nombrePrograma LIKE ? OR codigoPrograma LIKE ? OR codigoDiseño LIKE ? OR redTecnologica LIKE ?)";
                $params[] = '%' . $filtros['busqueda'] . '%';
                $params[] = '%' . $filtros['busqueda'] . '%';
                $params[] = '%' . $filtros['busqueda'] . '%';
                $params[] = '%' . $filtros['busqueda'] . '%';
            }
            
            // Filtro por horas de desarrollo (campo real: horasDesarrolloDiseño)
            if (!empty($filtros['horas_min'])) {
                $where_conditions[] = "horasDesarrolloDiseño >= ?";
                $params[] = (float)$filtros['horas_min'];
            }
            
            if (!empty($filtros['horas_max'])) {
                $where_conditions[] = "horasDesarrolloDiseño <= ?";
                $params[] = (float)$filtros['horas_max'];
            }
            
            // Filtro por meses de desarrollo (campo real: mesesDesarrolloPrograma)
            if (!empty($filtros['meses_min'])) {
                $where_conditions[] = "mesesDesarrolloPrograma >= ?";
                $params[] = (int)$filtros['meses_min'];
            }
            
            if (!empty($filtros['meses_max'])) {
                $where_conditions[] = "mesesDesarrolloPrograma <= ?";
                $params[] = (int)$filtros['meses_max'];
            }
            
            // Filtro por red tecnológica (campo real: redTecnologica)
            if (!empty($filtros['red_tecnologica'])) {
                $where_conditions[] = "redTecnologica LIKE ?";
                $params[] = '%' . $filtros['red_tecnologica'] . '%';
            }
            
            // Filtro por nivel académico de ingreso (campo real: nivelAcademicoIngreso)
            if (!empty($filtros['nivel_academico'])) {
                $where_conditions[] = "nivelAcademicoIngreso LIKE ?";
                $params[] = '%' . $filtros['nivel_academico'] . '%';
            }
            
            $where_clause = '';
            if (!empty($where_conditions)) {
                $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
            }
            
            // Consulta para contar total de registros
            $count_sql = "SELECT COUNT(*) as total FROM diseños" . $where_clause;
            $count_stmt = $this->conexion->prepare($count_sql);
            $count_stmt->execute($params);
            $total_registros = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Consulta principal con paginación (LIMIT y OFFSET deben ser valores directos)
            $sql = "SELECT * FROM diseños" . $where_clause . " ORDER BY codigoDiseño DESC LIMIT " . (int)$registros_por_pagina . " OFFSET " . (int)$offset;
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'datos' => $datos,
                'total_registros' => $total_registros,
                'pagina_actual' => $pagina,
                'registros_por_pagina' => $registros_por_pagina,
                'total_paginas' => ceil($total_registros / $registros_por_pagina)
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener diseños con paginación: " . $e->getMessage());
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

            // Función auxiliar para convertir valores vacíos a números
            $convertirANumero = function($valor) {
                return (empty($valor) || $valor === '') ? 0 : (float)$valor;
            };

            // Convertir campos numéricos
            $horasLectiva = $convertirANumero($datos['horasDesarrolloLectiva'] ?? '');
            $horasProductiva = $convertirANumero($datos['horasDesarrolloProductiva'] ?? '');
            $mesesLectiva = $convertirANumero($datos['mesesDesarrolloLectiva'] ?? '');
            $mesesProductiva = $convertirANumero($datos['mesesDesarrolloProductiva'] ?? '');

            // Calcular campos derivados
            $horasDesarrolloDiseño = $horasLectiva + $horasProductiva;
            $mesesDesarrolloDiseño = $mesesLectiva + $mesesProductiva;

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
                $horasLectiva, $horasProductiva,
                $mesesLectiva, $mesesProductiva,
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
            // Detectar si es modo completar (solo actualizar campos específicos)
            $modoCompletar = isset($datos['completar_modo']) && $datos['completar_modo'] == '1';

            if ($modoCompletar) {
                return $this->actualizarDiseñoCompletar($codigoDiseño, $datos);
            }

            // Función auxiliar para convertir valores vacíos a números
            $convertirANumero = function($valor) {
                return (empty($valor) || $valor === '') ? 0 : (float)$valor;
            };

            // Convertir campos numéricos
            $horasLectiva = $convertirANumero($datos['horasDesarrolloLectiva'] ?? '');
            $horasProductiva = $convertirANumero($datos['horasDesarrolloProductiva'] ?? '');
            $mesesLectiva = $convertirANumero($datos['mesesDesarrolloLectiva'] ?? '');
            $mesesProductiva = $convertirANumero($datos['mesesDesarrolloProductiva'] ?? '');

            // Calcular campos derivados
            $horasDesarrolloDiseño = $horasLectiva + $horasProductiva;
            $mesesDesarrolloDiseño = $mesesLectiva + $mesesProductiva;

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
                $horasLectiva, $horasProductiva,
                $mesesLectiva, $mesesProductiva,
                $horasDesarrolloDiseño, $mesesDesarrolloDiseño, $datos['nivelAcademicoIngreso'],
                $datos['gradoNivelAcademico'], $datos['formacionTrabajoDesarrolloHumano'],
                $datos['edadMinima'], $datos['requisitosAdicionales'], $codigoDiseño
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar diseño: " . $e->getMessage());
        }
    }

    public function actualizarDiseñoCompletar($codigoDiseño, $datos) {
        try {
            // Función auxiliar para convertir valores
            $convertirANumero = function($valor) {
                // Si está vacío, es null, o es string vacío, retornar null
                if ($valor === null || $valor === '' || (is_string($valor) && trim($valor) === '')) {
                    return null;
                }
                // Si es un número válido, convertirlo a float
                $numero = floatval($valor);
                return $numero > 0 ? $numero : null;
            };

            // Construir dinámicamente la consulta SQL para actualizar solo los campos enviados
            $camposActualizar = [];
            $valores = [];

            // Campos de texto
            $camposTexto = ['lineaTecnologica', 'redTecnologica', 'redConocimiento', 
                           'nivelAcademicoIngreso', 'formacionTrabajoDesarrolloHumano', 'requisitosAdicionales'];

            foreach ($camposTexto as $campo) {
                if (isset($datos[$campo])) {
                    $valor = trim($datos[$campo]);
                    $camposActualizar[] = "$campo = ?";
                    $valores[] = $valor !== '' ? $valor : null;
                }
            }

            // Campos numéricos
            $camposNumericos = ['gradoNivelAcademico', 'edadMinima'];

            foreach ($camposNumericos as $campo) {
                if (isset($datos[$campo])) {
                    $valor = $convertirANumero($datos[$campo]);
                    $camposActualizar[] = "$campo = ?";
                    $valores[] = $valor;
                }
            }

            // Manejar campos de desarrollo (horas o meses)
            $horasLectiva = $convertirANumero($datos['horasDesarrolloLectiva'] ?? '');
            $horasProductiva = $convertirANumero($datos['horasDesarrolloProductiva'] ?? '');
            $mesesLectiva = $convertirANumero($datos['mesesDesarrolloLectiva'] ?? '');
            $mesesProductiva = $convertirANumero($datos['mesesDesarrolloProductiva'] ?? '');

            // Verificar qué opción se está usando
            $usandoHoras = ($horasLectiva !== null && $horasProductiva !== null);
            $usandoMeses = ($mesesLectiva !== null && $mesesProductiva !== null);

            if ($usandoHoras) {
                // Usuario eligió horas, actualizar horas y limpiar meses
                $camposActualizar[] = "horasDesarrolloLectiva = ?";
                $valores[] = $horasLectiva;
                $camposActualizar[] = "horasDesarrolloProductiva = ?";
                $valores[] = $horasProductiva;
                $camposActualizar[] = "horasDesarrolloDiseño = ?";
                $valores[] = $horasLectiva + $horasProductiva;

                // Limpiar campos de meses (poner NULL)
                $camposActualizar[] = "mesesDesarrolloLectiva = NULL";
                $camposActualizar[] = "mesesDesarrolloProductiva = NULL";
                $camposActualizar[] = "mesesDesarrolloDiseño = NULL";

            } elseif ($usandoMeses) {
                // Usuario eligió meses, actualizar meses y limpiar horas
                $camposActualizar[] = "mesesDesarrolloLectiva = ?";
                $valores[] = $mesesLectiva;
                $camposActualizar[] = "mesesDesarrolloProductiva = ?";
                $valores[] = $mesesProductiva;
                $camposActualizar[] = "mesesDesarrolloDiseño = ?";
                $valores[] = $mesesLectiva + $mesesProductiva;

                // Limpiar campos de horas (poner NULL)
                $camposActualizar[] = "horasDesarrolloLectiva = NULL";
                $camposActualizar[] = "horasDesarrolloProductiva = NULL";
                $camposActualizar[] = "horasDesarrolloDiseño = NULL";
            }

            // Si no hay campos para actualizar, retornar true
            if (empty($camposActualizar)) {
                return true;
            }

            // Construir y ejecutar la consulta
            $sql = "UPDATE diseños SET " . implode(', ', $camposActualizar) . " WHERE codigoDiseño = ?";
            $valores[] = $codigoDiseño;

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute($valores);

        } catch (PDOException $e) {
            throw new Exception("Error al completar diseño: " . $e->getMessage());
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
            $stmt = $this->conexion->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetenciaReporte LIKE ? ORDER BY codigoDiseñoCompetenciaReporte");
            $stmt->execute([$codigoDiseño . '-%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener competencias: " . $e->getMessage());
        }
    }

    public function obtenerCompetenciasConPaginacion($codigoDiseño, $pagina = 1, $registros_por_pagina = 10, $filtros = []) {
        try {
            $offset = ($pagina - 1) * $registros_por_pagina;
            
            $where_conditions = ["codigoDiseñoCompetenciaReporte LIKE ?"];
            $params = [$codigoDiseño . '-%'];
            
            // Aplicar filtros
            if (!empty($filtros['busqueda'])) {
                $where_conditions[] = "(nombreCompetencia LIKE ? OR codigoCompetenciaReporte LIKE ?)";
                $params[] = '%' . $filtros['busqueda'] . '%';
                $params[] = '%' . $filtros['busqueda'] . '%';
            }
            
            if (!empty($filtros['horas_min'])) {
                $where_conditions[] = "horasDesarrolloCompetencia >= ?";
                $params[] = (float)$filtros['horas_min'];
            }
            
            if (!empty($filtros['horas_max'])) {
                $where_conditions[] = "horasDesarrolloCompetencia <= ?";
                $params[] = (float)$filtros['horas_max'];
            }
            
            $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
            
            // Consulta para contar total de registros
            $count_sql = "SELECT COUNT(*) as total FROM competencias" . $where_clause;
            $count_stmt = $this->conexion->prepare($count_sql);
            $count_stmt->execute($params);
            $total_registros = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Consulta principal con paginación (LIMIT y OFFSET deben ser valores directos)
            $sql = "SELECT * FROM competencias" . $where_clause . " ORDER BY codigoDiseñoCompetenciaReporte LIMIT " . (int)$registros_por_pagina . " OFFSET " . (int)$offset;
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'datos' => $datos,
                'total_registros' => $total_registros,
                'pagina_actual' => $pagina,
                'registros_por_pagina' => $registros_por_pagina,
                'total_paginas' => ceil($total_registros / $registros_por_pagina)
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener competencias con paginación: " . $e->getMessage());
        }
    }

    public function insertarCompetencia($codigoDiseño, $datos) {
        try {
            // Función auxiliar para convertir valores vacíos a números
            $convertirANumero = function($valor) {
                return (empty($valor) || $valor === '') ? 0 : (float)$valor;
            };

            // Convertir campo numérico
            $horasDesarrolloCompetencia = $convertirANumero($datos['horasDesarrolloCompetencia'] ?? '');

            $codigoDiseñoCompetenciaReporte = $codigoDiseño . '-' . $datos['codigoCompetenciaReporte'];

            $sql = "INSERT INTO competencias (codigoDiseñoCompetenciaReporte, codigoCompetenciaReporte, codigoCompetenciaPDF, nombreCompetencia, 
                    normaUnidadCompetencia, horasDesarrolloCompetencia, requisitosAcademicosInstructor, 
                    experienciaLaboralInstructor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $codigoDiseñoCompetenciaReporte, $datos['codigoCompetenciaReporte'], $datos['codigoCompetenciaPDF'] ?? null, $datos['nombreCompetencia'],
                $datos['normaUnidadCompetencia'], $horasDesarrolloCompetencia,
                $datos['requisitosAcademicosInstructor'], $datos['experienciaLaboralInstructor']
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al insertar competencia: " . $e->getMessage());
        }
    }

    public function actualizarCompetencia($codigoDiseñoCompetenciaReporte, $datos) {
        try {
            // Función auxiliar para convertir valores vacíos a números
            $convertirANumero = function($valor) {
                return (empty($valor) || $valor === '') ? 0 : (float)$valor;
            };

            // Convertir campo numérico
            $horasDesarrolloCompetencia = $convertirANumero($datos['horasDesarrolloCompetencia'] ?? '');

            $sql = "UPDATE competencias SET codigoCompetenciaReporte = ?, codigoCompetenciaPDF = ?, nombreCompetencia = ?, normaUnidadCompetencia = ?, 
                    horasDesarrolloCompetencia = ?, requisitosAcademicosInstructor = ?, 
                    experienciaLaboralInstructor = ? WHERE codigoDiseñoCompetenciaReporte = ?";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $datos['codigoCompetenciaReporte'], $datos['codigoCompetenciaPDF'] ?? null, $datos['nombreCompetencia'], $datos['normaUnidadCompetencia'],
                $horasDesarrolloCompetencia, $datos['requisitosAcademicosInstructor'],
                $datos['experienciaLaboralInstructor'], $codigoDiseñoCompetenciaReporte
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar competencia: " . $e->getMessage());
        }
    }

    public function eliminarCompetencia($codigoDiseñoCompetenciaReporte) {
        try {
            // Primero eliminar RAPs relacionados
            $this->eliminarRapsPorCompetencia($codigoDiseñoCompetenciaReporte);
            // Luego eliminar la competencia
            $stmt = $this->conexion->prepare("DELETE FROM competencias WHERE codigoDiseñoCompetenciaReporte = ?");
            return $stmt->execute([$codigoDiseñoCompetenciaReporte]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar competencia: " . $e->getMessage());
        }
    }

    public function eliminarCompetenciasPorDiseño($codigoDiseño) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM competencias WHERE codigoDiseñoCompetenciaReporte LIKE ?");
            return $stmt->execute([$codigoDiseño . '-%']);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar competencias del diseño: " . $e->getMessage());
        }
    }

    // MÉTODOS PARA RAPS
    public function obtenerRapsPorCompetencia($codigoDiseñoCompetenciaReporte) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM raps WHERE codigoDiseñoCompetenciaReporteRap LIKE ? ORDER BY codigoDiseñoCompetenciaReporteRap");
            $stmt->execute([$codigoDiseñoCompetenciaReporte . '-%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener RAPs: " . $e->getMessage());
        }
    }

    public function obtenerRapsConPaginacion($codigoDiseñoCompetenciaReporte, $pagina = 1, $registros_por_pagina = 10, $filtros = []) {
        try {
            $offset = ($pagina - 1) * $registros_por_pagina;
            
            $where_conditions = ["codigoDiseñoCompetenciaReporteRap LIKE ?"];
            $params = [$codigoDiseñoCompetenciaReporte . '-%'];
            
            // Aplicar filtros
            if (!empty($filtros['busqueda'])) {
                $where_conditions[] = "(nombreRap LIKE ? OR codigoRapReporte LIKE ?)";
                $params[] = '%' . $filtros['busqueda'] . '%';
                $params[] = '%' . $filtros['busqueda'] . '%';
            }
            
            if (!empty($filtros['horas_min'])) {
                $where_conditions[] = "horasDesarrolloRap >= ?";
                $params[] = (float)$filtros['horas_min'];
            }
            
            if (!empty($filtros['horas_max'])) {
                $where_conditions[] = "horasDesarrolloRap <= ?";
                $params[] = (float)$filtros['horas_max'];
            }
            
            $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
            
            // Consulta para contar total de registros
            $count_sql = "SELECT COUNT(*) as total FROM raps" . $where_clause;
            $count_stmt = $this->conexion->prepare($count_sql);
            $count_stmt->execute($params);
            $total_registros = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Consulta principal con paginación (LIMIT y OFFSET deben ser valores directos)
            $sql = "SELECT * FROM raps" . $where_clause . " ORDER BY codigoDiseñoCompetenciaReporteRap LIMIT " . (int)$registros_por_pagina . " OFFSET " . (int)$offset;
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'datos' => $datos,
                'total_registros' => $total_registros,
                'pagina_actual' => $pagina,
                'registros_por_pagina' => $registros_por_pagina,
                'total_paginas' => ceil($total_registros / $registros_por_pagina)
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener RAPs con paginación: " . $e->getMessage());
        }
    }

    public function insertarRap($codigoDiseñoCompetenciaReporte, $datos) {
        try {
            // Función auxiliar para convertir valores vacíos a números
            $convertirANumero = function($valor) {
                return (empty($valor) || $valor === '') ? 0 : (float)$valor;
            };

            // Convertir campo numérico
            $horasDesarrolloRap = $convertirANumero($datos['horasDesarrolloRap'] ?? '');

            // Generar el código completo del RAP
            $codigoDiseñoCompetenciaReporteRap = $codigoDiseñoCompetenciaReporte . '-' . $datos['codigoRapReporte'];

            $sql = "INSERT INTO raps (codigoDiseñoCompetenciaReporteRap, codigoRapReporte, nombreRap, horasDesarrolloRap) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $codigoDiseñoCompetenciaReporteRap,
                $datos['codigoRapReporte'], 
                $datos['nombreRap'], 
                $horasDesarrolloRap
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al insertar RAP: " . $e->getMessage());
        }
    }

    public function actualizarRap($codigoDiseñoCompetenciaReporteRap, $datos) {
        try {
            // Función auxiliar para convertir valores vacíos a números
            $convertirANumero = function($valor) {
                return (empty($valor) || $valor === '') ? 0 : (float)$valor;
            };

            // Convertir campo numérico
            $horasDesarrolloRap = $convertirANumero($datos['horasDesarrolloRap'] ?? '');

            $sql = "UPDATE raps SET codigoRapReporte = ?, nombreRap = ?, horasDesarrolloRap = ? 
                    WHERE codigoDiseñoCompetenciaReporteRap = ?";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $datos['codigoRapReporte'], 
                $datos['nombreRap'], 
                $horasDesarrolloRap, 
                $codigoDiseñoCompetenciaReporteRap
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar RAP: " . $e->getMessage());
        }
    }

    public function eliminarRap($codigoDiseñoCompetenciaReporteRap) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM raps WHERE codigoDiseñoCompetenciaReporteRap = ?");
            return $stmt->execute([$codigoDiseñoCompetenciaReporteRap]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar RAP: " . $e->getMessage());
        }
    }

    public function eliminarRapsPorCompetencia($codigoDiseñoCompetenciaReporte) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM raps WHERE codigoDiseñoCompetenciaReporteRap LIKE ?");
            return $stmt->execute([$codigoDiseñoCompetenciaReporte . '-%']);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar RAPs de la competencia: " . $e->getMessage());
        }
    }

    public function eliminarRapsPorDiseño($codigoDiseño) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM raps WHERE codigoDiseñoCompetenciaReporteRap LIKE ?");
            return $stmt->execute([$codigoDiseño . '-%']);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar RAPs del diseño: " . $e->getMessage());
        }
    }

    public function obtenerCompetenciaPorCodigo($codigoDiseñoCompetenciaReporte) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetenciaReporte = ?");
            $stmt->execute([$codigoDiseñoCompetenciaReporte]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener competencia: " . $e->getMessage());
        }
    }

    public function obtenerRapPorCodigo($codigoDiseñoCompetenciaReporteRap) {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM raps WHERE codigoDiseñoCompetenciaReporteRap = ?");
            $stmt->execute([$codigoDiseñoCompetenciaReporteRap]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener RAP: " . $e->getMessage());
        }
    }

    // Método auxiliar para ejecutar consultas personalizadas
    public function ejecutarConsulta($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al ejecutar consulta: " . $e->getMessage());
        }
    }
}
?>
