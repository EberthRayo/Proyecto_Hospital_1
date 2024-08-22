<?php
session_start();
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';
include '../../../../Controlador/LoginAdminController.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Debe iniciar sesión para acceder a esta página.";
    header('Location: ../../../../Vista/html/paneles/Login_admin.php'); // Redirige al login o a una página de error
    exit();
}
// Crear instancia del controlador de login admin

$usuario_id = $_SESSION['user_id'];
$loginAdmin = new LoginAdminController($conexion);
$datosUsuarioAdmin = $loginAdmin->obtenerDatosUsuarioAdmin($conexion, $usuario_id);
$nombreUsuario = $datosUsuarioAdmin['nombre'] ?? 'Desconocido';
$fotoPerfil = $datosUsuarioAdmin['foto_perfil'] ?? 'default.jpg';
$rol = $datosUsuarioAdmin['tipo_usuario'] ?? 'No definido';

$citaController = new CitaController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar'])) {
        $datos_fechahora_disponible = [
            'fecha_hora' => $_POST['fecha_hora'],
            'disponible' => $_POST['disponible']
        ];
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                '<strong>Éxito:</strong> ' . $_SESSION['mensaje'] .
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' .
                '<span aria-hidden="true">&times;</span></button></div>';

            // Limpiar el mensaje de la sesión después de mostrarlo
            unset($_SESSION['mensaje']);
        }
        $citaController->registrar_fechahora_disponible($datos_fechahora_disponible);
    } elseif (isset($_POST['eliminarFechaHora'])) {
        $citaController->eliminar_FechaHora($_POST['ID_Disponibilidad']);
    }
}

$fechahoras = $citaController->listar_fechahora();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control ADMIN</title>
    <link rel="icon" href="../../../../Vista/images/LogoH.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Animate.css (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle (con Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tempus Dominus CSS y JS -->
    <link href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-5@6.0.0-alpha4/css/tempusdominus-bootstrap-5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-5@6.0.0-alpha4/js/tempusdominus-bootstrap-5.min.js"></script>

    <!-- FontAwesome Kit (opcional) -->
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../../../Vista/css/panel_admin.css">
</head>

<body>
<div class="d-flex">
        <div class="sidebar bg-dark text-white p-3">
            <nav class="nav flex-column" style="margin-top: 50px;">
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/index.php"><i class="fa-solid fa-chart-line"></i> Estadísticas</a>
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
                <a class="nav-link text-white active" href="../../../../Vista/html/paneles/admind/disponibilidad.php"><i class="fas fa-clock"></i> Disponibilidad</a>
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
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="../../../../Controlador/LogoutController.php" method="POST" style="display: inline;">
                                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
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


            <main class="container my-5">
            <div class="d-flex justify-content-between align-items-center" style="margin-left: 450px; margin-top: 70px;">
                <h1 class="mb-4">Gestión de Fechas y horas</h1>
            </div>

            <form class="d-flex mb-4" role="search" method="GET" action="pacientes.php" style="margin-left: 200px;">
                <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" name="query">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
            </form>

            <?php
            if (isset($_SESSION['mensaje'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                    '<strong>Éxito:</strong> ' . $_SESSION['mensaje'] .
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' .
                    '<span aria-hidden="true">&times;</span></button></div>';
                unset($_SESSION['mensaje']);
            }
            ?>

            <!-- Botón para abrir el modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDisponibilidad" style="margin-left: 190px; margin-bottom: 20px;">
                Agregar Disponibilidad
            </button>
            
            <!-- Modal -->
            <div class="modal fade" id="modalDisponibilidad" tabindex="-1" aria-labelledby="modalDisponibilidadLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalDisponibilidadLabel">Agregar Disponibilidad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="Fechahora" class="form-label">Fecha y Hora:</label>
                                    <input type="datetime-local" class="form-control" id="Fechahora" name="fecha_hora" required>
                                </div>
                                <div class="mb-3">
                                    <label for="disponible" class="form-label">Disponible:</label>
                                    <select class="form-control" id="disponible" name="disponible" required>
                                        <option value="1" selected>1</option>
                                    </select>
                                </div>
                                <button type="submit" name="agregar" class="btn btn-primary">Agregar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Fechas y Horas -->
            <div class="table-responsive" style="width: 85%; margin-left: 190px; border:10px">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID_Fecha Y Hora</th>
                            <th>Fecha</th>
                            <th>Disponible</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($fechahoras)) : ?>
                            <?php foreach ($fechahoras as $fechahora) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fechahora['ID_Disponibilidad_fecha']); ?></td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d h:i A', strtotime($fechahora['Fecha_hora']))); ?></td>
                                    <td><?php echo htmlspecialchars($fechahora['Disponible']); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="">
                                                <input type="hidden" name="ID_Disponibilidad" value="<?php echo htmlspecialchars($fechahora['ID_Disponibilidad_fecha']); ?>">
                                                <button type="submit" name="eliminarFechaHora" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3">No hay Fechas y horas registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="../../../../Vista/js/panel.js"></script>
        <script>
            function confirmarEliminacion() {
                return confirm('¿Estás seguro de que quieres eliminar esta cita?');
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var citasSection = document.getElementById('citas-section');
                var calendarioSection = document.getElementById('calendario-section');
                var showCitas = document.getElementById('showCitas');
                var showCalendario = document.getElementById('showCalendario');

                if (showCitas && showCalendario && citasSection && calendarioSection) {
                    showCitas.addEventListener('click', function() {
                        citasSection.style.display = 'block';
                        calendarioSection.style.display = 'none';
                    });

                    showCalendario.addEventListener('click', function() {
                        citasSection.style.display = 'none';
                        calendarioSection.style.display = 'block';
                    });
                }
            });
        </script>

</body>

</html>