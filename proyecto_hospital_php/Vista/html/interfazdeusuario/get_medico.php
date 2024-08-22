<?php
include '../../../Modelo/conexion.php';
include '../../../Controlador/CitaController.php';

session_start();

$citaController = new CitaController($conexion);

if (isset($_POST['especialidad'])) {
    $especialidadSeleccionada = $_POST['especialidad'];
    $medicos = $citaController->obtenerMedicosPorEspecialidad($especialidadSeleccionada);

    $medicosHtml = '<option selected>Seleccione un médico</option>';
    foreach ($medicos as $medico) {
        $medicosHtml .= '<option value="' . htmlspecialchars($medico['ID_Medico']) . '">' . htmlspecialchars($medico['Nombres']) . ' ' . htmlspecialchars($medico['Apellidos']) . '</option>';
    }

    echo $medicosHtml;
}
?>
