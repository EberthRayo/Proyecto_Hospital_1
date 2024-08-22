<?php
include '../../../Modelo/conexion.php';
include '../../../Controlador/CitaController.php';
session_start();

// Recuperar el estado de la validación de EPS y los parámetros de la URL
$epsValidated = isset($_SESSION['eps_validated']) && $_SESSION['eps_validated'];
$showForm = isset($_GET['showForm']) ? $_GET['showForm'] : '';
$subsection = isset($_GET['subsection']) ? $_GET['subsection'] : '';

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tdocumento'])) {
        $citaController->procesarFormularioCitaEPS();
        exit; // Asegura que no se ejecute más código después de la respuesta
    }
}

// Inicializar variables para almacenar HTML de las opciones
$optionsHtml = '';
$especialidadesHtml = '<option selected>Seleccione la especialidad</option>';
$medicosHtml = '<option selected>Seleccione un médico</option>';

// Procesar solicitud POST para actualizar médicos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['especialidad'])) {
    $especialidadSeleccionada = $_POST['especialidad'];
    $medicos = $citaController->obtenerMedicosPorEspecialidad($especialidadSeleccionada);

    $medicosHtml = '<option selected>Seleccione un médico</option>';
    foreach ($medicos as $medico) {
        $medicosHtml .= '<option value="' . htmlspecialchars($medico['ID_Medico']) . '">' . htmlspecialchars($medico['Nombres']) . ' ' . htmlspecialchars($medico['Apellidos']) . '</option>';
    }
}

// Obtener fechas disponibles
$fechas_disponibles = $citaController->obtener_fechas_disponibles();
$options = is_array($fechas_disponibles) ? array_map(function ($fecha) {
    return '<option value="' . htmlspecialchars($fecha['ID_Disponibilidad_fecha']) . '">' . htmlspecialchars($fecha['Fecha_hora']) . '</option>';
}, $fechas_disponibles) : [];
$optionsHtml = implode("\n", $options);

// Obtener especialidades
$especialidades = $citaController->obtener_especialidad();
foreach ($especialidades as $especialidad) {
    $especialidadesHtml .= '<option value="' . htmlspecialchars($especialidad['ID_Especialidad_M']) . '">' . htmlspecialchars($especialidad['Nombre_Especialidad']) . '</option>';
}

// Preparar datos para la vista
$datos_paciente = isset($datos_paciente) ? $datos_paciente : [];
$epsOptions = $citaController->obtenerEps();


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnovaSalud H.S.M.C</title>
    <link rel="icon" href="../../../Vista/images/LogoH.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="../../css/formulariocita.css">
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script>
        // Función para actualizar los médicos disponibles usando AJAX
        function updateMedicos() {
            const especialidadSelect = document.getElementById('Especialidad');
            const especialidadId = especialidadSelect.value;
            // Verificar que se haya seleccionado una especialidad válida
            if (especialidadId === 'Seleccione la especialidad') return;
            // Crear el objeto XMLHttpRequest
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_medico.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // Función que se ejecuta cuando la respuesta del servidor está disponible
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.getElementById('medicos').innerHTML = xhr.responseText;
                } else {
                    console.error('Error en la solicitud AJAX');
                }
            };
            // Enviar la solicitud AJAX con la especialidad seleccionada
            xhr.send('especialidad=' + encodeURIComponent(especialidadId));
        }
    </script>
</head>

<body data-cobertura="Particular">
    <div class="d-flex">
        <div class="sidebar flex-shrink-0">
            <div class="sidebar-header text-center">
                <img src="../../images/LogoH.png" alt="Logo" width="60" height="60" class="d-inline-block align-text-center">
                <h2>InnovaSalud <br>H.S.M.C</h2>
                <hr>
            </div>
            <a href="#inicio" onclick="showSection('inicio'); return false;"><i class="fas fa-home"></i> Inicio</a>
            <button type="button" class="btn btn-primarys" style="color: #fff;" onclick="window.location.href='../../../index.php'">
                <i class="fa-solid fa-arrow-left"></i> Regresar al inicio
            </button>
            <a href="#appointment" onclick="showSection('appointment'); return false;"><i class="fas fa-calendar-alt"></i> Crear Cita</a>
            <a href="#consultar" onclick="showSection('consultar'); return false;"><i class="fas fa-search"></i> Consultar Cita</a>
            <a href="#cancelar" onclick="showSection('cancelar'); return false;"><i class="fas fa-times-circle"></i> Cancelar Cita</a>
        </div>
    </div>
    <div class="content-wrapper">
        <section id="inicio" class="section">
            <div class="container">
                <div class="form-container">
                    <div class="text-center mb-4">
                        <img src="../../images/LogoH.png" alt="Logo" height="80" width="80" class="img-fluid mb-3">
                        <br>
                        <h2 class="text-primary">Inicio</h2>
                    </div>
                    <p class="text-muted">
                        Bienvenido a <strong>InnovaSalud H.S.M.C.</strong> En esta sección, ofrecemos una plataforma integral y fácil de usar para que nuestros pacientes puedan gestionar sus citas médicas de manera eficiente y conveniente. Aquí, usted puede:
                        <br><br>
                        <strong>1. Crear una Cita Médica</strong>: Reserve una consulta con uno de nuestros especialistas según sus necesidades y disponibilidad. Nuestro sistema le permitirá seleccionar el área de especialidad, la fecha y hora que mejor le convengan. Programe sus citas en unos pocos pasos y reciba confirmaciones instantáneas.
                        <br><br>
                        <strong>2. Consultar Citas Programadas</strong>: Revise y gestione sus citas existentes con facilidad. Podrá ver detalles importantes como la fecha, hora, y el nombre del médico que lo atenderá.
                        <br><br>
                        <strong>3. Cancelar Citas Médicas</strong>: En caso de necesitar hacer cambios, puede <strong>cancelar</strong> sus citas directamente desde esta plataforma.
                        <br><br>
                        Nuestra misión es brindar un servicio de salud accesible y eficiente, asegurándonos de que usted reciba la atención necesaria sin complicaciones. A través de esta sección, buscamos mejorar su experiencia como paciente, ofreciéndole herramientas para manejar sus citas de manera autónoma y sin la necesidad de largos tiempos de espera. Le invitamos a explorar y utilizar esta plataforma para garantizar que su experiencia con <strong>InnovaSalud H.S.M.C.</strong> sea lo más cómoda y satisfactoria posible.
                    </p>
                </div>
            </div>
        </section>

        <section id="appointment" class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-sm-12">
                        <!-- Formulario de validación de EPS -->
                        <div id="appointmentForm" class="card form-container">
                            <div id="epsValidationForm" class="subsection active">
                                <div class="card-body">
                                    <!-- Título de la sección -->
                                    <h1 class="text-center my-4" style="font-weight: bold; color: #007bff;">
                                        Validación de EPS
                                    </h1>

                                    <div class="d-flex justify-content-center mb-4">
                                        <img src="../../images/LogoH.png" alt="Logo" height="80" width="80">
                                    </div>

                                    <!-- Instrucción -->
                                    <div style="padding: 15px; background-color: #d9edf7; color: #31708f; border: 1px solid #bce8f1; border-radius: 4px;">
                                        <strong>Instrucción:</strong> Verifique si está afiliado a una EPS. Este proceso se hace para validar si puede ser atendido en nuestras instalaciones. Si se encuentra afiliado, después de validar este formulario, podrá llenar los datos necesarios para la cita o puedes agendar tu cita medica de forma particular.
                                    </div>

                                    <hr>

                                    <!-- Formulario de validación -->
                                    <form action="../../../Modelo/simulacion_eps.php" method="post" id="validateEPSForm">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="Tdocumento">
                                                    <i class="fa-solid fa-id-card-clip" style="color: #74C0FC;"></i> Tipo documento
                                                </label>
                                                <select class="form-select form-select-sm" id="Tdocumento" name="tdocumento" style="font-size: 16px;">
                                                    <option value="" selected>Selecciona el tipo de documento</option>
                                                    <option value="Cedula Ciudadania">Cédula de Ciudadanía</option>
                                                    <option value="Cedula Extranjeria">Cédula de Extranjería</option>
                                                    <option value="Tarjeta de identidad">Tarjeta de Identidad</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="Documento">
                                                    <i class="fa-solid fa-id-card" style="color: #74C0FC;"></i> Número Documento
                                                </label>
                                                <input type="number" class="form-control" id="Documento" name="documento" placeholder="Escribe tu número de documento" required>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="epsSelect">
                                                    <i class="fa-solid fa-building-flag" style="color: #74C0FC;"></i> Selecciona tu EPS
                                                </label>
                                                <select class="form-select form-select-sm" id="epsSelect" name="eps" style="font-size: 16px;">
                                                    <option value="" selected>Selecciona tu EPS</option>
                                                    <option value="EPS250SS">Asmet Salud</option>
                                                    <option value="EPSM03">CAFESALUD E.P.S. S.A. -CM</option>
                                                    <option value="EPSS02">SALUD TOTAL E.P.S. -CM</option>
                                                    <option value="EPSS05">EPS SANITAS - CMa</option>
                                                    <option value="EPSS37">NUEVA EPS S.A. -CM</option>
                                                    <option value="EPSS08">COMPENSAR E.P.S. -CM</option>
                                                    <option value="No aplica">No aplica</option>
                                                </select>
                                            </div>
                                            <input type="hidden" id="selectedEpsCode" name="selectedEpsCode">
                                            <div class="col-md-12 mt-4 d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary">Validar Documento</button>
                                            </div>
                                            <div id="createParticularAppointment" class="d-flex justify-content-center mt-3" style="display: none;">
                                                <button class="btn btn-success btn-sm" id="particularAppointmentBtn">Crear cita particular</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Formulario de registro de cita que se muestra despues de validar correctamente la eps -->
                        <div id="appointmentForm" class="subsection">
                            <div class=" card form-container">
                                <div class="d-flex justify-content-center mb-4">
                                    <img src="../../images/LogoH.png" alt="Logo" height="80" width="80">
                                </div>
                                <form method="post" action="../../../Controlador/alertas/FormularioCitaEps.php" id="CrearcitaEps">
                                    <input type="hidden" name="action" value="procesar_cita">
                                    <h3 class="text-center text-secondary mb-4">Registro de Cita</h3>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="appointmentTipoDocumento">
                                                <i class="fa-solid fa-id-card-clip" style="color: #74C0FC;"></i> Tipo Documento
                                            </label>
                                            <select class="form-select form-select-sm" id="appointmentTipoDocumento" name="tdocumento" style="font-size: 16px;" readonly>
                                                <option selected>Selecciona el tipo de documento</option>
                                                <option value="Cedula Ciudadania" <?php echo isset($_SESSION['tdocumento']) && $_SESSION['tdocumento'] == 'Cedula Ciudadania' ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                                                <option value="Cedula Extranjeria" <?php echo isset($_SESSION['tdocumento']) && $_SESSION['tdocumento'] == 'Cedula Extranjeria' ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                                                <option value="Tarjeta de identidad" <?php echo isset($_SESSION['tdocumento']) && $_SESSION['tdocumento'] == 'Tarjeta de identidad' ? 'selected' : ''; ?>>Tarjeta de Identidad</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="appointmentDocumento" class="form-label">
                                                <i class="fa-solid fa-id-card" style="color: #74C0FC;"></i> Número Documento
                                            </label>
                                            <input type="number" class="form-control" id="appointmentDocumento" name="documento" placeholder="Escribe tu documento" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="Nombre_completo" class="form-label">
                                                <i class="fa-solid fa-user" style="color: #74C0FC;"></i> Nombre Completo
                                            </label>
                                            <input type="text" class="form-control" id="Nombre_completo" name="nombre" placeholder="Escribe tu nombre completo" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="FechaNacimiento" class="form-label">
                                                <i class="fa-regular fa-calendar-days" style="color: #74C0FC;"></i> Fecha Nacimiento
                                            </label>
                                            <input type="text" class="form-control" id="FechaNacimiento" name="nacimiento">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="Genero">
                                                <i class="fa-solid fa-restroom" style="color: #74C0FC;"></i> Género
                                            </label>
                                            <input type="text" class="form-control" id="Genero" name="genero"></select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="Direccion" class="form-label">
                                                <i class="fa-solid fa-location-crosshairs" style="color: #74C0FC;"></i> Dirección
                                            </label>
                                            <input type="text" class="form-control" id="Direccion" name="direccion" value="<?php echo isset($paciente['Direccion_Residencia']) ? htmlspecialchars($paciente['Direccion_Residencia']) : ''; ?>" placeholder="Escribe tu dirección">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="Telefono" class="form-label">
                                                <i class="fa-solid fa-square-phone" style="color: #74C0FC;"></i> Teléfono
                                            </label>
                                            <input type="number" class="form-control" id="Telefono" name="telefono" value="<?php echo isset($paciente['Numero_Telefono']) ? htmlspecialchars($paciente['Numero_Telefono']) : ''; ?>" placeholder="Escribe tu número de teléfono">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Correo" class="form-label">
                                                <i class="fa-solid fa-envelope" style="color: #74C0FC;"></i> Correo Electrónico
                                            </label>
                                            <input type="email" class="form-control" id="Correo" name="correo" value="<?php echo isset($paciente['Correo_Electronico']) ? htmlspecialchars($paciente['Correo_Electronico']) : ''; ?>" placeholder="ej: example@gmail.com">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="appointmentEps">
                                                <i class="fa-solid fa-hospital" style="color: #74C0FC;"></i> EPS
                                            </label>
                                            <input type="text" name="eps" id="appointmentEps" class="form-control" style="font-size: 16px;" readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="Edad" class="form-label">
                                                <i class="fa-solid fa-cake-candles" style="color: #74C0FC;"></i> Edad
                                            </label>
                                            <input type="number" class="form-control" id="Edad" name="edad" placeholder="Escribe tu edad">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="Especialidad">
                                                <i class="fa-solid fa-hospital-user" style="color: #74C0FC;"></i> Especialidad
                                            </label>
                                            <select class="form-select form-select-lg mb-2" id="Especialidad" name="especialidad" style="font-size: 16px;" onchange="updateMedicos()">
                                                <?php echo $especialidadesHtml; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="medicos">
                                                <i class="fa-solid fa-user-md" style="color: #74C0FC;"></i> Médicos disponibles
                                            </label>
                                            <select class="form-select form-select-lg mb-2" id="medicos" name="medico" style="font-size: 16px;">
                                                <?php echo $medicosHtml; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="fechahora">
                                                <i class="fa-regular fa-clock" style="color: #74C0FC;"></i> Fechas y horas disponibles
                                            </label>
                                            <select class="form-control" id="fechahora" name="horafecha" style="font-size: 16px;">
                                                <option selected>Selecciona las fechas y horas disponibles</option>
                                                <?php echo $optionsHtml; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="Motivocita" class="form-label">
                                                <i class="fa-solid fa-comment" style="color: #74C0FC;"></i> Motivo de la cita médica:
                                            </label>
                                            <input type="text" class="form-control" id="Motivocita" name="motivo" style="font-size: 18px;" placeholder="Danos a conocer el motivo por el cual requieres la cita médica">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" name="registrar" class="btn btn-primary">Registrar Cita</button>
                                    </div>

                                </form>
                            </div>
                        </div>


                        <!-- Formulario de registro de cita particular -->
                        <div id="particularForm" class="subsection">
                            <div class=" card form-container">
                                <h3 class="text-center text-secondary">Registro Cita Particular</h3>
                                <form id="registerParticularForm" method="post" action="procesar_cita_particular.php">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="Tdocumento"><i class="fa-solid fa-id-card-clip" style="color: #74C0FC;"></i> Tipo documento</label>
                                            <select class="form-select form-select-sm" id="Tdocumento" name="tdocumento" style="font-size: 16px;">
                                                <option selected>Selecciona el tipo de documento</option>
                                                <option value="Cedula Ciudadania">Cedula Ciudadania</option>
                                                <option value="Cedula Extranjeria">Cedula Extranjeria</option>
                                                <option value="Tarjeta de identidad">Tarjeta de Identidad</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Documento" class="form-label"><i class="fa-solid fa-id-card" style="color: #74C0FC;"></i> Numero Documento</label>
                                            <input type="number" class="form-control" id="Documento" name="documento" placeholder="Escribe tu documento">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="PNombre"><i class="fa-solid fa-user" style="color: #74C0FC;"></i> Nombre Completo</label>
                                            <input type="text" class="form-control" id="PNombre" name="nombre" placeholder="Escribe tu nombre completo" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="PFechaNacimiento"><i class="fa-regular fa-calendar-days" style="color: #74C0FC;"></i> Fecha Nacimiento</label>
                                            <input type="date" class="form-control" id="PFechaNacimiento" name="nacimiento" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="EPS"><i class="fa-solid fa-building-flag" style="color: #74C0FC;"></i> Eps de afiliación</label>
                                            <select class="form-select form-select-lg mb-2" id="EPS" name="eps" style="font-size: 16px;">
                                                <option value="No aplica">No aplica</option>
                                                <option value="Sanitas">Sanitas</option>
                                                <option value="Asmet Salud">Asmet Salud</option>
                                                <option value="Pijao Salud">Pijao Salud</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Edad" class="form-label" style="font-size: 16px;"><i class="fa-regular fa-calendar-days" style="color: #74C0FC;"></i> Edad</label>
                                            <input type="number" class="form-control" id="Edad" name="edad">
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="PTelefono"><i class="fa-solid fa-square-phone" style="color: #74C0FC;"></i> Teléfono</label>
                                                <input type="number" class="form-control" id="PTelefono" name="telefono" placeholder="Escribe tu Número de teléfono" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="PCorreo"><i class="fa-solid fa-envelope" style="color: #74C0FC;"></i> Correo Electrónico</label>
                                                <input type="email" class="form-control" id="PCorreo" name="correo" placeholder="ej: example@gmail.com" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="Genero"><i class="fa-solid fa-restroom" style="color: #74C0FC;"></i> Genero</label>
                                                <select class="form-select form-select-lg mb-2" id="Genero" name="genero" style="font-size: 16px;">
                                                    <option selected>Selecciona el género</option>
                                                    <option value="Femenino">Femenino</option>
                                                    <option value="Masculino">Masculino</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="Direccion" class="form-label"><i class="fa-solid fa-location-crosshairs" style="color: #74C0FC;"></i> Direccion</label>
                                                <input type="text" class="form-control" id="Direccion" name="direccion" placeholder="Escribe tu dirección">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="fechahora">Fechas y horas disponibles</label>
                                                <select class="form-control" id="fechahora" name="horafecha" style="font-size: 16px;">
                                                    <option selected>Selecciona las fechas y horas disponibles</option>
                                                    <?php echo $optionsHtml; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="PMotivo"><i class="fa-solid fa-comment" style="color: #74C0FC;"></i> Motivo de la cita médica</label>
                                                <input type="text" class="form-control" id="PMotivo" name="motivo" placeholder="Danos a conocer el motivo por el cual requieres la cita médica" required>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Registrar Cita Particular</button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    <section id="consultar" class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-sm-12">
                <div class="card form-container">
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-4">
                            <img src="../../images/LogoH.png" alt="Logo" height="80" width="80">
                        </div>
                        <form id="consultaForm">
                            <h3 class="text-center text-secondary">Consultar cita</h3>
                            <div class="mb-3">
                                <label for="buscarDocumento"><i class="fa-solid fa-id-card" style="color: #74C0FC;"></i> Número de documento</label>
                                <input type="number" class="form-control" id="buscarDocumento" name="buscar" placeholder="Escribe el número de documento para poder consultar tu cita médica" required>
                            </div>
                            
                        </form>

                        <!-- Contenedor para mostrar los resultados de la búsqueda -->
                        <div id="resultadoConsulta" class="mt-4"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <section id="cancelar" class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-sm-12">
                    <div class="card form-container">
                        <div class="card-body">
                            <div class="d-flex justify-content-center mb-4">
                                <img src="../../images/LogoH.png" alt="Logo" height="80" width="80">
                            </div>
                            <h3 class="text-center text-secondary">Cancelar Cita</h3>

                            <form method="post" action="" id="cancelarCitaForm">
                                <div class="mb-3">
                                    <label for="Documento" class="form-label">Número de Documento</label>
                                    <input type="number" class="form-control" id="Documento" name="documento" placeholder="Escribe tu número de documento para poder cancelar tu cita" required>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-danger" name="buscar_cancelar">Buscar Cita</button>
                                </div>
                                <hr>
                            </form>
                            <div id="resultadosCitas">

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../Vista/js/FormularioCita.js"></script>
    




</body>

</html>