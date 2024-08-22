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
    header('Location: ../../../../Vista/html/paneles/Login_admin.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];
$loginAdmin = new LoginAdminController($conexion);
$datosUsuarioAdmin = $loginAdmin->obtenerDatosUsuarioAdmin($conexion, $usuario_id);
$nombreUsuario = $datosUsuarioAdmin['nombre'] ?? 'Desconocido';
$fotoPerfil = $datosUsuarioAdmin['foto_perfil'] ?? 'default.jpg';
$rol = $datosUsuarioAdmin['tipo_usuario'] ?? 'No definido';

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
$especialidades = $citaController->listar_especialidad();
$consultorios = $citaController->obtener_consultorio();
$especialidades = $citaController->obtener_especialidad();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Administrador</title>
    <link rel="icon" href="../../../../Vista/images/LogoH.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../../Vista/css/panel_admin.css">
</head>

<body>
    <div class="d-flex">
        <div class="sidebar bg-dark text-white p-3">
            <nav class="nav flex-column" style="margin-top: 50px;">
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/index.php"><i class="fa-solid fa-chart-line"></i> Estadísticas</a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/admind/pacientes.php"><i class="fas fa-users"></i> Pacientes</a>
                <a class="nav-link text-white active" href="../../../../Vista/html/paneles/admind/medicos.php"><i class="fas fa-user-md"></i> Doctores</a>
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
                    <a class="navbar-brand navbar-light" href="index.php">Panel de Control: Administrador</a>
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
                                    <li><a class="dropdown-item " href="#">Configuración</a></li>
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

            <main class="container my-5" >
                <div class="d-flex justify-content-between align-items-center" style="margin-left: 450px; margin-top:100px;">
                    <h1 class="mb-4">Gestión de Médicos</h1>
                    <button class="btn btn-success"><a href="../../../../Vista/html/paneles/admind/AgregarMedico.php" class="text-white text-decoration-none">Agregar Médico</a></button>
                </div>

                <form class="d-flex mb-4" role="search" method="GET" action="medicos.php" style="margin-left: 200px;">
                    <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" name="query">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>

                <div class="table-responsive" style="width: 90%; margin-left:170px;">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Identificación</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Especialidad</th>
                                <th>Consultorio</th>
                                <th>Horario de Trabajo</th>
                                <th>Estado de Disponibilidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($medicos)) : ?>
                                <tr>
                                    <td colspan="10" class="text-center">No hay registros disponibles.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($medicos as $medico) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($medico['ID_Medico']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Nombres']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Correo_electrónico']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Teléfono_contacto']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Nombre_Especialidad']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Horario_trabajo']); ?></td>
                                        <td><?php echo htmlspecialchars($medico['Estado_disponibilidad']); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm"><a href="editar_medico.php?ID_Medico=<?php echo $medico['ID_Medico']; ?>" class="text-white text-decoration-none"><i class="fas fa-edit"></i> Editar</a></button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#eliminarModal_<?php echo $medico['ID_Medico']; ?>"><i class="fas fa-trash"></i> Eliminar</button>
                                        </td>
                                    </tr>

                                    <!-- Modal de confirmación de eliminación -->
                                    <div class="modal fade" id="eliminarModal_<?php echo $medico['ID_Medico']; ?>" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="eliminarModalLabel">Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Está seguro de que desea eliminar al médico <?php echo htmlspecialchars($medico['Nombres']) . ' ' . htmlspecialchars($medico['Apellidos']); ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="POST" action="medicos.php" class="d-inline">
                                                        <input type="hidden" name="ID_Medico" value="<?php echo $medico['ID_Medico']; ?>">
                                                        <button type="submit" name="eliminarMedico" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="../../../../Vista/js/panel.js"></script>
    
</body>
</html>
