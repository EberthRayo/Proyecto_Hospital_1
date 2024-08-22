<?php
include '../../../../Modelo/conexion.php';
include '../../../../Controlador/CitaController.php';

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $datos_paciente = [
        'ID_Paciente' => $_POST['ID_Paciente'],
        'Tipo_documento' => $_POST['Tipo_documento'],
        'Nombres' => $_POST['Nombres'],
        'Fecha_Nacimiento' => $_POST['Fecha_Nacimiento'],
        'Genero' => $_POST['Genero'],
        'Direccion_Residencia' => $_POST['Direccion_Residencia'],
        'Numero_Telefono' => $_POST['Numero_Telefono'],
        'Correo_Electronico' => $_POST['Correo_Electronico'],
        'Eps' => $_POST['Eps'],
        'Cobertura' => $_POST['Cobertura']
    ];

    if ($citaController->editar_paciente($datos_paciente)) {
        header("Location: pacientes.php?mensaje=Paciente actualizado exitosamente");
        exit();
    } else {
        echo "<p class='text-danger'>Error al actualizar el paciente.</p>";
    }
}

if (isset($_GET['id'])) {
    $paciente = $citaController->obtener_paciente_por_id($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Información del Paciente</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ced4da;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .btn-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
        }

        .btn-primary:hover {
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }

        .btn-cancel {
            margin-right: 10px;
        }

        .title {
            margin-bottom: 30px;
            text-align: center;
            color: #343a40;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="text-center mb-4">
            <h2 class="text-primary">Editar Paciente</h2>
            <img src="../../../../Vista/images/LogoH.png" alt="Logo" height="80" width="80" class="img-fluid mb-3">
        </div>
        <form action="" method="POST">
            <input type="hidden" name="ID_Paciente" value="<?php echo $paciente['ID_Paciente']; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Tipo_documento">Tipo de Documento:</label>
                        <select class="form-control" id="Tipo_documento" name="Tipo_documento" required>
                        <option selected>Seleccione su Tipo de Documento</option>
                            <option value="Cedula Ciudadania" <?php if ($paciente['Tipo_documento'] == 'Cedula Ciudadania') echo 'selected'; ?>>Cedula Ciudadania</option>
                            <option value="Tarjeta Identidad" <?php if ($paciente['Tipo_documento'] == 'Tarjeta Identidad') echo 'selected'; ?>>Tarjeta Identidad</option>
                            <option value="Cedula Extranjeria" <?php if ($paciente['Tipo_documento'] == 'Cedula Extranjeria') echo 'selected'; ?>>Cedula Extranjeria</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Nombres">Nombre Completo:</label>
                        <input type="text" class="form-control" id="Nombres" name="Nombres" value="<?php echo $paciente['Nombres']; ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Fecha_Nacimiento">Fecha de Nacimiento:</label>
                        <input type="date" class="form-control" id="Fecha_Nacimiento" name="Fecha_Nacimiento" value="<?php echo $paciente['Fecha_Nacimiento']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Genero">Género:</label>
                        <select class="form-control" id="Genero" name="Genero" required>
                        <option selected>Seleccione su Genero </option>
                            <option value="Masculino" <?php if ($paciente['Genero'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
                            <option value="Femenino" <?php if ($paciente['Genero'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
                            <option value="Otro" <?php if ($paciente['Genero'] == 'Otro') echo 'selected'; ?>>Otro</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Direccion_Residencia">Dirección de Residencia:</label>
                        <input type="text" class="form-control" id="Direccion_Residencia" name="Direccion_Residencia" value="<?php echo $paciente['Direccion_Residencia']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Numero_Telefono">Número de Teléfono:</label>
                        <input type="text" class="form-control" id="Numero_Telefono" name="Numero_Telefono" value="<?php echo $paciente['Numero_Telefono']; ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Correo_Electronico">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="Correo_Electronico" name="Correo_Electronico" value="<?php echo $paciente['Correo_Electronico']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EPS"><i class="fa-solid fa-building-flag" style="color: #74C0FC;"></i> Eps de afiliación:</label>
                        <select class="form-control" id="EPS" name="Eps" required>
                            <option value="No aplica" <?php if ($paciente['Eps'] == 'No aplica') echo 'selected'; ?>>No aplica</option>
                            <option value="Sanitas" <?php if ($paciente['Eps'] == 'Sanitas') echo 'selected'; ?>>Sanitas</option>
                            <option value="Asmet Salud" <?php if ($paciente['Eps'] == 'Asmet Salud') echo 'selected'; ?>>Asmet Salud</option>
                            <option value="Pijao Salud" <?php if ($paciente['Eps'] == 'Pijao Salud') echo 'selected'; ?>>Pijao Salud</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Cobertura">Cobertura:</label>
                <select class="form-control" id="Cobertura" name="Cobertura" required>
                    <option value="Subsidiado" <?php if ($paciente['Cobertura'] == 'Subsidiado') echo 'selected'; ?>>Subsidiado</option>
                    <option value="Contributivo" <?php if ($paciente['Cobertura'] == 'Contributivo') echo 'selected'; ?>>Contributivo</option>
                    <option value="Particular" <?php if ($paciente['Cobertura'] == 'Particular') echo 'selected'; ?>>Particular</option>
                </select>
            </div>
            <div class="form-group text-center">
                <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
                <a href="pacientes.php" class="btn btn-secondary btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
