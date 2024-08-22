<?php
// editar_medico.php

include '../../../../Modelo/conexion.php';
include '../../../../Controlador/CitaController.php';

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $datos_medico = [
        'ID_Medico' => $_POST['ID_Medico'],
        'nombres' => $_POST['nombres'],
        'apellidos' => $_POST['apellidos'],
        'especialidad' => $_POST['especialidad'],
        'horario_trabajo' => $_POST['horario_trabajo'],
        'telefono' => $_POST['telefono'],
        'correo' => $_POST['correo'],
        'consultorio' => $_POST['consultorio'],
        'estado_disponibilidad' => $_POST['estado_disponibilidad']
    ];

    if ($citaController->actualizar_medico($datos_medico)) {
        header("Location: medicos.php?mensaje=Medico actualizado exitosamente");
        exit();
    } else {
        echo "<p class='text-danger'>Error al actualizar el médico.</p>";
    }
}

if (isset($_GET['id'])) {
    $medico = $citaController->obtener_medico_por_id($_GET['id']);
}

$consultorios = $citaController->obtener_consultorio();
$especialidades = $citaController->obtener_especialidad();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Médico</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-top: 500px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-custom {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .form-title {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php if (isset($medico)) : ?>
        <div class="form-container">
            <div class="text-center mb-4">
                <h2 class="text-primary">Editar Médico</h2>
                <img src="../../../../Vista/images/LogoH.png" alt="Logo" height="80" width="80" class="img-fluid mb-3">
            </div>
            <form method="POST" action="">

            <div class="form-group">
                    <label for="nombres">Identificación Médico:</label>
                    <input type="text" class="form-control" name="ID_Medico" value="<?php echo $medico['ID_Medico']; ?>" Readonly>
                </div>
                

                <div class="form-group">
                    <label for="nombres">Nombres:</label>
                    <input type="text" class="form-control" name="nombres" value="<?php echo $medico['Nombres']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" class="form-control" name="apellidos" value="<?php echo $medico['Apellidos']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="especialidad">Especialidad:</label>
                    <select class="form-control" name="especialidad" required>
                        <?php foreach ($especialidades as $especialidad) : ?>
                            <option value="<?php echo $especialidad['ID_Especialidad_M']; ?>" <?php echo $especialidad['ID_Especialidad_M'] == $medico['ID_Especialidad_M'] ? 'selected' : ''; ?>>
                                <?php echo $especialidad['Nombre_Especialidad']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="horario_trabajo">Horario de trabajo:</label>
                    <input type="text" class="form-control" name="horario_trabajo" value="<?php echo $medico['Horario_trabajo']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono" value="<?php echo $medico['Teléfono_contacto']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" class="form-control" name="correo" value="<?php echo $medico['Correo_electrónico']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="consultorio">Consultorio:</label>
                    <select class="form-control" name="consultorio" required>
                        <?php foreach ($consultorios as $consultorio) : ?>
                            <option value="<?php echo $consultorio['ID_Consultorio_M']; ?>" <?php echo $consultorio['ID_Consultorio_M'] == $medico['ID_Consultorio_M'] ? 'selected' : ''; ?>>
                                <?php echo $consultorio['Nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado_disponibilidad">Estado de disponibilidad:</label>
                    <select class="form-control" name="estado_disponibilidad" required>
                        <option value="1" <?php echo $medico['Estado_disponibilidad'] == 1 ? 'selected' : ''; ?>>Disponible</option>
                        <option value="0" <?php echo $medico['Estado_disponibilidad'] == 0 ? 'selected' : ''; ?>>No disponible</option>
                    </select>
                </div>

                <div style="text-align: center;">
                    <button type="submit" class="btn btn-custom" name="actualizar">Actualizar</button>
                    <button type="button" onclick="window.location.href='../../../../Vista/html/paneles/admind/medicos.php'" class="btn btn-danger">Regresar</button>
                </div>

            </form>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>