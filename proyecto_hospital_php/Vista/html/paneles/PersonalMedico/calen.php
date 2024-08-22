<?php
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';

$citaController = new CitaController($conexion);

// Manejo de solicitudes POST para actualizar la asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_asistencia'])) {
        $idCita = $_POST['ID_cita'] ?? null;
        $asistencia = $_POST['Asistencia'] ?? null;
        if ($idCita && $asistencia) {
            $citaController->actualizar_asistencia($idCita, $asistencia);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        }
        exit();
    }
}

$citas = $citaController->listar_citas();
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
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../../Vista/css/panel_admin.css">
    <style>
        .logo-img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            width: 80%;
            /* Ajusta según sea necesario */
            text-align: center;
        }

        .table-responsive {
            margin: 0 auto;
        }

        table {
            width: 100%;
            margin: 0 auto;
        }
        .container {
            max-width: 90%;
        }

        .citas-section {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -20%);
            width: 100%;
            text-align: center;
        }

        table {
            margin-top: 0;
        }
        .table-responsive{
            margin-left: 270px;
            width: 80%;
      }
</style>
</head>

<body>
<div class="d-flex">
    <div class="sidebar bg-dark text-white p-3">
        <img src="../../../../Vista/images/LogoH.png" alt="Logo" class="d-inline-block mb-4" style="width: 70px; height: 70px; margin-top:50px; margin-left:50px;">
        <h2 class="mb-3">InnovaSalud <br> H.S.M.C</h2>
      <hr>
        <nav class="nav flex-column">
            <a class="nav-link text-white" href="../../../../Vista/html/paneles/PersonalMedico/index.php">
                <i class="fa-solid fa-chart-line"></i> Citas
            </a>
            <a class="nav-link text-white active" href="../../../../Vista/html/paneles/PersonalMedico/calen.php">
                <i class="fas fa-calendar-day"></i> Calendario
            </a>
            <a class="nav-link text-white" href="../../../../Vista/html/paneles/PersonalMedico/perfil.php">
                <i class="fas fa-user-md"></i> Perfil
            </a>
            <form action="../../../../Controlador/LogoutMedicoController.php" method="POST" class="mt-4">
                <button type="submit" class="btn btn-link text-white">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </nav>
    </div>
</div>
 
    
<div class="flex-grow-1 bg-light">
    <header class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <span>PANEL DE CONTROL: Personal Médico</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item d-flex align-items-center me-3">
                        <i class="fas fa-bell text-white me-2"></i> <!-- Ícono de campana -->
                        <span class="text-white" id="reloj"></span>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</div>


    <div id="calendario-section" class="section">
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

            // Script para mostrar la hora actual en tiempo real
            function actualizarReloj() {
                const ahora = new Date();
                let horas = ahora.getHours();
                const minutos = ahora.getMinutes().toString().padStart(2, '0');
                const segundos = ahora.getSeconds().toString().padStart(2, '0');
                const ampm = horas >= 12 ? 'PM' : 'AM';

                horas = horas % 12;
                horas = horas ? horas : 12; // Si es 0, entonces mostrar 12

                const horaActual = `${horas}:${minutos}:${segundos} ${ampm}`;
                document.getElementById('currentTime').textContent = horaActual;
            }

            setInterval(actualizarReloj, 1000);
            actualizarReloj(); // Actualiza inmediatamente al cargar la página
        </script>
        <script>
    function actualizarReloj() {
        const ahora = new Date();
        let horas = ahora.getHours();
        const minutos = ahora.getMinutes().toString().padStart(2, '0');
        const segundos = ahora.getSeconds().toString().padStart(2, '0');
        const ampm = horas >= 12 ? 'PM' : 'AM';

        horas = horas % 12;
        horas = horas ? horas : 12; // Si es 0, entonces mostrar 12

        const horaActual = `${horas}:${minutos}:${segundos} ${ampm}`;
        document.getElementById('reloj').textContent = horaActual;
    }

    setInterval(actualizarReloj, 1000);
    actualizarReloj(); // Actualiza inmediatamente al cargar la página
</script>
    </div>
</body>

</html>
