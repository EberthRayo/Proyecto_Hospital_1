<?php
session_start();
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';
include '../../../../Controlador/LoginAdminController.php';

if (isset($_SESSION['user_id'])) {
    $usuario_id = $_SESSION['user_id'];

    // Crear instancia del controlador de login admin
    $loginAdmin = new LoginAdminController($conexion);
    $datosUsuarioAdmin = $loginAdmin->obtenerDatosUsuarioAdmin($conexion, $usuario_id);

    // Extraer datos del arreglo devuelto por la función
    $nombreUsuario = $datosUsuarioAdmin['nombre'];
    $fotoPerfil = $datosUsuarioAdmin['foto_perfil'];
    $rol = $datosUsuarioAdmin['tipo_usuario'];
}

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registrar'])) {
        $datos_medico = [
            'identificacion' => $_POST['identificacion'],
            'nombres' => $_POST['nombres'],
            'apellidos' => $_POST['apellidos'],
            'email' => $_POST['email'],
            'telefono' => $_POST['telefono'],
            'especialidad' => $_POST['especialidad'],
            'consultorio' => $_POST['consultorio'],
            'horario_trabajo' => $_POST['horario_trabajo'],
            'estado_disponibilidad' => $_POST['estado_disponibilidad']
        ];
        $citaController->registrar_medico($datos_medico);
    } elseif (isset($_POST['eliminarMedico'])) {
        $citaController->eliminar_medico($_POST['ID_Medico']);
    }
}

$medicos = $citaController->listar_medicos();
$consultorios = $citaController->obtener_consultorio();
$especialidades = $citaController->obtener_especialidad();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Médico</title>
    <link rel="icon" href="../../../../Vista/images/LogoH.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="../../css/formulariocita.css">
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .main-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .input-group-text {
            border-radius: 0 8px 8px 0;
        }

        .input-group-append .input-group-text {
            background-color: #e9ecef;
        }

        h3 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: 500;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="main-content">
                    <button class="btn btn-danger"><a href="../../../../Vista/html/paneles/admind/medicos.php" style="color: #ffffff; text-decoration:none;">Regresar</a></button>
                    <center>
                        <h3>Registrar Médico</h3>
                    </center>
                    <div class="d-flex justify-content-center mb-4">
                        <img src="../../../../Vista/images/LogoH.png" alt="Logo" height="80" width="80">
                    </div>
                    <form method="POST" action="medicos.php">
                        <div class="mb-3">
                            <label for="identificacion" class="form-label">Identificación:</label>
                            <input type="text" class="form-control" id="identificacion" name="identificacion" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombres" required>
                        </div>
                        <div class="mb-3">
                            <label for="Apellido" class="form-label">Apellidos:</label>
                            <input type="text" class="form-control" id="Apellido" name="apellidos" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="especialidad" class="form-label">Especialidad:</label>
                            <select class="form-control" id="especialidad" name="especialidad" required>
                                <?php foreach ($especialidades as $especialidad) : ?>
                                    <option value="<?php echo $especialidad['ID_Especialidad_M']; ?>"><?php echo $especialidad['Nombre_Especialidad']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="consultorio" class="form-label">Consultorio:</label>
                            <select class="form-control" id="consultorio" name="consultorio" required>
                                <?php foreach ($consultorios as $consultorio) : ?>
                                    <option value="<?php echo $consultorio['ID_Consultorio_M']; ?>"><?php echo $consultorio['Nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="horario_trabajo" class="form-label">Horario de Trabajo:</label>
                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1" name="horario_trabajo" required />
                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="Estado_disponibilidad" class="form-label">Estado de Disponibilidad:</label>
                            <input type="text" class="form-control" id="Estado_disponibilidad" name="estado_disponibilidad" required>
                        </div>
                        <button type="submit" name="registrar" class="btn btn-primary w-100 mt-3">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempus-dominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
    <script>
        $(function() {
            $('#datetimepicker1').datetimepicker({
                format: 'HH:mm'
            });
        });
    </script>
</body>

</html>