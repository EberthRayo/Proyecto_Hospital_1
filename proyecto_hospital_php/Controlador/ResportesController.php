<?php
require_once 'libs/fpdf.php';

class ReporteController {
    private $reporteModel;

    public function __construct() {
        // Asumiendo que tienes una clase de modelo para manejar la lógica de la base de datos
        require_once '../../../../Modelo/Reporte.php';
        require_once '../../../../Modelo/conexion.php';
        $this->reporteModel = new Reporte($conexion);
    }

    public function index() {
        $mes = date('m'); // Mes actual
        $anio = date('Y'); // Año actual

        $citas = $this->reporteModel->obtenerCitasDelMes($mes, $anio);
        $pacientes = $this->reporteModel->obtenerPacientesDelMes($mes, $anio);

        $data = [
            'citas' => $citas,
            'pacientes' => $pacientes
        ];

        // Renderizar la vista con los datos
        $this->view('reportes/index', $data);
    }

    public function generarPDF() {
        $mes = date('m'); // Mes actual
        $anio = date('Y'); // Año actual

        $citas = $this->reporteModel->obtenerCitasDelMes($mes, $anio);
        $pacientes = $this->reporteModel->obtenerPacientesDelMes($mes, $anio);

        // Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Reporte Mensual de Citas y Pacientes', 0, 1, 'C');

        // Citas
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Citas del Mes', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        foreach ($citas as $cita) {
            $pdf->Cell(0, 10, "ID: {$cita['ID_cita']}, Paciente: {$cita['ID_Paciente']}, Fecha: {$cita['ID_Disponibilidad-fecha']}", 0, 1);
        }

        // Espacio entre secciones
        $pdf->Cell(0, 10, '', 0, 1);

        // Pacientes
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Pacientes Registrados en el Mes', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        foreach ($pacientes as $paciente) {
            $pdf->Cell(0, 10, "ID: {$paciente['ID_Paciente']}, Nombre: {$paciente['Nombres']}, Fecha de Registro: {$paciente['fecha_registro']}", 0, 1);
        }

        // Guardar el archivo PDF en el servidor
        $filename = 'reportes/reporte_' . $mes . '_' . $anio . '.pdf';
        $pdf->Output('F', $filename);

        // Registrar el reporte en la base de datos
        $this->reporteModel->guardarReporte($filename);

        // Redirigir al panel de control o mostrar mensaje de éxito
        header('Location: /panel_control');
        exit();
    }
}
?>
