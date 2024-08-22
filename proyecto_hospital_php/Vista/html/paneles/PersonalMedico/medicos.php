<?php
session_start();
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';


$citaController = new CitaController($conexion);

// Manejo de solicitudes POST para actualizar la asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_asistencia'])) {
        $idCita = $_POST['ID_cita'];
        $asistencia = $_POST['Asistencia'];
        $citaController->actualizar_asistencia($idCita, $asistencia);
        echo json_encode(['success' => true]);
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../../Vista/css/panel_admin.css">
</head>

<body>
<div class="d-flex">
    <div class="sidebar bg-dark text-white p-3">
        <nav class="nav flex-column" style="margin-top: 50px;">
            <form action="../../../../Vista/html/paneles/admind/medicos.php" method="GET" style="display: inline;">
                <button type="submit" class="nav-link btn btn-link text-white"><i class="fas fa-clock"></i> Citas</button>
            </form>

            <form action="../../../../Vista/html/paneles/admind/calendario.php" method="GET" style="display: inline;">
                <button type="submit" class="nav-link btn btn-link text-white"><i class="fas fa-calendar-alt"></i> Calendario</button>
            </form>

            <form action="../../../../Vista/html/paneles/admind/consultorios.php" method="GET" style="display: inline;">
                <button type="submit" class="nav-link btn btn-link text-white"><i class="fas fa-building"></i> Perfil</button>
            </form>

            <form action="../../../../Controlador/LogoutController.php" method="POST" style="display: inline;">
                <button type="submit" class="nav-link btn btn-link text-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
            </form>
        </nav>
    </div>
</div>
        <div class="flex-grow-1 bg-light">
            <header class="navbar navbar-expand-lg navbar-light bg-dark shadow-sm fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand navbar-light" href="index.php">Panel de Control: Medico</a>
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
                <h1 class="mb-4">Gestión de Citas</h1>

                <div class="table-responsive">
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
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="../../../../Vista/js/panel.js"></script>

    <script>
        function cambiarAsistencia(idCita, asistencia) {
            if (confirm(`¿Está seguro de que desea marcar la cita como ${asistencia}?`)) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'medicos.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                document.getElementById(`asistencia_${idCita}`).textContent = asistencia;
                            } else {
                                alert('Error al actualizar la asistencia.');
                            }
                        } else {
                            alert('Error en la solicitud.');
                        }
                    }
                };
                xhr.send(`update_asistencia=true&ID_cita=${idCita}&Asistencia=${asistencia}`);
            }
        }
    </script>
</body>

</html>
