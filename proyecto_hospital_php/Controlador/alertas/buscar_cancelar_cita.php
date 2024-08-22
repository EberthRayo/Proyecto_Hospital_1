<?php
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Modelo/conexion.php';
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/CitaController.php';

header('Content-Type: application/json');

$citaController = new CitaController($conexion);
$response = [];

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['documento']) && !isset($_POST['cancelar_cita'])) {
        $documento = trim($_POST['documento']);
        $response = $citaController->buscar_cancelarCita($documento);
    } else {
        $response = ['success' => false, 'message' => 'Datos faltantes en la solicitud.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Método de solicitud no válido.'];
}

echo json_encode($response);
$conexion->close();
?>
