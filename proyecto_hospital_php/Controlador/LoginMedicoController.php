<?php

require __DIR__ . '/../../EnvioCorreo/PHPMailer/src/Exception.php';
require __DIR__ . '/../../EnvioCorreo/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../EnvioCorreo/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('enviar_correo')) {
    function enviar_correo($correo, $asunto, $mensaje)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0; 
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
            $mail->Body = $mensaje;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}

if (!class_exists('LoginMedicoController')) {
    class LoginMedicoController
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function procesar_login()
    {
        header('Content-type: application/json');

        if (!empty($_POST['correo']) && !empty($_POST['contrasena'])) {
            $Correo = $_POST['correo'];
            $Contraseña = $_POST['contrasena'];
            $Recuerdame = isset($_POST['recuerdame']) ? $_POST['recuerdame'] : '';

            if (!filter_var($Correo, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'message' => 'Correo Electrónico incorrecto']);
                exit();
            }

            $Correo = $this->conexion->real_escape_string($Correo);
            $Contraseña = $this->conexion->real_escape_string($Contraseña);

            $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE correo = ? AND tipo_usuario = 'Personal Medico'");
            $stmt->bind_param("s", $Correo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($Contraseña, $user['contrasena'])) {
                    date_default_timezone_set('America/Bogota');
                    $_SESSION['medico_id'] = $user['id'];
                    $_SESSION['tipo_usuario'] = 'Personal Medico';
                    $_SESSION['ultimo_login'] = date('Y-m-d h:i:s A');

                    $hora_actual = date("Y-m-d h:i:s A");
                    $updateLoginTimeSql = "UPDATE usuarios SET ultimo_login = ? WHERE correo = ?";
                    $updateStmt = $this->conexion->prepare($updateLoginTimeSql);
                    $updateStmt->bind_param("ss", $hora_actual, $Correo);
                    $updateStmt->execute();

                    if ($Recuerdame) {
                        $token = bin2hex(random_bytes(16));
                        setcookie('recuerdame', $token, time() + (86400 * 30), "/");
                        $updateTokenSql = "UPDATE usuarios SET token = ? WHERE correo = ?";
                        $updateTokenStmt = $this->conexion->prepare($updateTokenSql);
                        $updateTokenStmt->bind_param("ss", $token, $Correo);
                        $updateTokenStmt->execute();
                    }

                    $_SESSION['hora_inicio_sesion'] = time();

                    echo json_encode(['status' => 'success', 'message' => 'Accedió Correctamente']);
                    exit();
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Usuario o Contraseña Incorrecta']);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Su usuario y contraseña no se encuentran en nuestra base de datos, necesito un permiso de administrador para ingresar']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Los datos están vacíos']);
            exit();
        }
    }

    public function verificar_recuerdame()
    {
        if (isset($_COOKIE['recuerdame'])) {
            $token = $_COOKIE['recuerdame'];
            $token = $this->conexion->real_escape_string($token);
            $sql = "SELECT * FROM usuarios WHERE token = '$token'";
            $result = $this->conexion->query($sql);
            if ($result && $result->num_rows > 0) {
                return "Accedió correctamente mediante cookie";
            }
        }
        return "Cookie inválida o inexistente";
    }

    public function procesar_olvido_contrasena($correo)
    {
        $correo = $this->conexion->real_escape_string($correo);
        $sql = "SELECT * FROM usuarios WHERE correo = '$correo' AND tipo_usuario = 'Personal Medico'";
        $result = $this->conexion->query($sql);

        if ($result && $result->num_rows > 0) {
            $token = bin2hex(random_bytes(16));
            $updateTokenSql = "UPDATE usuarios SET reset_token = '$token', token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE correo = '$correo'";
            $this->conexion->query($updateTokenSql);

            $resetLink = "http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/reset_passwordM.php?token=$token";
            $asunto = "Reestablecer Clave de inicio sesión del perfil Medico";
            $mensaje = "Haga click en el siguiente enlace para reestablecer su contraseña: <a href='$resetLink'>$resetLink</a>";

            if (enviar_correo($correo, $asunto, $mensaje)) {
                return "Correo de reestablecimiento enviado, revise su bandeja de entrada";
            } else {
                return "Error al enviar el correo";
            }
        } else {
            return "Correo no encontrado";
        }
    }

    public function restablecer_contrasena($token, $nueva_contrasena)
    {
        if(!$this->validar_contraseña($nueva_contrasena)){
            return "La contraseña no cumple con los requisitos";
        }
        $token = $this->conexion->real_escape_string($token);
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        $sql = "SELECT * FROM usuarios WHERE reset_token = ? AND token_expiry > NOW()";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt){
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0){
                $updatePassSql = "UPDATE usuarios SET contrasena = ? WHERE reset_token = ?";
                $stmtUpdate = $this->conexion->prepare($updatePassSql);
                if ($stmtUpdate){
                    $stmtUpdate->bind_param("ss", $hashed_password, $token);
                    $stmtUpdate->execute();
                    if ($stmtUpdate->affected_rows > 0){
                        $this->limpiar_token($token);
                        return "Contraseña Reestablecida Correctamente";
                    } else{
                        return "Error al reestablecer la contraseña";
                    }
                } else {
                    return "Error en la Preparación de la consulta de actualización";
                }
            } else {
                return "Token inválido o expirado";
            }
        } else {
            return "Error en la preparación de la consulta";
        }
    }    

    public function validar_contraseña($contrasena)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!#%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $contrasena);
    } 

    private function limpiar_token($token)
    {
        $updateTokenSql = "UPDATE usuarios SET reset_token = NULL, token_expiry = NULL WHERE reset_token = ?";
        $stmt = $this->conexion->prepare($updateTokenSql);
        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
        }
    }

    public function obtenerDatosUsuarioMedico($conexion, $medico_id)
    {
        $datosUsuarioMedico = array();
        $sql = "SELECT nombre, foto_perfil, tipo_usuario, correo, horario_trabajo, Descripcion_profesional FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            die('Error de preparación de la consulta: ' . $conexion->error);
        }

        $stmt->bind_param("i", $medico_id);
        $stmt->execute();

        if ($stmt->errno) {
            die('Error en la ejecución de la consulta: ' . $stmt->error);
        }

        $stmt->bind_result($username, $profile_photo, $role, $email, $work_schedule, $descripcion);

        if ($stmt->fetch()) {
            $datosUsuarioMedico['nombre'] = $username;
            $datosUsuarioMedico['foto_perfil'] = $profile_photo;
            $datosUsuarioMedico['tipo_usuario'] = $role;
            $datosUsuarioMedico['correo'] = $email;
            $datosUsuarioMedico['horario_trabajo'] = $work_schedule;
            $datosUsuarioMedico['Descripcion_profesional'] = $descripcion;
        } else {
            $datosUsuarioMedico['nombre'] = '';
            $datosUsuarioMedico['foto_perfil'] = '';
            $datosUsuarioMedico['tipo_usuario'] = '';
        }

        $stmt->close();

        return $datosUsuarioMedico;
    }
}
}
?>
