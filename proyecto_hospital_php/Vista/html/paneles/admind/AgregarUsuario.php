<?php
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregarU'])) {
        $datos_usuario = array(
            'nombre' => $_POST['nombre'],
            'correo' => $_POST['correo'],
            'contrasena' => $_POST['password'],
            'descripcion_profesional' => $_POST['descripcion_profesional'],
            'foto_perfil' => $_FILES['foto_perfil']['name'],
            'horario_trabajo' => $_POST['horario_trabajo'],
            'especialidad' => $_POST['especialidad'],
            'tipo_usuario' => $_POST['tipo_usuario'],
            'token' => bin2hex(random_bytes(16)), // Genera un token aleatorio
            'reset_token' => bin2hex(random_bytes(16)), // Genera un token aleatorio
            'token_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour')) // Expira en 1 hora
        );

        // Manejar la carga de la foto de perfil
        if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../../../Vista/images/usuarios/';
            $upload_file = $upload_dir . basename($_FILES['foto_perfil']['name']);
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $upload_file)) {
                $citaController->registrar_usuario($datos_usuario);
            } else {
                echo "Error al subir la imagen.";
            }
        } else {
            echo "Error en la carga de la imagen.";
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 800px;
            margin-top: 50px;
        }
        h2 {
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .btn-danger {
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 600;
        }
        .btn-danger a {
            color: #ffffff;
            text-decoration: none;
        }
        .form-control, .form-control-file {
            border-radius: 8px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-control-file:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .btn-danger, .btn-primary {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn btn-danger">
            <a href="../../../../Vista/html/paneles/admind/usuarios.php">Regresar</a>
        </button>
        <center><h2>Agregar Nuevo Usuario</h2>
            <img src="../../../../Vista/images/LogoH.png" alt="Logo" height="80" width="80"></center>
        <form action="AgregarUsuario.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="tipo_usuario">Tipo de Usuario</label>
                <select class="form-control" id="tipo_usuario" name="tipo_usuario" required>
                    <option value="" selected>Selecciona el tipo de usuario</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Personal Medico">Personal Medico</option>
                    <option value="Jefe Inmediato">Jefe Inmediato</option>
                </select>
            </div>
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad</label>
                <input type="text" class="form-control" id="especialidad" name="especialidad" required>
            </div>
            <div class="form-group">
                <label for="horario_trabajo">Horario de Trabajo</label>
                <input type="text" class="form-control" id="horario_trabajo" name="horario_trabajo" required>
            </div>
            <div class="form-group">
                <label for="descripcion_profesional">Descripción Profesional</label>
                <textarea class="form-control" id="descripcion_profesional" name="descripcion_profesional" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil</label>
                <input type="file" class="form-control-file" id="foto_perfil" name="foto_perfil" accept="image/*" required>
            </div>
            <hr>
            <center><button type="submit" class="btn btn-primary" name="agregarU">Agregar Usuario</button></center>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>