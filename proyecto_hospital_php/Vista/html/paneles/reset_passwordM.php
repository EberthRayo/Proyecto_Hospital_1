<?php
include '../../../Modelo/conexion.php';
include '../../../Controlador/LoginMedicoController.php';

$loginController = new LoginMedicoController($conexion);
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['correo']) && isset($_POST['contrasena'])) {
        $message = $loginController->procesar_login();
    } elseif (isset($_POST['correo_olvido'])) {
        $message = $loginController->procesar_olvido_contrasena($_POST['correo_olvido']);
    } elseif (isset($_POST['token']) && isset($_POST['nueva_contrasena'])) {
        $message = $loginController->restablecer_contrasena($_POST['token'], $_POST['nueva_contrasena']);
    }
}

$recuerdame_message = $loginController->verificar_recuerdame();

// Cerrar la conexión
$conexion->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña </title>
    <link rel="icon" href="../../../Vista/images/LogoH.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            margin: 0;
            background-image: url('../../../Vista/images/LoginM.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
        .container {
            max-width: 500px;
            margin-top: 50px;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .form-group {
            position: relative;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            color: #007bff;
            font-weight: bold;
        }

        .alert {
            margin-bottom: 20px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
    <a href="../../../Vista/html/paneles/Login_personal_Medico.php" class="text-black">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="d-flex justify-content-center mb-4">
            <img src="../../images/LogoH.png" alt="Logo" height="80" width="80">
        </div>
        <?php if ($message) : ?>
            <div class="alert alert-<?php echo ($message == "Contraseña restablecida correctamente") ? "success" : "danger"; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="header">
            <h2>Restablecer Contraseña <br>Perfil Médico</h2>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="nueva_contrasena">Nueva Contraseña:</label>
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" required class="form-control">
                <span class="fas fa-eye toggle-password"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Restablecer Contraseña</button>
        </form>
    </div>
    <script>
        document.querySelector('.toggle-password').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('nueva_contrasena');
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