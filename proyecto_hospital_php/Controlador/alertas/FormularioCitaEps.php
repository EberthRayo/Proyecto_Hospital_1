<?php
ob_start(); // Inicia la captura de la salida
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Modelo/conexion.php';
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/CitaController.php';

session_start();

if (!isset($conexion)) {
    $response = ['success' => false, 'message' => 'No se pudo conectar a la base de datos.'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$citaController = new CitaController($conexion);

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'procesar_cita') {
        if (!empty($_POST['documento'])) {
            try {
                $response = $citaController->procesarFormularioCitaEPS();
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => 'Ocurrió un error en el servidor: ' . $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'message' => 'Faltan datos en el formulario.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Acción no válida.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Método de solicitud no permitido.'];
}

ob_end_clean(); // Limpia la salida capturada y la descarta
echo json_encode($response);

$conexion->close();
exit;
