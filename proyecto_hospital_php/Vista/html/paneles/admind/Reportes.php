<?php
require_once '../../../../Modelo/conexion.php'; // Archivo donde está la conexión a la base de datos
require_once '../../../../Modelo/Reporte.php'; // Archivo con la clase Reporte
require_once '../../../../Vista/fpdf/fpdf.php'; // Archivo de la librería FPDF

// Crear una instancia de Reporte con la conexión a la base de datos
$reporte = new Reporte($conexion);

// Obtener el mes y año actual
$mes = date('m'); // Mes actual en formato numérico (01 a 12)
$anio = date('Y'); // Año actual en formato de 4 dígitos

// Obtener citas del mes actual
$citas = $reporte->obtenerCitasDelMes($mes, $anio);

// Obtener pacientes del mes actual
$pacientes = $reporte->obtenerPacientesDelMes($mes, $anio);

// Obtener estadísticas
$totalCitas = $reporte->contarCitasDelMes($mes, $anio);
$totalPacientes = $reporte->contarPacientesDelMes($mes, $anio);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Agregar el logo del hospital
$logoPath = '../../../../Vista/images/LogoH.png';  // Ruta del logo
$pdf->Image($logoPath, 10, 8, 33); // Agregar imagen en la posición (10, 8) y tamaño de 33 mm de ancho
$pdf->Ln(20); // Espacio para dejar margen debajo del logo

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Reporte Mensual de Citas y Pacientes', 0, 1, 'C');
$pdf->Ln(5); // Espacio

// Mes y Año en la información del reporte
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Mes: ' . $mes . ' | Año: ' . $anio, 0, 1, 'C');
$pdf->Ln(10); // Espacio

// Estadísticas
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Estadísticas del Mes', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Total de Citas: $totalCitas", 0, 1);
$pdf->Cell(0, 10, "Total de Pacientes Registrados: $totalPacientes", 0, 1);
$pdf->Ln(10); // Espacio

// Citas
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Citas del Mes', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);

// Listado de citas
foreach ($citas as $cita) {
    $pdf->Cell(0, 10, 'ID Cita: ' . $cita['ID_cita'], 0, 1);
    $pdf->Cell(0, 10, 'Paciente ID: ' . $cita['ID_Paciente'], 0, 1);
    $pdf->Cell(0, 10, 'Fecha y Hora: ' . $cita['Fecha_hora'], 0, 1);
    $pdf->Cell(0, 10, 'Motivo: ' . $cita['Motivo'], 0, 1);
    $pdf->Cell(0, 10, 'Área de Salud: ' . $cita['Area_salud'], 0, 1);
    $pdf->Cell(0, 10, 'Asistencia: ' . $cita['Asistencia'], 0, 1);
    $pdf->Ln(10); // Espacio entre citas
}

// Espacio entre secciones
$pdf->Ln(10);

// Pacientes
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Pacientes Registrados en el Mes', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);

// Listado de pacientes
foreach ($pacientes as $paciente) {
    $pdf->Cell(0, 10, 'ID Paciente: ' . $paciente['ID_Paciente'], 0, 1);
    $pdf->Cell(0, 10, 'Nombre: ' . $paciente['Nombres'], 0, 1);
    $pdf->Cell(0, 10, 'Fecha de Registro: ' . $paciente['fecha_registro'], 0, 1);
    $pdf->Ln(10); // Espacio entre pacientes
}

// Guardar el archivo PDF en el servidor
$filename = '../../../../Vista/html/Reportes/Reporte_x' . $mes . '_' . $anio . '.pdf';
$pdf->Output('F', $filename);

// Registrar el reporte en la base de datos
$reporte->guardarReporte($filename);

// Redirigir al panel de control o mostrar mensaje de éxito
header('Location: ../../../../Vista/html/paneles/admin/index.php');
exit();
?>
<!-- Sección de Reportes -->
<div class="container mt-5">
                    <?php
                    // Filtrar los reportes por fecha
                    $reportFilesFiltered = array_filter($reportFiles, function ($file) use ($fechaLimite) {
                        return archivoDentroDelPeriodo($file, $fechaLimite);
                    });
                    ?>
                    <center>
                        <h2 class="mb-4">Reportes Generados</h2>
                    </center>
                    <ul class="list-group">
                        <?php if (!empty($reportFilesFiltered)) : ?>
                            <?php foreach ($reportFilesFiltered as $file) : ?>
                                <li class="list-group-item">
                                    <?php $filename = basename($file); ?>
                                    <a href="<?= $reportDir . $filename; ?>" class="btn btn-primary" download="<?= $filename; ?>">
                                        Descargar <?= htmlspecialchars($filename); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li class="list-group-item">No hay reportes disponibles en los últimos 30 días.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>