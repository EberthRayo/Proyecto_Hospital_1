<?php
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';
$citaController = new CitaController($conexion);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registrar'])) {
        $resultado = $citaController->procesarFormularioCitaPanel($conexion);
        echo $resultado; 
        exit; 
    }
}
$fechas_disponibles = $citaController->obtener_fechas_disponibles();

// Genera las opciones para el select
if (is_array($fechas_disponibles)) {
    $options = array_map(function ($fecha) {
        return '<option value="' . htmlspecialchars($fecha['ID_Disponibilidad_fecha']) . '">' . htmlspecialchars($fecha['Fecha_hora']) . '</option>';
    }, $fechas_disponibles);
} else {
    $options = [];
}

// Unir todas las opciones en una cadena
$optionsHtml = implode("\n", $options);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Ingreso de Pacientes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 30px;
        }

        .form-section {
            background-color: #f8f9fa;
            margin: auto;
            max-width: 700px;
            padding: 1.2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-outline-success {
            border-radius: 0.25rem;
        }

        .form-control {
            border-radius: 0.25rem;
        }

        .form-group label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="form-section">
                    <center>
                        <h4>Formulario de Ingreso de Pacientes</h4>
                    </center>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="Documento">Documento Paciente:</label>
                            <input type="number" class="form-control" id="Documento" name="documento" required>
                        </div>
                        <div class="form-group">
                            <label for="Tdocumento">Tipo documento</label>
                            <select class="form-control" id="Tdocumento" name="tdocumento">
                                <option selected>Selecciona el tipo de documento</option>
                                <option value="Cedula Ciudadania">Cedula Ciudadania</option>
                                <option value="Cedula Extranjeria">Cedula Extranjeria</option>
                                <option value="Tarjeta de identidad">Tarjeta de Identidad</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Nombre_completo">Nombres:</label>
                            <input type="text" class="form-control" id="Nombre_completo" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="FechaNacimiento">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control" id="FechaNacimiento" name="nacimiento" required>
                        </div>
                        <div class="form-group">
                            <label for="Genero">Género:</label>
                            <select class="form-control" id="Genero" name="genero" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Direccion">Dirección de Residencia:</label>
                            <input type="text" class="form-control" id="Direccion" name="direccion" required>
                        </div>
                        <div class="form-group">
                            <label for="Telefono">Número de Teléfono:</label>
                            <input type="number" class="form-control" id="Telefono" name="telefono" required>
                        </div>
                        <div class="form-group">
                            <label for="Correo">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="Correo" name="correo" required>
                        </div>
                        <div class="form-group">
                            <label for="EPS">Eps de afiliación</label>
                            <select class="form-control" id="EPS" name="eps">
                                <option value="No aplica">No aplica</option>
                                <option value="Sanitas">Sanitas</option>
                                <option value="Asmet Salud">Asmet Salud</option>
                                <option value="Pijao Salud">Pijao Salud</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Tcobertura">Tipo de cobertura</label>
                            <select class="form-control" id="Tcobertura" name="cobertura">
                                <option selected>Seleccione la cobertura</option>
                                <option value="Subisidiado">Subsidiado</option>
                                <option value="Contributivo">Contributivo</option>
                                <option value="Particular">Particular</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Area">Área médica</label>
                            <select class="form-control" id="Area" name="area">
                                <option selected>Seleccione el area medica</option>
                                <option value="Laboratorio">Laboratorio</option>
                                <option value="Pediatria">Pediatria</option>
                                <option value="Medico General">Medico General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fechahora">Fechas y horas disponibles</label>
                            <select class="form-control" id="fechahora" name="horafecha" style="font-size: 16px;">
                                <option selected>Selecciona las fechas y horas disponibles</option>
                                <?php echo $optionsHtml; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="Motivocita" class="form-label">Motivo de la cita médica:</label>
                            <input type="text" class="form-control" id="Motivocita" name="motivo" placeholder="Danos a conocer el motivo por el cual requieres la cita Médica">
                        </div>
                        <button type="submit" name="registrar" class="btn btn-primary btn-block">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('formulario-cita').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir el envío del formulario

            var formData = new FormData(this);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result); // Mostrar el mensaje de resultado del servidor
                if (result.includes('Paciente registrado con éxito')) {
                    document.getElementById('formulario-cita').reset(); // Opcional: Limpiar el formulario si es necesario
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>

</html>