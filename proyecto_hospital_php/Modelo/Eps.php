<?php
class Eps{
    private $Numero_afiliacion;
    private $Entidad_promotora_salud;
    private $Identificacion_Paciente;
    private $plan_salud;

    public function __construct($Nua, $EPS, $IDP, $Pls){
        $this->Numero_afiliacion = $Nua;
        $this->Entidad_promotora_salud = $EPS;
        $this->Identificacion_Paciente = $IDP;
        $this->plan_salud = $Pls;
    }
    public function obtenerNumeroAfiliacion(){
        return $this->Numero_afiliacion;
    }
    public function obtenerEntidadPromotoraSalud(){
        return $this->Entidad_promotora_salud;
    }
    public function obtenerIdentificacionPaciente(){
        return $this->Identificacion_Paciente;
    }
    public function obtenerPlanSalud(){
        return $this->plan_salud;
    }


}


?>