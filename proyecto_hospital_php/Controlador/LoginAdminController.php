<?php

require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/SMTP.php';
require_once __DIR__ . '../../Modelo/conexion.php';
require_once __DIR__ . '../../Controlador/AuthMiddlewareAdmin.php';

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

if (!class_exists('LoginAdminController')) {
class LoginAdminController
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }
    public function procesar_login()
{
    header('Content-Type: application/json');
    
    // Validar y sanitizar entrada
    if (!empty($_POST['correo']) && !empty($_POST['contrasena'])) {
        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);
        $recuerdame = isset($_POST['recuerdame']) ? $_POST['recuerdame'] : '';

        // Validar correo electrónico
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Correo electrónico inválido']);
            exit();
        }

        // Sanitizar entrada
        $correo = $this->conexion->real_escape_string($correo);
        $contrasena = $this->conexion->real_escape_string($contrasena);

        // Consultar la base de datos
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE correo = ? AND tipo_usuario = 'administrador'");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verificar la contraseña
            if (password_verify($contrasena, $user['contrasena'])) {
                date_default_timezone_set('America/Bogota');
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['tipo_usuario'] = 'administrador';
                $_SESSION['ultimo_login'] = date('Y-m-d h:i:s A'); 

                // Actualizar el último inicio de sesión
                $hora_actual = date("Y-m-d h:i:s A");
                $updateLoginTimeSql = "UPDATE usuarios SET ultimo_login = ? WHERE correo = ?";
                $updateStmt = $this->conexion->prepare($updateLoginTimeSql);
                $updateStmt->bind_param("ss", $hora_actual, $correo);
                $updateStmt->execute();

                // Manejo de la opción "Recuérdame"
                if ($recuerdame) {
                    $token = bin2hex(random_bytes(16));
                    setcookie('recuerdame', $token, time() + (86400 * 30), "/");
                    $updateTokenSql = "UPDATE usuarios SET token = ? WHERE correo = ?";
                    $updateTokenStmt = $this->conexion->prepare($updateTokenSql);
                    $updateTokenStmt->bind_param("ss", $token, $correo);
                    $updateTokenStmt->execute();
                }

                $_SESSION['hora_inicio_sesion'] = time();

                // Generar respuesta JSON
                echo json_encode(['status' => 'success', 'message' => 'Accedió correctamente']);
                exit();
            } else {
                // Mensaje de error en caso de contraseña incorrecta
                echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrecto']);
                exit();
            }
        } else {
            // Mensaje de error en caso de usuario no encontrado
            echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrecto']);
            exit();
        }
    } else {
        // Mensaje de error en caso de datos vacíos
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
        $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $result = $this->conexion->query($sql);

        if ($result && $result->num_rows > 0) {
            $token = bin2hex(random_bytes(16));
            $updateTokenSql = "UPDATE usuarios SET reset_token = '$token', token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE correo = '$correo'";
            $this->conexion->query($updateTokenSql);

            $resetLink = "http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/reset_password.php?token=$token";
            $asunto = "Reestablecer clave de inicio sesion del perfil Administrador";
            $mensaje = "Haga click en el siguiente enlace para restablecer su contraseña: <a href='$resetLink'>$resetLink</a>";

            if (enviar_correo($correo, $asunto, $mensaje)) {
                return "Correo de restablecimiento enviado";
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

        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $updatePassSql = "UPDATE usuarios SET contrasena = ? WHERE reset_token = ?";
                $stmtUpdate = $this->conexion->prepare($updatePassSql);
                if ($stmtUpdate) {
                    $stmtUpdate->bind_param("ss", $hashed_password, $token);
                    $stmtUpdate->execute();
                    if ($stmtUpdate->affected_rows > 0) {
                        $this->limpiar_token($token);
                        return "Contraseña restablecida correctamente";
                    } else {
                        return "Error al restablecer la contraseña";
                    }
                } else {
                    return "Error en la preparación de la consulta de actualización";
                }
            } else {
                return "Token inválido o expirado";
            }
        } else {
            return "Error en la preparación de la consulta";
        }
    }

    private function validar_contraseña($contrasena) {
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

    function obtenerDatosUsuarioAdmin($conexion, $usuario_id) {
        $datosUsuario = array();
        $sql = "SELECT nombre, foto_perfil, tipo_usuario, correo, horario_trabajo FROM usuarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
    
        if ($stmt === false) {
            die('Error de preparación de la consulta: ' . $conexion->error);
        }
    
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
    
        if ($stmt->errno) {
            die('Error en la ejecución de la consulta: ' . $stmt->error);
        }
    
        $stmt->bind_result($nombreUsuario, $fotoPerfil, $rol, $correo, $horario);
    
        if ($stmt->fetch()) {
            $datosUsuario['nombre'] = $nombreUsuario;
            $datosUsuario['foto_perfil'] = $fotoPerfil;
            $datosUsuario['tipo_usuario'] = $rol;
            $datosUsuario['correo'] = $correo;
            $datosUsuario['horario_trabajo'] = $horario;
        } else {
            $datosUsuario['nombre'] = '';
            $datosUsuario['foto_perfil'] = '';
            $datosUsuario['tipo_usuario'] = '';
        }
    
        $stmt->close();
    
        return $datosUsuario;
    }
    
    public function actualizar_perfil_admin($datos_usuario){
        $stmt = $this->conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, Descripcion_profesional = ?, horario_trabajo = ?, foto_perfil = ? WHERE id = ?");

        if ($stmt === false){
            echo "Error al actualizar los datos de usuario: ". $this->conexion->error;
            return false;
        }

        $stmt->bind_param("sssssi",
            $datos_usuario['nombre'],
            $datos_usuario['correo'],
            $datos_usuario['Descripcion_profesional'],
            $datos_usuario['horario_trabajo'],
            $datos_usuario['foto_perfil'],
            $datos_usuario['id']
        );

        if ($stmt->execute()){
            return true;
        } else {
            echo "Error al ejecutar la consulta para editar el usuario: " . $stmt->error;
            return false;
        }
    }
}
}
?>
