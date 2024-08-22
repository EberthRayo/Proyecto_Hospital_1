<?php
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';
include '../../../../Controlador/LoginMedicoController.php';
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
    <!-- FontAwesome Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../../Vista/css/panel_admin.css">
    <style>
        .logo-img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto;
            /* Ajusta el espacio inferior */
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

        .table-responsive {
            margin-left: 270px;
            width: 80%;
        }
        #campanita {
    position: relative;
    font-size: 1.5em;
}

#campanita::after {
    content: attr(data-notificaciones);
    position: absolute;
    top: -10px;
    right: -10px;
    background: red;
    color: white;
    border-radius: 50%;
    padding: 0.2em 0.5em;
    font-size: 0.8em;
    display: none; /* Inicialmente oculto */
}

#campanita:not(:empty)::after {
    display: block; /* Muestra solo si hay notificaciones */
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
                <a class="nav-link text-white active" href="../../../../Vista/html/paneles/PersonalMedico/index.php">
                    <i class="fa-solid fa-chart-line"></i> Citas
                </a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/PersonalMedico/calen.php">
                    <i class="fas fa-calendar-day"></i> Calendario
                </a>
                <a class="nav-link text-white" href="../../../../Vista/html/paneles/PersonalMedico/perfil.php">
                    <i class="fas fa-user-md"></i> Perfil
                </a>
                <form id="logoutForm" action="../../../../Controlador/LogoutMedicoController.php" method="POST" style="display: inline;">
                    <button type="button" id="logoutButton" class="nav-link btn btn-link text-white">
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
                            <i class="fas fa-bell text-white me-2" id="campanita"></i> <!-- Ícono de campana -->
                            <span class="text-white" id="reloj"></span>
                        </li>

                    </ul>
                </div>
            </div>
        </header>
    </div>


    <main class="container my-5">
        <div class="table-responsive" style="margin-top: -500px;">
            <h1 class="mb-4">Gestión de Citas</h1>
            <center></center>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID Cita</th>
                        <th>ID Paciente</th>
                        <th>Motivo</th>
                        <th>Asistencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citas)) : ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay citas disponibles.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($citas as $cita) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cita['ID_cita']); ?></td>
                                <td><?php echo htmlspecialchars($cita['ID_Paciente']); ?></td>
                                <td><?php echo htmlspecialchars($cita['Motivo']); ?></td>
                                <td id="asistencia_<?php echo $cita['ID_cita']; ?>">
                                    <?php echo htmlspecialchars($cita['Asistencia']); ?>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="cambiarAsistencia(<?php echo $cita['ID_cita']; ?>, 'Asistió')">Asistió</button>
                                    <button class="btn btn-danger btn-sm" onclick="cambiarAsistencia(<?php echo $cita['ID_cita']; ?>, 'No Asistió')">No Asistió</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../../Vista/js/panelMedico.js"></script>

</body>

</html>