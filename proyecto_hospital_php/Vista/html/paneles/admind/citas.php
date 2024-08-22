<?php
session_start();
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/LoginAdminController.php';
include_once '../../../../Controlador/LoginAdminController.php';


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

$conexion->close();

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FontAwesome Kit (opcional) -->
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>

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
                        <li><a class="dropdown-item" href="#" id="showCitas">Citas</a></li>
                        <li><a class="dropdown-item" href="#" id="showCalendario">Calendario</a></li>
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
            <header class="navbar navbar-expand-lg navbar-light bg-dark shadow-sm fixed-top ">
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
                                        <form action="../../../../Controlador/LogoutController.php" method="POST" style="display: inline;">
                                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($count > 0) : ?>
                                        <span class="badge bg-danger"><?php echo $count; ?></span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <li class="dropdown-header">Citas de hoy</li>
                                    <?php if ($count > 0) : ?>
                                        <?php while ($fila = mysqli_fetch_assoc($result)) { ?>
                                            <li class="dropdown-item"><?php echo htmlspecialchars($fila['Nombres']); ?></li>
                                        <?php } ?>
                                    <?php else : ?>
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


            <div id="citas-section" class="section" style="display: block;margin-left: 200px;">
                <div class="main-content"></div>
                <main class="container my-5">
                    <div class="d-flex justify-content-between align-items-center" style="margin-left: 400px; margin-top:100px;">
                        <h1 class="mb-4">Gestión de citas</h1>

                    </div>

                    <form class="d-flex mb-4" role="search" method="GET" action="citas.php" style="margin-left: 100px;">
                        <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar" name="query">
                        <button class="btn btn-outline-success" type="submit">Buscar</button>
                    </form>

                    <div class="table-responsive" style="width: 90%;margin-left:70px; ">
                        <table class="table table-bordered">
                            <thead class="table-dark">

                                <tr>
                                    <th>ID Cita</th>
                                    <th>ID Paciente</th>
                                    <th>Fecha y Hora</th>
                                    <th>ID Médico</th>
                                    <th>Nombre Médico </th>
                                    <th>Especialidad </th>
                                    <th>Motivo</th>
                                    <th>Asistencia</th>
                                    <th>Fecha Creada</th>
                                    <th>Estado de la Cita</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include '../../../../Modelo/conexion.php';

                                $query = isset($_GET['query']) ? $_GET['query'] : '';

                                // Adjust the SQL query to match the number of placeholders
                                $sql = "SELECT c.ID_cita, c.ID_Paciente, c.ID_Disponibilidad_fecha, f.Fecha_hora, c.ID_Medico, m.Nombres, c.ID_Especialidad_M, e.Nombre_Especialidad, c.Motivo, c.Estado_Cita, c.created_at, c.Asistencia
                                FROM citas c
                                JOIN fechahora_citas f ON c.ID_Disponibilidad_fecha = f.ID_Disponibilidad_fecha
                                JOIN especialidad_medico e ON c.ID_Especialidad_M = e.ID_Especialidad_M
                                JOIN medicos m ON c.ID_Medico = m.ID_Medico
                                WHERE c.ID_cita LIKE ? OR 
                                    c.ID_Paciente LIKE ? OR 
                                    c.ID_Disponibilidad_fecha LIKE ? OR 
                                    f.Fecha_hora LIKE ? OR 
                                    c.ID_Medico LIKE ? OR 
                                    m.Nombres LIKE ? OR
                                    c.ID_Especialidad_M LIKE ? OR 
                                    e.Nombre_Especialidad LIKE ? OR 
                                    c.Motivo LIKE ? OR 
                                    c.Estado_Cita LIKE ? OR
                                    c.Asistencia LIKE ?";

                                $stmt = $conexion->prepare($sql);

                                // Adjust the number of bind parameters to match the placeholders
                                $likeQuery = "%" . $query . "%";
                                $stmt->bind_param("sssssssssss", $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery, $likeQuery);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["ID_cita"] . "</td>";
                                        echo "<td>" . $row["ID_Paciente"] . "</td>";
                                        $fechaHora = date("d-m-Y h:i A", strtotime($row["Fecha_hora"]));
                                        echo "<td>" . $fechaHora . "</td>";
                                        echo "<td>" . $row["ID_Medico"] . "</td>";
                                        echo "<td>" . $row["Nombres"] . "</td>";
                                        echo "<td>" . $row["Nombre_Especialidad"] . "</td>";
                                        echo "<td>" . $row["Motivo"] . "</td>";
                                        echo "<td>" . $row["Asistencia"] . "</td>";
                                        $fechaHoracreado = date("d-m-Y h:i A", strtotime($row["created_at"]));
                                        echo "<td>" . $fechaHoracreado . "</td>";
                                        echo "<td>" . $row["Estado_Cita"] . "</td>";
                                        echo '<td>
                <div class="btn-group" role="group" aria-label="Acciones">
                    <a href="editar_cita.php?id=' . $row["ID_cita"] . '" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="eliminar_cita.php?id=' . $row["ID_cita"] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de eliminar esta cita?\');">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
              </td>';
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10'>No se encontraron registros</td></tr>";
                                }

                                $conexion->close();
                                ?>

                        </table>

                    </div>
            </div>
        </div>




    </div>
    <div id="calendario-section" class="section" style="display: none;">
        <div id="calendar-container">
            <center>
                <h1>Calendario de citas</h1>
            </center>
            <hr>
            <div id="calendar-controls">
                <button id="prev-month">&laquo; Anterior</button>
                <span id="current-month-year"></span>
                <button id="next-month">Siguiente &raquo;</button>
            </div>
            <div id="calendar"></div>
        </div>

        <script>
            let currentMonth = new Date().getMonth();
            let currentYear = new Date().getFullYear();

            function generateCalendar(month, year, events) {
                const calendar = document.getElementById('calendar');
                calendar.innerHTML = '';

                const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const startDay = new Date(year, month, 1).getDay();

                // Create header
                const header = document.createElement('div');
                header.className = 'calendar-header';
                for (const day of daysOfWeek) {
                    const dayDiv = document.createElement('div');
                    dayDiv.textContent = day;
                    header.appendChild(dayDiv);
                }
                calendar.appendChild(header);

                // Create days
                const days = document.createElement('div');
                days.className = 'calendar-days';
                for (let i = 0; i < startDay; i++) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'day';
                    days.appendChild(emptyDiv);
                }
                for (let i = 1; i <= daysInMonth; i++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'day';
                    dayDiv.innerHTML = `<div class="day-number">${i}</div>`;

                    // Add events to the day
                    const eventList = events.filter(event => {
                        const eventDate = new Date(event.start);
                        return eventDate.getDate() === i && eventDate.getMonth() === month && eventDate.getFullYear() === year;
                    });
                    eventList.forEach(event => {
                        const eventDiv = document.createElement('div');
                        eventDiv.className = 'event';

                        // Set color based on the status of the appointment
                        eventDiv.style.backgroundColor = event.color;
                        eventDiv.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-time">${new Date(event.start).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true })}</div>
                    `;
                        dayDiv.appendChild(eventDiv);
                    });

                    days.appendChild(dayDiv);
                }
                calendar.appendChild(days);
                document.getElementById('current-month-year').textContent = `${new Intl.DateTimeFormat('es-ES', { month: 'long', year: 'numeric' }).format(new Date(year, month))}`;
            }

            function fetchEventsAndGenerateCalendar(month, year) {
                fetch('../../../../Modelo/fetch_events.php')
                    .then(response => response.json())
                    .then(events => {
                        console.log('Eventos recibidos:', events); // Debug: Verificar datos recibidos
                        generateCalendar(month, year, events);
                    })
                    .catch(error => console.error('Error fetching events:', error));
            }


            document.getElementById('prev-month').addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                fetchEventsAndGenerateCalendar(currentMonth, currentYear);
            });

            document.getElementById('next-month').addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                fetchEventsAndGenerateCalendar(currentMonth, currentYear);
            });

            // Initialize calendar
            fetchEventsAndGenerateCalendar(currentMonth, currentYear);
        </script>
    </div>


</body>

</html>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../../../../Vista/js/panel.js"></script>
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