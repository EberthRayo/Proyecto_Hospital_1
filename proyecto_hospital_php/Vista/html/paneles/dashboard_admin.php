<?php
// Simulando datos para demostración
$num_citas = 50;
$num_pacientes = 200;
$num_medicos = 20;

// Simulando datos por días y semanas
$datos_dias = [
    'Lunes' => ['citas' => 10, 'pacientes' => 30, 'medicos' => 5],
    'Martes' => ['citas' => 15, 'pacientes' => 35, 'medicos' => 6],
    'Miércoles' => ['citas' => 20, 'pacientes' => 40, 'medicos' => 7],
    'Jueves' => ['citas' => 25, 'pacientes' => 45, 'medicos' => 8],
    'Viernes' => ['citas' => 30, 'pacientes' => 50, 'medicos' => 9],
];

$datos_semanas = [
    'Semana 1' => ['citas' => 50, 'pacientes' => 100, 'medicos' => 15],
    'Semana 2' => ['citas' => 60, 'pacientes' => 110, 'medicos' => 16],
    'Semana 3' => ['citas' => 70, 'pacientes' => 120, 'medicos' => 17],
    'Semana 4' => ['citas' => 80, 'pacientes' => 130, 'medicos' => 18],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .navbar {
            background-color: #343a40;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand, .navbar-toggler-icon, .navbar-nav .nav-link {
            color: #ffffff;
        }
        .navbar-nav .nav-link:hover {
            color: #cccccc;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            width: 250px;
            padding-top: 60px;
        }
        .sidebar .nav-link {
            color: #ffffff;
            padding: 15px;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
        .stat-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .stat-card h5 {
            margin-bottom: 0.5rem;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a class="nav-link" href="pacientes.php"><i class="fas fa-users"></i> Pacientes</a>
            <a class="nav-link" href="medicos.php"><i class="fas fa-user-md"></i> Doctores</a>
            <a class="nav-link" href="citas.php"><i class="fas fa-calendar-check"></i> Citas</a>
            <a class="nav-link" href="#"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </nav>
    </div>
    <header class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="index.php">ADMIN</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Usuario
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Perfil</a>
                        <a class="dropdown-item" href="#">Configuración</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Cerrar sesión</a>
                    </div>
                </li>
            </ul>
        </div>
    </header>

    <div class="main-content">
        <div class="container mt-5">
            <!-- Estadísticas -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card">
                        <h5>Citas</h5>
                        <canvas id="citasChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h5>Pacientes</h5>
                        <canvas id="pacientesChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h5>Médicos</h5>
                        <canvas id="medicosChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Estadísticas combinadas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="stat-card">
                        <h5>Estadísticas Combinadas</h5>
                        <canvas id="combinedChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Estadísticas por días -->
            <div class="row">
                <div class="col-md-12">
                    <div class="stat-card">
                        <h5>Estadísticas por Días</h5>
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Estadísticas por semanas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="stat-card">
                        <h5>Estadísticas por Semanas</h5>
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos simulados
        const numCitas = <?php echo $num_citas; ?>;
        const numPacientes = <?php echo $num_pacientes; ?>;
        const numMedicos = <?php echo $num_medicos; ?>;

        const datosDias = <?php echo json_encode($datos_dias); ?>;
        const datosSemanas = <?php echo json_encode($datos_semanas); ?>;

        // Configuración del gráfico de citas
        const citasCtx = document.getElementById('citasChart').getContext('2d');
        const citasChart = new Chart(citasCtx, {
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

        // Configuración del gráfico de pacientes
        const pacientesCtx = document.getElementById('pacientesChart').getContext('2d');
        const pacientesChart = new Chart(pacientesCtx, {
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

        // Configuración del gráfico de médicos
        const medicosCtx = document.getElementById('medicosChart').getContext('2d');
        const medicosChart = new Chart(medicosCtx, {
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

        // Configuración del gráfico combinado
        const combinedCtx = document.getElementById('combinedChart').getContext('2d');
        const combinedChart = new Chart(combinedCtx, {
            type: 'bar',
            data: {
                labels: ['Citas', 'Pacientes', 'Médicos'],
                datasets: [{
                    label: 'Estadísticas Combinadas',
                    data: [numCitas, numPacientes, numMedicos],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
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

        // Configuración del gráfico por días
        const dailyLabels = Object.keys(datosDias);
        const dailyCitas = dailyLabels.map(day => datosDias[day].citas);
        const dailyPacientes = dailyLabels.map(day => datosDias[day].pacientes);
        const dailyMedicos = dailyLabels.map(day => datosDias[day].medicos);

        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [
                    {
                        label: 'Citas',
                        data: dailyCitas,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pacientes',
                        data: dailyPacientes,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Médicos',
                        data: dailyMedicos,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Configuración del gráfico por semanas
        const weeklyLabels = Object.keys(datosSemanas);
        const weeklyCitas = weeklyLabels.map(week => datosSemanas[week].citas);
        const weeklyPacientes = weeklyLabels.map(week => datosSemanas[week].pacientes);
        const weeklyMedicos = weeklyLabels.map(week => datosSemanas[week].medicos);

        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [
                    {
                        label: 'Citas',
                        data: weeklyCitas,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pacientes',
                        data: weeklyPacientes,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Médicos',
                        data: weeklyMedicos,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
