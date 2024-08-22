<?php
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Modelo/conexion.php';
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/LoginAdminController.php';


header('Content-Type: application/json');

$loginController = new LoginAdminController($conexion);

$response = [];

session_start(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'forgot_password') {
            if (!empty($_POST['correo'])) {
                $response = $loginController->procesar_olvido_contrasena($_POST['correo']);
            } else {
                $response = ['status' => 'error', 'message' => 'Por favor, introduzca su correo electrónico.'];
            }
        } elseif ($_POST['action'] === 'reset_password') {
            if (!empty($_POST['token']) && !empty($_POST['nueva_contrasena'])) {
                $response = $loginController->restablecer_contrasena($_POST['token'], $_POST['nueva_contrasena']);
            } else {
                $response = ['status' => 'error', 'message' => 'Por favor, introduzca el token y la nueva contraseña.'];
            }
        } elseif (isset($_POST['correo']) && isset($_POST['contrasena'])) {
            $response = $loginController->procesar_login();
            if ($response['status'] === 'success') {
                $_SESSION['admin_id'] = $_SESSION['user_id'];
            }
        }
    }
}
echo json_encode($response);

$conexion->close();
?>
