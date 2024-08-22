<?php 
require_once "../../Modelo/conexion.php";

if (isset($_POST['documento'])) {
    $documento = $_POST['documento'];

    $stmt = $conexion->prepare("
        SELECT 
            c.ID_cita AS numero_cita, 
            c.ID_Medico AS id_medico,
            c.Estado_Cita AS estado,
            p.Nombres AS nombre_paciente, 
            f.Fecha_Hora AS fecha_cita, 
            e.Nombre_Especialidad AS nombre_especialidad, 
            CONCAT(o.ID_Consultorio_M, ' ', o.Nombre) AS consultorio,
            CONCAT(m.Nombres, ' ', m.Apellidos) AS nombre_medico, 
            c.Motivo AS motivo
        FROM citas c
        INNER JOIN pacientes p ON c.ID_Paciente = p.ID_Paciente
        INNER JOIN fechahora_citas f ON c.ID_Disponibilidad_fecha = f.ID_Disponibilidad_fecha
        INNER JOIN especialidad_medico e ON c.ID_Especialidad_M = e.ID_Especialidad_M
        INNER JOIN medicos m ON c.ID_Medico = m.ID_Medico
        INNER JOIN consultorio o ON o.ID_Consultorio_M = m.ID_Consultorio_M
        WHERE c.ID_Paciente = ?
    ");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Formatear la fecha y hora a formato AM/PM
        $fechaHora = new DateTime($data['fecha_cita']);
        $data['fecha_cita'] = $fechaHora->format('Y-m-d h:i:s A');

        // Mensajes de instrucción basados en el estado
        $instrucciones = '';
        switch ($data['estado']) {
            case 'Confirmada':
                $instrucciones = "
                <strong>Instrucciones para su cita médica:</strong><br><br>
                Su cita está confirmada para el <strong>{$data['fecha_cita']}</strong>. Por favor, llegue 15 minutos antes de la hora programada.<br><br>
                
                <strong>Pasos a seguir:</strong><br>
                1. Dirígete a la consulta externa para realizar el proceso previo.<br>
                2. En consulta externa, te tomarán un pequeño diagnóstico de tus signos vitales.<br><br>
                
                <strong>Qué llevar:</strong><br>
                - Documento de identidad (por ejemplo, Tarjeta de identidad, Cédula de ciudadanía)<br>
                - Cualquier información médica relevante (por ejemplo, resultados de exámenes previos)<br>
                - Lista de medicamentos que estás tomando<br><br>
                
                Si por alguna razón no puedes asistir a la cita en la fecha establecida, te solicitamos que canceles tu cita lo antes posible. Puedes hacerlo accediendo a la sección de cancelación dentro de nuestra plataforma.<br><br>
                
                Si tienes alguna pregunta o necesitas reprogramar tu cita, por favor contáctanos a la brevedad.<br>
                ¡Te esperamos!
            ";
            

                break;
                case 'Validada':
                    $instrucciones = "
                        <strong>Instrucciones para su cita médica:</strong><br><br>
                        Su cita ha sido validada por nuestros profesionales. Actualmente, estamos en el proceso de evaluación para asegurarnos de que cumpla con todos los requisitos necesarios para su consulta.<br><br>
                        La fecha de su cita está programada para el <strong>{$data['fecha_cita']}</strong>. Mientras tanto, le pedimos que sea paciente y que revise los detalles de su cita. Prepárese adecuadamente para su consulta.<br><br>
                        Si por alguna razón no puede asistir a la cita en la fecha establecida, le solicitamos que cancele su cita lo antes posible. Puede hacerlo accediendo a la sección de cancelación dentro de nuestra plataforma.<br><br>
                        Para cualquier pregunta o si necesita asistencia adicional, no dude en contactarnos. Estamos aquí para ayudarle.
                    ";
                
            
                break;
            case 'Cancelada':
                $instrucciones = "Su cita ha sido cancelada. Al parecer no cumple con los requisitos del Hospital, La cita estaba programada para el {$data['fecha_cita']}. Comuníquese con el hospital para reprogramar.";
                break;
            default:
                $instrucciones = "El estado de su cita es desconocido. La cita estaba programada para el {$data['fecha_cita']}. Por favor, comuníquese con el hospital.";
                break;
        }
        $data['instrucciones'] = $instrucciones;

        echo json_encode($data);
    } else { 
        echo json_encode(['error' => 'No se encontró ninguna cita con ese número de documento.']);
    }
    $conexion->close();
} else {
    echo json_encode(['error' => 'No se proporcionó ningún documento.']);
}
?>
