<?php

require __DIR__ . '/../../EnvioCorreo/PHPMailer/src/Exception.php';
require __DIR__ . '/../../EnvioCorreo/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../EnvioCorreo/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviar_correo($correo, $asunto, $mensaje)
{
    $mail = new PHPMailer(true);
    $resultado = '';

    try {
        $mail->SMTPDebug = 0; // Desactiva la depuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'danicrg05@gmail.com'; 
        $mail->Password = 'oqbi utlj zkjp xgsg'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('crgd4006@gmail.com', 'InnovaSalud');
        $mail->addAddress($correo);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        $mail->send();
        $resultado = "";
        return true;
    } catch (Exception $e) {
        $resultado = "Mailer Error: {$mail->ErrorInfo}";
        return false;
    } finally {
        echo $resultado; // Opcional: puedes almacenar esto en una variable de sesión si quieres mostrar el mensaje en otra página.
    }
}

class JefeController
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function procesar_login()
    {
        if (!empty($_POST['correo']) && !empty($_POST['contrasena'])) {
            $Correo = $_POST['correo'];
            $Contraseña = $_POST['contrasena'];
            $Recuerdame = isset($_POST['recuerdame']) ? $_POST['recuerdame'] : '';

            $Correo = $this->conexion->real_escape_string($Correo);
            $Contraseña = $this->conexion->real_escape_string($Contraseña);

            $sql = "SELECT * FROM jefe_login WHERE email = '$Correo'";
            $result = $this->conexion->query($sql);

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($Contraseña, $user['contrasena'])) {
                    if ($Recuerdame) {
                        $token = bin2hex(random_bytes(16));
                        setcookie('recuerdame', $token, time() + (86400 * 30), "/");
                        $updateTokensql = "UPDATE jefe_login SET token = '$token' WHERE email= '$Correo'";
                        $this->conexion->query($updateTokensql);
                    }
                    // Mensaje de depuración
                    error_log("Login successful, redirecting...");
                    header("Location: /Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/dashboard_admin.php");
                    exit();
                } else {
                    return "Usuario o Contraseña Incorrecto";
                }
            } else {
                return "Usuario o Contraseña Incorrecto";
            }
        } else {
            return "Los datos estan Vacíos";
        }
    }

    public function verificar_recuerdame()
    {
        if (isset($_COOKIE['recuerdame'])){
            $token = $_COOKIE['recuerdame'];
            $token = $this->conexion->real_escape_string($token);
            $sql = "SELECT * FROM jefe_login WHERE token = '$token'";
            $result = $this->conexion->query($sql);
            if ($result && $result->num_rows > 0){
                return "Accedió correctamente mediante cookies";
            }
        }
        return "Cookie inválida o inexistente";
    }

    public function procesar_olvido_contrasena($correo)
    {
        $correo = $this->conexion->real_escape_string($correo);
        $sql = "SELECT * FROM jefe_login WHERE email = '$correo'";
        $result = $this->conexion->query($sql);

        if ($result && $result->num_rows > 0){
            $token = bin2hex(random_bytes(16));
            $updateTokensql = "UPDATE jefe_login SET reset_token = '$token', token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = '$correo'";
            $this->conexion->query($updateTokensql);

            $resetLink = "http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/reset_passwordJefe.php?token=$token";
            $asunto = "Reestablecer clave de inicio sesion del perfil Jefe inmediato";
            $mensaje = "Haga click en el siguiente enlace para reestablecer su contraseña: <a href='$resetLink'>$resetLink</a>";

            if (enviar_correo($correo, $asunto, $mensaje)){
                return "Correo de reestablecimiento enviado";
            } else {
                return "Error al enviar el correo";
            }
        } else {
            return "El correo digitado no ha sido encontrado, por favor ingresa un correo valido";
        }
    }

    public function restablecer_contrasena($token, $nueva_contrasena)
    {
        $token = $this->conexion->real_escape_string($token);
        $nueva_contrasena = $this->conexion->real_escape_string($nueva_contrasena);
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        $sql = "SELECT * FROM jefe_login WHERE reset_token = '$token' AND token_expiry > NOW()";
        $result = $this->conexion->query($sql);

        if ($result && $result->num_rows > 0){
            $updateTokensql = "UPDATE jefe_login SET contrasena= '$hashed_password', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
            $this->conexion->query($updateTokensql);
            return "Contraseña Reestablecida Correctamente";
        } else {
            return "Token inválido o expirado";
        }
    }
}

$loginJefe = new JefeController($conexion);
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['action']) && $_POST['action'] === 'forgot_password'){
        echo $loginJefe->procesar_olvido_contrasena($_POST['correo']);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset_password'){
        echo $loginJefe->restablecer_contrasena($_POST['token'], $_POST['nueva_contrasena']);
    }
}
?>
