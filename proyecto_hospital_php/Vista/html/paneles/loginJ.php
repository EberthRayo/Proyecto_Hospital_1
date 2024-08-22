<?php
include '../../../Modelo/conexion.php';
include '../../../Controlador/LoginJefeController.php';

$loginJefe = new JefeController($conexion);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'forgot_password') {
        $message = $loginJefe->procesar_olvido_contrasena($_POST['correo']);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
        $message = $loginJefe->restablecer_contrasena($_POST['token'], $_POST['nueva_contrasena']);
    } elseif (isset($_POST['correo']) && isset($_POST['contrasena'])) {
        $message = $loginJefe->procesar_login();
    }
}

$recuerdame_message = $loginJefe->verificar_recuerdame();

// Cerrar la conexión
$conexion->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Jefe Inmediato</title>
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
            background-image: url('../../../Vista/images/loginJ.jpg');
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

        <h2>Login Jefe Inmediato</h2>
        <?php if ($message) : ?>
            <div class="alert alert-<?php echo ($message == "Accedió correctamente" || $message == "Correo de reestablecimiento enviado, revise su bandeja de entrada") ? "success" : "danger"; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="correo" id="email" class="form-control" required>
            </div>
            <div class="form-group password-container">
                <label for="Password">Contraseña</label>
                <input type="password" name="contrasena" id="Password" class="form-control" required>
                <span class="fas fa-eye toggle-password"></span>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="recuerdame" id="Recuerdame">
                <label class="form-check-label" for="Recuerdame">Recuérdame</label>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script>
        document.querySelector('.toggle-password').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('Password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                e.target.classList.remove('fa-eye');
                e.target.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                e.target.classList.remove('fa-eye-slash');
                e.target.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>