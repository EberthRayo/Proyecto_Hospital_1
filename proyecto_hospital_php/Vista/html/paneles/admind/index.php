<?php
session_start();
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';
include_once '../../../../Controlador/LoginAdminController.php';
include_once '../../../../Controlador/EnviarconfirmacionController.php';

// Configura la zona horaria a Colombia
date_default_timezone_set('America/Bogota');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Debe iniciar sesión para acceder a esta página.";
    header('Location: ../../../../Vista/html/paneles/Login_admin.php');
    exit();
}

$tiempo_inactividad = 900;

if (isset($_SESSION['hora_inicio_sesion'])) {
    $tiempo_transcurrido = time() - $_SESSION['hora_inicio_sesion'];

    if ($tiempo_transcurrido > $tiempo_inactividad) {
        session_unset();
        session_destroy();
        header('Location: ../../../../Vista/html/paneles/Login_admin.php');
        exit();
    } else {
        $_SESSION['hora_inicio_sesion'] = time();
    }
}

$usuario_id = $_SESSION['user_id'];
$loginAdmin = new LoginAdminController($conexion);
$datosUsuarioAdmin = $loginAdmin->obtenerDatosUsuarioAdmin($conexion, $usuario_id);
$nombreUsuario = $datosUsuarioAdmin['nombre'] ?? 'Desconocido';
$fotoPerfil = $datosUsuarioAdmin['foto_perfil'] ?? 'default.jpg';
$rol = $datosUsuarioAdmin['tipo_usuario'] ?? 'No definido';

$authMiddlewarePath = $_SERVER['DOCUMENT_ROOT'] . '/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/AuthMiddlewareAdmin.php';

if (file_exists($authMiddlewarePath)) {
    require_once $authMiddlewarePath;
    if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
        if (class_exists('AuthMiddlewareAdmin')) {
            AuthMiddlewareAdmin::checkAuth();
            error_log("AuthMiddlewareAdmin cargado y autenticación verificada.");
        } else {
            error_log("Error: Clase AuthMiddlewareAdmin no encontrada.");
            echo "Error: Clase AuthMiddlewareAdmin no encontrada.";
            exit();
        }
    }
} else {
    error_log("Error: AuthMiddlewareAdmin.php no encontrado.");
    echo "Error: AuthMiddlewareAdmin.php no encontrado.";
    exit();
}

if (isset($_POST['validar'])) {
    $idCita = $_POST['id_cita'];
    $confirmacionCitaController = new CitasConfirmacionController();
    $confirmacionCitaController->validarYEnviarCorreo($idCita);
}

// Consulta para obtener el número de citas
$result_citas = $conexion->query("SELECT COUNT(*) as num_citas FROM citas");
$num_citas = $result_citas->fetch_assoc()['num_citas'];

// Consulta para obtener el número de pacientes
$result_pacientes = $conexion->query("SELECT COUNT(*) as num_pacientes FROM pacientes");
$num_pacientes = $result_pacientes->fetch_assoc()['num_pacientes'];

// Consulta para obtener el número de médicos
$result_medicos = $conexion->query("SELECT COUNT(*) as num_medicos FROM medicos");
$num_medicos = $result_medicos->fetch_assoc()['num_medicos'];

$result_usuarios = $conexion->query("SELECT COUNT(*) as num_usuarios FROM usuarios");
$num_usuarios = $result_usuarios->fetch_assoc()['num_usuarios'];

// Datos por días
$datos_dias = [];
$result_dias = $conexion->query("SELECT DAYNAME(created_at) as dia, COUNT(*) as num_citas FROM citas GROUP BY DAYNAME(created_at)");
while ($row = $result_dias->fetch_assoc()) {
    $datos_dias[$row['dia']] = $row['num_citas'];
}

// Datos por semanas
$datos_semanas = [];
$result_semanas = $conexion->query("SELECT WEEK(created_at) as semana, COUNT(*) as num_citas FROM citas GROUP BY WEEK(created_at)");
while ($row = $result_semanas->fetch_assoc()) {
    $datos_semanas[$row['semana']] = $row['num_citas'];
}

if (!isset($_SESSION['alerta_mostrada'])) {
    $_SESSION['alerta_mostrada'] = true;
    $_SESSION['ultimo_login'] = date('Y-m-d h:i:s A'); // Guarda la hora de último inicio de sesión en formato 12 horas
}

$hora_actual = date("Y-m-d h:i:s A");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control ADMIN</title>
    <link rel="icon" href="../../../../Vista/images/LogoH.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-5@6.0.0-alpha4/css/tempusdominus-bootstrap-5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-5@6.0.0-alpha4/js/tempusdominus-bootstrap-5.min.js"></script>
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../../Vista/css/panel_admin.css">

</head>

<body>
    <div class="d-flex">
        <div class="sidebar bg-dark text-white p-3">
            <nav class="nav flex-column" style="margin-top: 50px;">
                <a class="nav-link text-white active" href="../../../../Vista/html/paneles/admind/index.php"><i class="fa-solid fa-chart-line"></i> Estadísticas</a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/pacientes.php"><i class="fas fa-users"></i> Pacientes</a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/medicos.php"><i class="fas fa-user-md"></i> Doctores</a>
                <div class="nav-link dropdown">
                    <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="toggleMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar"></i> Citas
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="toggleMenu">
                        <li><a class="dropdown-item" href="citas.php#main-content">Citas</a></li>
                        <li><a class="dropdown-item" href="citas.php#calendario-section">Calendario</a></li>
                    </ul>
                </div>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/disponibilidad.php"><i class="fas fa-clock"></i> Disponibilidad</a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/consultorios.php"><i class="fas fa-building"></i> Consultorios</a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/usuarios.php"><i class="fas fa-users-cog"></i> Usuarios</a>
                <form id="logoutForm" action="../../../../Controlador/LogoutController.php" method="POST" style="display: inline;">
                    <button type="button" id="logoutButton" class="nav-link btn btn-link text-white">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </button>
                </form>
            </nav>
        </div>

        <div class="flex-grow-1 bg-light">
            <?php
            // Configuración de la zona horaria
            date_default_timezone_set('America/Bogota');
            $fecha_actual = date('Y-m-d');

            // Conexión a la base de datos
            include '../../../../Modelo/conexion.php';

            // Consulta SQL para obtener las citas del día actual
            $sql = mysqli_prepare($conexion, "SELECT citas.ID_Paciente, pacientes.Nombres 
                                  FROM citas
                                  JOIN pacientes ON citas.ID_Paciente = pacientes.ID_Paciente 
                                  WHERE DATE(citas.ID_Disponibilidad_fecha) = ? 
                                  ORDER BY citas.ID_Paciente ASC LIMIT 10");
            mysqli_stmt_bind_param($sql, 's', $fecha_actual);
            mysqli_stmt_execute($sql);
            $result = mysqli_stmt_get_result($sql);
            $count = mysqli_num_rows($result);
            ?>
            <header class="navbar navbar-expand-lg navbar-light bg-dark shadow-sm fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php">Panel de Control: Administrador</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo htmlspecialchars($nombreUsuario); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
                                    <li><a class="dropdown-item" href="../../../../Vista/html/paneles/admind/perfil.php">Perfil</a></li>
                                    <li><a class="dropdown-item" href="#">Configuración</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form id="logoutForm" action="../../../../Controlador/LogoutController.php" method="POST" style="display: inline;">
                                            <button type="button" id="logoutButton" class="nav-link btn btn-link text-black">
                                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($count > 0): ?>
                                        <span class="badge bg-danger"><?php echo $count; ?></span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <li class="dropdown-header">Citas de hoy</li>
                                    <?php if ($count > 0): ?>
                                        <?php while ($fila = mysqli_fetch_assoc($result)) { ?>
                                            <li class="dropdown-item"><?php echo htmlspecialchars($fila['Nombres']); ?></li>
                                        <?php } ?>
                                    <?php else: ?>
                                        <li class="dropdown-item">No hay citas para hoy</li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <span id="currentTime" class="nav-link"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>


            <div class="main-content">

                <div class="container mt-5">

                    <!-- Estadísticas -->
                    <div class="row">

                        <div class="row mt-4">
                            <div class="col-md-3">
                                <a href="citas.php" class="text-decoration-none">
                                    <div class="card text-white bg-primary mb-3">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="fas fa-calendar-check mr-2"></i> Citas
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Número de citas: <br> <?php echo $num_citas; ?></h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="pacientes.php" class="text-decoration-none">
                                    <div class="card text-white bg-success mb-3">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="fas fa-user-injured mr-2"></i> Pacientes
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Número de pacientes: <?php echo $num_pacientes; ?></h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="medicos.php" class="text-decoration-none">
                                    <div class="card text-white bg-warning mb-3">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="fas fa-user-md mr-2"></i> Médicos
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Número de médicos: <?php echo $num_medicos; ?></h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="usuarios.php" class="text-decoration-none">
                                    <div class="card text-white bg-info mb-3">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="fas fa-users mr-2"></i> Usuarios
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Número de usuarios: <?php echo $num_usuarios; ?></h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-6">
                                <canvas id="citasDiaChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="citasSemanaChart"></canvas>
                            </div>
                        </div>
                        <div class="container mt-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <canvas id="citasChart"></canvas>
                                </div>
                                <div class="col-md-4">
                                    <canvas id="pacientesChart"></canvas>
                                </div>
                                <div class="col-md-4">
                                    <canvas id="medicosChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <script>
                            // Mostrar SweetAlert con la bienvenida al administrador
                            document.addEventListener('DOMContentLoaded', function() {
                                <?php if (isset($_SESSION['alerta_mostrada']) && $_SESSION['alerta_mostrada']): ?>
                                    Swal.fire({
                                        title: '¡Bienvenido!',
                                        text: 'Hola, <?= htmlspecialchars($nombreUsuario) ?>. Bienvenido al panel de administración. Último inicio de sesión: <?= htmlspecialchars($_SESSION['ultimo_login']) ?>',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    }).then(function() {
                                        // Mostrar el modal después de la alerta
                                        var myModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                                        myModal.show();
                                    });
                                <?php endif; ?>
                            });
                        </script>
                        <script>
                            $(document).ready(function() {
                                // Mostrar el modal de bienvenida
                                $('#welcomeModal').modal('show');

                                // Datos obtenidos del PHP
                                const numCitas = <?php echo $num_citas; ?>;
                                const numPacientes = <?php echo $num_pacientes; ?>;
                                const numMedicos = <?php echo $num_medicos; ?>;
                                const numUsuarios = <?php echo $num_usuarios; ?>;

                                const datosDias = <?php echo json_encode($datos_dias); ?>;
                                const datosSemanas = <?php echo json_encode($datos_semanas); ?>;

                                // Gráfico de Citas
                                const ctxCitas = document.getElementById('citasChart').getContext('2d');
                                new Chart(ctxCitas, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Citas'],
                                        datasets: [{
                                            label: 'Número de Citas',
                                            data: [numCitas],
                                            backgroundColor: ['rgba(75, 192, 192, 0.2)'],
                                            borderColor: ['rgba(75, 192, 192, 1)'],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });

                                // Gráfico de Pacientes
                                const ctxPacientes = document.getElementById('pacientesChart').getContext('2d');
                                new Chart(ctxPacientes, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Pacientes'],
                                        datasets: [{
                                            label: 'Número de Pacientes',
                                            data: [numPacientes],
                                            backgroundColor: ['rgba(153, 102, 255, 0.2)'],
                                            borderColor: ['rgba(153, 102, 255, 1)'],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });

                                // Gráfico de Médicos
                                const ctxMedicos = document.getElementById('medicosChart').getContext('2d');
                                new Chart(ctxMedicos, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Médicos'],
                                        datasets: [{
                                            label: 'Número de Médicos',
                                            data: [numMedicos],
                                            backgroundColor: ['rgba(255, 159, 64, 0.2)'],
                                            borderColor: ['rgba(255, 159, 64, 1)'],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });

                                // Gráfico de Citas por Día
                                var ctxDia = document.getElementById('citasDiaChart').getContext('2d');
                                new Chart(ctxDia, {
                                    type: 'bar',
                                    data: {
                                        labels: <?php echo json_encode(array_keys($datos_dias)); ?>,
                                        datasets: [{
                                            label: 'Citas por día',
                                            data: <?php echo json_encode(array_values($datos_dias)); ?>,
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });

                                // Gráfico de Citas por Semana
                                var ctxSemana = document.getElementById('citasSemanaChart').getContext('2d');
                                new Chart(ctxSemana, {
                                    type: 'bar',
                                    data: {
                                        labels: <?php echo json_encode(array_keys($datos_semanas)); ?>,
                                        datasets: [{
                                            label: 'Citas por semana',
                                            data: <?php echo json_encode(array_values($datos_semanas)); ?>,
                                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                            borderColor: 'rgba(153, 102, 255, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            });
                        </script>
                        <script src="../../../../Vista/js/panel.js"></script>

</body>

</html>