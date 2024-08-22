<?php

class Reporte {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function obtenerCitasDelMes($mes, $anio) {
        // Consulta SQL actualizada para mysqli
        $query = "
            SELECT c.ID_cita, c.ID_Paciente, f.Fecha_hora, c.Asistencia, c.Area_salud, c.Motivo
            FROM citas AS c
            INNER JOIN fechahora_citas AS f ON c.ID_Disponibilidad_fecha = f.ID_Disponibilidad_fecha
            WHERE MONTH(f.Fecha_hora) = ? AND YEAR(f.Fecha_hora) = ?
        ";
        
        // Preparar la consulta
        if ($stmt = $this->db->prepare($query)) {
            // Vincular parámetros
            $stmt->bind_param("ii", $mes, $anio);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Obtener resultados
            $result = $stmt->get_result();
            
            // Fetch all results as an associative array
            $data = $result->fetch_all(MYSQLI_ASSOC);
            
            // Cerrar la declaración
            $stmt->close();
            
            return $data;
        } else {
            // Manejo de errores en caso de fallo en la preparación de la consulta
            throw new Exception("Error en la preparación de la consulta: " . $this->db->error);
        }
    }
    
    

    public function obtenerPacientesDelMes($mes, $anio) {
        $sql = "SELECT * FROM pacientes WHERE MONTH(fecha_registro) = ? AND YEAR(fecha_registro) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $mes, $anio); // Usa bind_param para mysqli
        $stmt->execute();
        $result = $stmt->get_result();
        $pacientes = $result->fetch_all(MYSQLI_ASSOC); // Usa fetch_all en mysqli

        // Depuración
        if (empty($pacientes)) {
            echo "No se encontraron pacientes.";
        } else {
            // Descomenta si necesitas ver los datos
             var_dump($pacientes);
        }

        return $pacientes;
    }

    public function contarCitasDelMes($mes, $anio) {
        $sql = "SELECT COUNT(*) AS total FROM citas WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $mes, $anio); // Usa bind_param para mysqli
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc(); // Usa fetch_assoc en mysqli
        return $total['total'];
    }

    public function contarPacientesDelMes($mes, $anio) {
        $sql = "SELECT COUNT(*) AS total FROM pacientes WHERE MONTH(fecha_registro) = ? AND YEAR(fecha_registro) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $mes, $anio); // Usa bind_param para mysqli
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc(); // Usa fetch_assoc en mysqli
        return $total['total'];
    }

    public function guardarReporte($ruta) {
        $sql = "INSERT INTO reportes (ruta, fecha_generacion) VALUES (?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $ruta); // Usa bind_param para mysqli
        $stmt->execute();
        $stmt->close();
    }

    public function obtenerReportes() {
        $sql = "SELECT * FROM reportes ORDER BY fecha_generacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $datos = $result->fetch_all(MYSQLI_ASSOC); // Usa fetch_all en mysqli
        $stmt->close();
        return $datos;
    }
}
?>