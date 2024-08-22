<?php
// editar_medico.php

include '../../../../Modelo/conexion.php';
include '../../../../Controlador/CitaController.php';

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $datos_cita = [
        'ID_Cita' => $_POST['ID_Cita'],
        'ID_Paciente' => $_POST['ID_Paciente'],
        'ID_Disponibilidad_fecha' => $_POST['ID_Disponibilidad_fecha'],
        'Id_Especialidad_M' => $_POST['Id_Especialidad_M'],
        'Motivo' => $_POST['Motivo'],
        'Estado_cita' => $_POST['Estado_cita'],
        'Asistencia' => $_POST['Asistencia']
    ];

    if ($citaController->editar_estado_cita($datos_cita)) {
        header("Location: citas.php?mensaje=Paciente actualizado exitosamente");
        exit();
    } else {
        echo "<p class='text-danger'>Error al actualizar la cita.</p>";
    }
}

if (isset($_GET['id'])) {
    $cita = $citaController->obtener_cita_por_id($_GET['id']);
    if ($cita) {
        $paciente = $citaController->obtener_paciente_por_id($cita['ID_Paciente']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
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
            margin-top:250px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
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
    </style>
</head>

<body>
    <?php if (isset($cita) && isset($paciente)) : ?>
        <div class="form-container">
            <div class="text-center mb-4">
                <h2 class="text-primary">Editar Cita</h2>
                <img src="../../../../Vista/images/LogoH.png" alt="Logo" height="80" width="80" class="img-fluid mb-3">
            </div>
            <form method="POST" action="">
                <input type="hidden" name="ID_Cita" value="<?php echo htmlspecialchars($cita['ID_cita']); ?>">

                <div class="form-group">
                    <label for="id_cita">ID Cita:</label>
                    <input type="text" class="form-control" name="ID_Cita" value="<?php echo htmlspecialchars($cita['ID_cita']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="id_paciente">ID Paciente:</label>
                    <input type="text" class="form-control" name="ID_Paciente" value="<?php echo htmlspecialchars($paciente['ID_Paciente']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="id_Disponibilidad">ID Disponibilidad de la Fecha:</label>
                    <input type="text" class="form-control" name="ID_Disponibilidad_fecha" value="<?php echo htmlspecialchars($cita['ID_Disponibilidad_fecha']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="especialidad">Area de salud:</label>
                    <input type="text" class="form-control" name="Id_Especialidad_M" value="<?php echo htmlspecialchars($cita['Id_Especialidad_M']); ?>">
                </div>
                <div class="form-group">
                    <label for="area_salud">Motivo:</label>
                    <input type="text" class="form-control" name="Motivo" value="<?php echo htmlspecialchars($cita['Motivo']); ?>">
                </div>
                <div class="form-group">
                    <label for="asistencia">Asistencia:</label>
                    <select class="form-control" id="asistencia" name="Asistencia" required>
                        <option value="Por asistir" <?php if ($cita['Asistencia'] == 'Por asistir') echo 'selected'; ?>>Por asistir</option>
                        <option value="No asistio" <?php if ($cita['Asistencia'] == 'No asistio') echo 'selected'; ?>>No asistio</option>
                        <option value="Asistio" <?php if ($cita['Asistencia'] == 'Asistio') echo 'selected'; ?>>Asistio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado de la cita:</label>
                    <select class="form-control" id="estado" name="Estado_cita" required>
                        <option value="Valorada" <?php if ($cita['Estado_Cita'] == 'Valorada') echo 'selected'; ?>>Valorada</option>
                        <option value="Confirmada" <?php if ($cita['Estado_Cita'] == 'Confirmada') echo 'selected'; ?>>Confirmada</option>
                        <option value="Cancelada" <?php if ($cita['Estado_Cita'] == 'Cancelada') echo 'selected'; ?>>Cancelada</option>
                    </select>

                </div>
                <!-- Agrega aquí los demás campos del formulario según sea necesario -->

                <div style="text-align: center;">
                    <button type="submit" class="btn btn-custom" name="actualizar">Actualizar</button>
                    <button type="button" onclick="window.location.href='../../../../Vista/html/paneles/admind/citas.php'" class="btn btn-danger">Regresar</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>