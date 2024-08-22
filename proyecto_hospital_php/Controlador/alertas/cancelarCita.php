<?php
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Modelo/conexion.php';
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/CitaController.php';

header('Content-Type: application/json');

$citaController = new CitaController($conexion);
$response = [];

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_cita']) && isset($_POST['documento']) && isset($_POST['Motivocan'])) {
        $id_cita = trim($_POST['id_cita']);
        $documento = trim($_POST['documento']);
        $Motivocan = trim($_POST['Motivocan']);

        // Aquí puedes añadir la lógica para cancelar la cita
        $result = $citaController->cancelarCita($id_cita, $documento, $Motivocan);

        if ($result) {
            $response = ['success' => true, 'message' => 'Cita cancelada exitosamente.'];
        } else {
            $response = ['success' => false, 'message' => 'Error al cancelar la cita.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Datos faltantes en la solicitud.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Método de solicitud no válido.'];
}

echo json_encode($response);
$conexion->close();
?>
