<?php
include_once "../Modelo/conexion.php"; 
include_once "../Controlador/CitaController.php";
session_start();

header('Content-Type: application/json');

// Verifica que el método de solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $documento = $_POST['documento'] ?? '';
    $eps = $_POST['eps'] ?? '';

    // Preparar consulta para validar la EPS y el documento del paciente
    $query = "SELECT * FROM entidades_promotoras_salud WHERE documento_paciente = ? AND Codigo = ?";
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param("ss", $documento, $eps);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // EPS válida, ahora obtenemos la información del paciente
            $queryPaciente = "SELECT * FROM pacientes WHERE ID_Paciente = ?";
            if ($stmtPaciente = $conexion->prepare($queryPaciente)) {
                $stmtPaciente->bind_param("s", $documento);
                $stmtPaciente->execute();
                $resultPaciente = $stmtPaciente->get_result();

                if ($resultPaciente->num_rows > 0) {
                    $paciente = $resultPaciente->fetch_assoc();
                    $response = [
                        'success' => true,
                        'message' => 'La validación de EPS fue exitosa, puedes crear tu cita médica.',
                        'data' => $paciente
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'No se encontró información del paciente.'
                    ];
                }
                $stmtPaciente->close();
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error al preparar la consulta de paciente.'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'El documento o la EPS no son válidos.'
            ];
        }
        $stmt->close();
    } else {
        $response = [
            'success' => false,
            'message' => 'Error al preparar la consulta de EPS.'
        ];
    }

    $conexion->close();

    echo json_encode($response);
    exit();
} else {
    $response = [
        'success' => false,
        'message' => 'Método de solicitud no permitido.'
    ];
    echo json_encode($response);
    exit();
}
?>
