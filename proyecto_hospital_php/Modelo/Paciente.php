<?php
include_once __DIR__ . '/../Modelo/conexion.php';

class Paciente {
    private $Identificacion;
    private $Tipo_documento;
    private $Nombre_completo;
    private $Genero;
    private $Direccion_Residencia;
    private $Fecha_Nacimiento;
    private $Numero_telefono;
    private $Correo_electronico;
    private $Cobertura;
    private $Eps;
    private $conexion;

    public function __construct($ID, $tdo, $Nom, $Gen, $Dir, $fec, $Num, $Cor, $Cob, $Eps, $conexion) {
        $this->Identificacion = $ID;
        $this->Tipo_documento = $tdo;
        $this->Nombre_completo = $Nom;
        $this->Genero = $Gen;
        $this->Direccion_Residencia = $Dir;
        $this->Fecha_Nacimiento = $fec;
        $this->Numero_telefono = $Num;
        $this->Correo_electronico = $Cor;
        $this->Cobertura = $Cob;
        $this->Eps = $Eps;
        
    }

    // Métodos getters
    public function obtenerIdentificacion() {
        return $this->Identificacion;
    }
    public function obtenertipo_documento() {
        return $this->Tipo_documento;
    }
    public function obtenerNombre() {
        return $this->Nombre_completo;
    }
    public function obtenerGenero() {
        return $this->Genero;
    }
    public function obtenerFechaN() {
        return $this->Fecha_Nacimiento;
    }
    public function obtenerTelefono() {
        return $this->Numero_telefono;
    }
    public function obtenerCorreo() {
        return $this->Correo_electronico;
    }
    public function obtenerDireccion() {
        return $this->Direccion_Residencia;
    }
    public function obtenerCobertura() {
        return $this->Cobertura;
    }
    public function obtenerEps() {
        return $this->Eps;
    }
    
}
?>
