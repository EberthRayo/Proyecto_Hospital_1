<?php

require_once 'Controlador/CitaController.php';
require_once 'Controlador/Controlador.php';
require_once 'Modelo/conexion.php'; 

$citaController = new CitaController($conexion);
$controlador = new Controlador();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'registrar':
                $citaController->procesarFormularioCita($conexion);
                break;
            case 'buscar':
                $citaController->buscarCita();
                break;
            case 'cancelar':
                $citaController->cancelarCita();
                break;
            case 'buscar_cancelar':
                $citaController->buscar_cancelarCita();
                break;
            default:
                $controlador->verPagina('Vista/html/interfazdeusuario/index.php');
                break;
        }
    }
} else {
    $controlador->verPagina('Vista/html/interfazdeusuario/index.php');
}
?>
