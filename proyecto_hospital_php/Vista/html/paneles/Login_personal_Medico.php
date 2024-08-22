<?php
session_start();
include '../../../Modelo/conexion.php';
include_once '../../../Controlador/LoginMedicoController.php';

// Crear una instancia del controlador de inicio de sesión
$loginMedicoController = new LoginMedicoController($conexion);
$message = '';
$recuerdame_message = '';

// Procesar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar solicitud de olvido de contraseña
    if (isset($_POST['action']) && $_POST['action'] === 'forgot_password') {
        if (!empty($_POST['correo'])) {
            $_SESSION['message'] = $loginMedicoController->procesar_olvido_contrasena($_POST['correo']);
        } else {
            $_SESSION['message'] = 'Por favor, introduzca su correo electrónico.';
        }
    }
    // Procesar solicitud de restablecimiento de contraseña
    elseif (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
        if (!empty($_POST['token']) && !empty($_POST['nueva_contrasena'])) {
            $response = $loginMedicoController->restablecer_contrasena($_POST['token'], $_POST['nueva_contrasena']);
            $_SESSION['message'] = $response;
            if ($response === 'Contraseña restablecida correctamente') {
                header("Location: /Vista/html/paneles/admind/reset_password.php"); // Redirige a la página actual o a una página de éxito
                exit();
            }
        } else {
            $_SESSION['message'] = 'Por favor, introduzca el token y la nueva contraseña.';
        }
    }
    // Procesar solicitud de inicio de sesión
    elseif (isset($_POST['correo']) && isset($_POST['contrasena'])) {
        $user = $loginMedicoController->procesar_login();
        if ($user === true) {
            $_SESSION['medico_id'] = $_SESSION['user_id']; // Asegúrate de que $_SESSION['user_id'] esté configurado correctamente en procesar_login()
            header("Location: /Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/PersonalMedico/index.php"); // Redirige al panel de control
            exit();
        } else {
            $_SESSION['message'] = $user; // Mensaje de error devuelto por procesar_login()
        }
    }
}

// Mostrar mensaje de sesión
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Verificar la cookie "Recuérdame"
$recuerdame_message = $loginMedicoController->verificar_recuerdame();

// Cerrar la conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Personal Médico</title>
    <link rel="icon" href="../../../Vista/images/LogoH.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('../../../Vista/images/LoginM.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .login-container:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }

        .login-container h2 {
            margin: 0 0 20px;
            color: #333;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check label {
            margin-left: 5px;
            color: #555;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-group button:hover {
            background: #0056b3;
        }

        .form-group button:active {
            background: #004080;
        }

        .form-group .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-top: 5px;
        }

        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
        }

        .password-container {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <a href="../../../Vista/html/paneles/index.php" class="text-black">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="d-flex justify-content-center mb-4">
            <img src="../../images/LogoH.png" alt="Logo" height="80" width="80">
        </div>

        <h2>Login Médico</h2>
        <form action="../../../Vista/html/paneles/PersonalMedico/index.php" method="post" id="loginMedicoForm">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="correo" id="email" class="form-control" required>
            </div>
            <div class="form-group password-container">
                <label for="Password2">Contraseña</label>
                <input type="password" name="contrasena" id="Password2" class="form-control" required>
                <span class="fas fa-eye toggle-password"></span>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="recuerdame" id="Recuerdame">
                <label class="form-check-label" for="Recuerdame">Recuérdame</label>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                <input type="hidden" name="action" value="login">
            </div>
            <div class="forgot-password">
                <a href="" data-toggle="modal" data-target="#forgotPasswordModal">Olvidé mi contraseña</a>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Recuperar contraseña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="correo_olvido">Correo:</label>
                            <input type="email" id="correo_olvido" name="correo" required class="form-control">
                        </div>
                        <input type="hidden" name="action" value="forgot_password">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
         document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('Password1');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });

        $(document).ready(function(){
    $('#loginMedicoForm').on('submit', function(event){ // Agregar # antes de loginMedicoForm
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/loginMedico.php',
            data: formData,
            dataType: 'json',
            success: function(response){
                console.log('Respuesta del servidor:', response);
                if (response.status === 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location.href = 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/PersonalMedico/index.php';
                        } else {
                            console.log('El usuario no confirmó la alerta.');
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Muestra un mensaje de error en caso de fallo de la solicitud
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error en la solicitud: ' + xhr.responseText,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

    </script>
</body>

</html>
