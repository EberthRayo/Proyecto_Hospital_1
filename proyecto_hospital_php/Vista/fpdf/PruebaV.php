<?php
require_once('fpdf.php');

// Clase personalizada para la generación del PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->Image('D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Vista/fpdf/Lo.jpg', 10, 10, 30);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Hospital Serafín Montaña Cuellar'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 5, utf8_decode('Ubicación: Calle 4 Nº 12-52, centro, San Luis Tolima, Tolima'), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Teléfono: +57 321 4277692'), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Correo: hospitalserafinsanluis@yahoo.es'), 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 10, utf8_decode("Reporte de Cita Médica"), 0, 1, 'C');
        $this->Ln(5);
        $this->SetDrawColor(0, 51, 102);
        $this->Line(10, 40, 200, 40);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetY(-15);
        $hoy = date('d/m/Y');
        $this->Cell(0, 10, utf8_decode($hoy), 0, 0, 'R');
    }

    public function ReportData($paciente, $datos_cita, $estado_cita)
    {
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 0);

        $mensaje = "Estimado/a paciente,\n\n";
        switch ($estado_cita) {
            case 'Valorada':
                $mensaje .= "Actualmente, su cita está en proceso de valoración por nuestros profesionales. Estará disponible para su revisión muy pronto. Le notificaremos por correo electrónico una vez que esté lista.\n";
                break;
            case 'Cancelada':
                $mensaje .= "Lamentamos informarle que su cita ha sido cancelada. Para obtener más información o para reprogramar su cita, por favor, comuníquese con nosotros.\n";
                break;
            case 'Confirmada':
                $fechaHora = isset($datos_cita['Fecha_hora']) ? date('d/m/Y h:i A', strtotime($datos_cita['Fecha_hora'])) : 'No disponible';
                $mensaje .= "Nos complace informarle que su cita ha sido confirmada para el día $fechaHora. Le rogamos que se presente a tiempo en nuestras instalaciones para recibir la atención necesaria.\n";
                break;
            default:
                $mensaje .= "Su cita está pendiente de valoración. Le mantendremos informado sobre cualquier actualización relevante.\n";
                break;
        }

        $mensaje .= "\nAtentamente,\nHospital Serafín Montaña Cuellar";
        $this->MultiCell(0, 10, utf8_decode($mensaje), 0, 'L');

        $this->Ln(10);

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 10, utf8_decode('Detalles de la Cita'), 0, 1, 'L');
        $this->Ln(5);

        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(0, 10, utf8_decode("Documento del Paciente: " . (isset($paciente['ID_Paciente']) ? $paciente['ID_Paciente'] : 'No disponible')), 0, 1, 'L');
        $this->Cell(0, 10, utf8_decode("Nombre del Paciente: " . (isset($paciente['Nombres']) ? $paciente['Nombres'] : 'No disponible')), 0, 1, 'L');

        $fechaHora = isset($datos_cita['Fecha_hora']) ? date('d/m/Y h:i A', strtotime($datos_cita['Fecha_hora'])) : 'No disponible';
        $this->Cell(0, 10, utf8_decode("Fecha y Hora de la Cita: " . $fechaHora), 0, 1, 'L');

        $this->Cell(0, 10, utf8_decode("Identificación Médico: " . (isset($datos_cita['ID_Medico']) ? $datos_cita['ID_Medico'] : 'No disponible')), 0, 1, 'L');
        $this->Cell(0, 10, utf8_decode("Nombre del Médico: " . (isset($datos_cita['Nombre_Medico']) ? $datos_cita['Nombre_Medico'] : 'No disponible')), 0, 1, 'L');

        $this->Cell(0, 10, utf8_decode("Especialidad Médica: " . (isset($datos_cita['Especialidad_M']) ? $datos_cita['Especialidad_M'] : 'No disponible')), 0, 1, 'L');

        $consultorio = isset($datos_cita['Nombre']) ? $datos_cita['Nombre'] : 'No disponible';
        $this->Cell(0, 10, utf8_decode("Consultorio: " . $consultorio), 0, 1, 'L');
    }
}

// Verificación y generación del reporte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'procesar_cita') {
    // Conexión a la base de datos
    $conexion = new mysqli('localhost', 'root', '', 'innovasaludhsmc');
    if ($conexion->connect_error) {
        die('Error de conexión: ' . $conexion->connect_error);
    }

    // Sanitización de datos
    $paciente = [
        'ID_Paciente' => htmlspecialchars(trim($_POST['documento'])),
        'Nombres' => htmlspecialchars(trim($_POST['nombre']))
    ];

    // Obtener la fecha y hora de la cita desde `fechahora_citas`
    $id_disponibilidad_fecha = htmlspecialchars(trim($_POST['horafecha']));
    $consulta_fecha_hora = $conexion->prepare("SELECT Fecha_hora FROM fechahora_citas WHERE ID_Disponibilidad_Fecha = ?");
    $consulta_fecha_hora->bind_param("i", $id_disponibilidad_fecha);
    $consulta_fecha_hora->execute();
    $resultado_fecha_hora = $consulta_fecha_hora->get_result();
    $fecha_hora = $resultado_fecha_hora->fetch_assoc()['Fecha_hora'];

    // Obtener los nombres y apellidos del médico desde `medicos`
    $id_medico = htmlspecialchars(trim($_POST['medico']));
    $consulta_medico = $conexion->prepare("SELECT Nombres, Apellidos FROM medicos WHERE ID_Medico = ?");
    $consulta_medico->bind_param("i", $id_medico);
    $consulta_medico->execute();
    $resultado_medico = $consulta_medico->get_result();
    $medico = $resultado_medico->fetch_assoc();
    $nombres_medico = $medico['Nombres'];
    $apellidos_medico = $medico['Apellidos'];

    // Obtener el nombre de la especialidad desde `especialidades_medicas`
    $id_especialidad = htmlspecialchars(trim($_POST['especialidad']));
    $consulta_especialidad = $conexion->prepare("SELECT Nombre_Especialidad FROM  especialidad_medico WHERE ID_Especialidad_M = ?");
    $consulta_especialidad->bind_param("i", $id_especialidad);
    $consulta_especialidad->execute();
    $resultado_especialidad = $consulta_especialidad->get_result();
    $nombre_especialidad = $resultado_especialidad->fetch_assoc()['Nombre_Especialidad'];

    // Preparar los datos de la cita
    $datos_cita = [
        'Fecha_hora' => $fecha_hora,
        'ID_Medico' => $id_medico,
        'Nombre_Medico' => $nombres_medico . ' ' . $apellidos_medico,
        'Especialidad_M' => $nombre_especialidad
    ];
    

    // Estado de la cita
    $estado_cita = 'Confirmada'; // Esto puede ser dinámico según la lógica del sistema

    // Generar PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Cita Médica');
    $pdf->ReportData($paciente, $datos_cita, $estado_cita);

    // Ruta del archivo en el servidor
    $ruta_archivo = 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/reportes_citas/Reporte_Cita_Medica_' . $_POST['documento'] . '.pdf';

    // Verificar si la ruta existe y es escribible
    if (!is_dir(dirname($ruta_archivo))) {
        mkdir(dirname($ruta_archivo), 0777, true);
    }

    // Guarda el archivo en la carpeta especificada
    $pdf->Output('F', $ruta_archivo);

    // Mensaje de éxito
    echo json_encode(['status' => 'success', 'message' => 'PDF generado y guardado correctamente.']);

    // Cierre de la conexión
    $conexion->close();
}
?>
?>
