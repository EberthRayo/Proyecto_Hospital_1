<?php
include __DIR__ . '/../Modelo/conexion.php';

class CitaConfirmacion {
        public function __construct($conexion)
        {
            $this->conexion = $conexion;
        }
        public function obtenerDatosCita($documento){

        $stmt = "SELECT pacientes.*,
            citas.ID_cita,
            citas.ID_Disponibilidad_fecha,
            citas.Area_salud,
            citas.Motivo,
            citas.Estado_Cita,
            fechahora_citas.Fecha_hora,
            pacientes.Correo_Electronico
        FROM pacientes 
        LEFT JOIN citas ON pacientes.ID_Paciente = citas.ID_Paciente
        LEFT JOIN fechahora_citas ON citas.ID_Disponibilidad_fecha = fechahora_citas.ID_Disponibilidad_fecha
        WHERE pacientes.ID_Paciente = '$documento'";
            
        $result = $conexion->query($stmt);

        if($result && $result->num_rows > 0){
            $data = [];
            while ($fila = $result->fetch_assoc()){
                $data[] = $fila;
            }
            return $data;
        } else{
            return false;
        }
        }
      
}

?>
