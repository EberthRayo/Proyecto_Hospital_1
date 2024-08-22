<?php
class Medico{
    private $Identificacion_Medico;
    private $Nombres;
    private $Apellidos;
    private $ID_Especialidad_Medico;
    private $Horario_trabajo;
    private $Telefono;
    private $Correo;
    private $Consultorio;
    private $Estado_disponibilidad;

    public function __construct($IDM, $Nom, $Ape, $IDE, $Hort, $tel, $cor, $con, $Esd){
        $this->Identificacion_Medico = $IDM;
        $this->Nombres = $Nom;
        $this->Apellidos = $Ape;
        $this->ID_Especialidad_Medico = $IDM;
        $this->Horario_trabajo = $Hort;
        $this->Telefono = $tel;
        $this->Correo = $cor;
        $this->Consultorio = $con;
        $this->Estado_disponibilidad = $Esd;
    }

    public function obtenerIdentificacionMedico(){
        return $this->Identificacion_Medico;
    }
    public function obtenerNombres(){
        return $this->Nombres;
    }
    public function obtenerApellidos(){
        return $this->Apellidos;
    } 
    public function obtenerIdEspecialidadMedico(){
        return $this->ID_Especialidad_Medico;
    }
    public function obtenerHorarioTrabajo(){
        return $this->Horario_trabajo;
    }
    public function obtenerTelefono(){
        return $this->Telefono;
    }
   public function obtenerCorreo(){
    return $this->Correo;
   }
   public function obtenerConsultorio(){
    return $this->Consultorio;
   }
   public function obtenerEstadoDisponibilidad(){
    return $this->Estado_disponibilidad;
   }
}
?>