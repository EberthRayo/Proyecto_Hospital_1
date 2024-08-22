<?php
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Modelo/conexion.php';
require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/LoginMedicoController.php';


header('Content-Type: application/json');

$loginMedicoController = new LoginMedicoController ($conexion);

$response =  [];

session_start();

if ($_SERVER["REQUEST_METHOD"] == 'POST'){
    if (isset($_POST['action']) === 'forgot_password'){
        if (!empty($_POST['correo'])){
            $response = $loginMedicoController->procesar_olvido_contrasena($_POST['correo']);
        } else {
            $response =  ['status' => 'error', 'message' => 'Por favor, Introduzca su correo electronico'];
        }
    } elseif ($_POST['action'] === 'reset_password') {
        if (!empty($_POST['token']) && !empty($_POST['nueva_contrasena'])){
            $response = $loginMedicoController->restablecer_contrasena($_POST['token'], $_POST['nueva_contrasena']);
        } else {
            $response = ['status' => 'error', 'message' => 'Por favor, introduzca  el token y la nueva contraseña. '];
        }
    } elseif (isset($_POST['correo']) && isset($_POST['contrasena'])){
        $response = $loginMedicoController->procesar_login();
        if ($response['status'] === 'success'){
            $_SESSION['medico_id'] = $_SESSION['user_id'];
        }
    }
}
echo json_encode($response);

$conexion->close();
?>
