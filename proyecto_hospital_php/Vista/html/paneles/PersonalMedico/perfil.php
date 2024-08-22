<?php
include '../../../../Modelo/conexion.php';
include '../../../../Controlador/LoginMedicoController.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $loginMedicoController = new LoginMedicoController($conexion);

    $datosUsuarioMedico = $loginMedicoController->obtenerDatosUsuarioMedico($conexion, $user_id);

    if ($datosUsuarioMedico) {
        // Uso de los datos obtenidos
        $username = $datosUsuarioMedico['nombre'];
        $profile_photo = $datosUsuarioMedico['foto_perfil'];
        $role = $datosUsuarioMedico['tipo_usuario'];
        $email = $datosUsuarioMedico['correo'];
        $work_schedule = $datosUsuarioMedico['horario_trabajo'];
        $descripcion = $datosUsuarioMedico['Descripcion_profesional'];
    } else {
        echo 'No se encontraron datos del usuario.';
        exit;
    }
} else {
    echo 'No hay sesión activa.';
    exit;
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Médico</title>
    <link rel="icon" href="../../../../Vista/images/LogoH.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 08px 02px 08px 08px  rgba(0, 0, 0, 0.8);
        }

        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header img.logo {
            width: 120px;
            margin-bottom: 20px;
        }

        .profile-header img.profile-pic {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #007bff;
        }

        .profile-header h1 {
            margin-top: 10px;
            font-size: 24px;
            color: #333;
        }

        .profile-header p {
            margin: 5px 0;
            color: #777;
        }

        .profile-details {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-details h2 {
            margin-bottom: 20px;
            font-size: 20px;
            color: #007bff;
        }

        .profile-details ul {
            list-style: none;
            padding: 0;
        }

        .profile-details ul li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
        }

        .profile-details ul li strong {
            color: #333;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

        .buttons a {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: #ffffff;
            font-weight: bold;
        }

        .edit-profile-btn {
            background-color: #007bff;
        }

        .back-btn {
            background-color: #6c757d;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            cursor: pointer;
            float: right;
            font-size: 1.5rem;
            color: #333;
        }
    </style>
</head>

<body>
    
    <div class="container">
        <div class="profile-header">
            <img class="profile-pic" src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Foto de perfil">
            <h1><?php echo htmlspecialchars($username); ?></h1>
            <p>Rol: <?php echo htmlspecialchars($role); ?></p>
            <p>Correo Electrónico: <?php echo htmlspecialchars($email); ?></p>
        </div>

        <div class="profile-details">
            <h2>Detalles del Perfil</h2>
            <ul>
                <li><strong>Nombre:</strong> <?php echo htmlspecialchars($username); ?></li>
                <li><strong>Horario de Trabajo:</strong> <?php echo htmlspecialchars($work_schedule); ?></li>
                <li><strong>Descripción Profesional:</strong> <?php echo htmlspecialchars($descripcion); ?></li>
            </ul>
        </div>

        <div class="buttons">
            <a class="edit-profile-btn" href="#" id="editProfileBtn">Editar Perfil</a>
            <a class="back-btn" href="../../../../Vista/html/paneles/PersonalMedico/index.php">Volver</a>
        </div>
    </div>

    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <h2>Editar Perfil</h2>
            <form id="editProfileForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profileName">Nombre</label>
                    <input type="text" id="profileName" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="form-group">
                    <label for="profileEmail">Correo Electrónico</label>
                    <input type="email" id="profileEmail" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group">
                    <label for="profileDescription">Descripción profesional</label>
                    <textarea id="profileDescription" class="form-control" rows="4"><?php echo htmlspecialchars($descripcion); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="profileHorario">Horario de trabajo</label>
                    <input type="text" id="profileHorario" class="form-control" value="<?php echo htmlspecialchars($work_schedule); ?>">
                </div>
                <div class="form-group">
                    <label for="profilePhoto">Cambiar foto de perfil:</label>
                    <input type="file" id="profilePhoto" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary" name="guardar">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        // Edit profile modal logic
        const editProfileBtn = document.getElementById('editProfileBtn');
        const editProfileModal = document.getElementById('editProfileModal');
        const closeModal = document.getElementById('closeModal');

        editProfileBtn.addEventListener('click', () => {
            editProfileModal.style.display = 'flex';
        });

        closeModal.addEventListener('click', () => {
            editProfileModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target == editProfileModal) {
                editProfileModal.style.display = 'none';
            }
        });

        // Edit profile form submission logic
        document.getElementById('editProfileForm').addEventListener('submit', function (event) {
            event.preventDefault();
            // Perform AJAX request to submit form data to the server for processing.
            const formData = new FormData(this);
            // Replace with actual AJAX code to send data to the server.
            console.log('Form submitted');
        });
    </script>
</body>
</html>
