<?php

class Controlador
{
    private $conexion;

    public function __construct()
    {
        $conexionPath = __DIR__ . '/../Modelo/conexion.php';
        if (file_exists($conexionPath)) {
            include $conexionPath;
            $this->conexion = $conexion;
            if ($this->conexion->connect_error) {
                die('Conexión fallida: ' . $this->conexion->connect_error);
            }
        } else {
            die('Error: no se pudo encontrar el archivo de conexión.');
        }
    }

   

    public function verPagina($ruta)
    {
        require_once $ruta;
    }
}

?>
