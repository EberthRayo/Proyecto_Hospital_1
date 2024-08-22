<?php

header('Content-Type: application/json');
include __DIR__ . '/../Modelo/conexion.php'; // Asegúrate de que el archivo de conexión esté bien incluido

// Consulta para obtener las fechas con horarios disponibles
$sql = "
    SELECT DISTINCT 
        DATE(f.Fecha_hora) AS date
    FROM 
        fechahora_citas f
    LEFT JOIN 
        citas c ON f.ID_Disponibilidad_fecha = c.ID_Disponibilidad_fecha
    WHERE 
        c.ID_cita IS NULL
    ORDER BY 
        f.Fecha_hora
"; // Ajusta la consulta según tu esquema de base de datos

$result = $conexion->query($sql);

$dates = array();
while ($row = $result->fetch_assoc()) {
    $dates[] = $row['date'];
}

echo json_encode($dates);
?>
    