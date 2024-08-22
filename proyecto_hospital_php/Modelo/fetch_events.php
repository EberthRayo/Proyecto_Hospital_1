<?php
include __DIR__ . '/../Modelo/conexion.php';

$query = "
    SELECT 
        c.ID_cita, 
        f.Fecha_hora, 
        p.Nombres,
        c.Estado_Cita
    FROM 
        citas c
    JOIN 
        fechahora_citas f ON c.ID_Disponibilidad_fecha = f.ID_Disponibilidad_fecha
    JOIN 
        pacientes p ON c.ID_Paciente = p.ID_Paciente
";

$result = $conexion->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    // Asume que 'Fecha_hora' ya está en formato ISO 8601
    $color = 'grey'; 
    switch (trim(strtolower($row['Estado_Cita']))) {
        case 'valorada':
            $color = 'blue';
            break;
        case 'confirmada':
            $color = 'green';
            break;
        case 'cancelada':
            $color = 'red';
            break;
    }

    $events[] = [
        'title' => $row['Nombres'],
        'start' => $row['Fecha_hora'],
        'color' => $color
    ];
}

$conexion->close();

// Set content type to JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
