<?php
include '../../../../Modelo/conexion.php';
include_once '../../../../Controlador/CitaController.php';

$citaController = new CitaController($conexion);

// Obtener el número de notificaciones pendientes
$notificacionesPendientes = $citaController->contarNotificacionesPendientes();

echo json_encode(['notificaciones' => $notificacionesPendientes]);
?>
