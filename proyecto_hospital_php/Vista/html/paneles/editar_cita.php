<?php
include '../../../../Modelo/conexion.php';

if (isset($_GET['id'])) {
    $ID_cita = $_GET['id'];

    $sql = "SELECT * FROM citas WHERE ID_cita = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $ID_cita);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cita = $result->fetch_assoc();
    } else {
        echo "No se encontró la cita.";
        exit();
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "ID de cita no proporcionado.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ced4da;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
        }
        .btn-primary:hover {
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }
        .btn-cancel {
            margin-right: 10px;
        }
        .title {
            margin-bottom: 30px;
            text-align: center;
            color: #343a40;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
        <div class="text-center mb-4">
                <h2 class="text-primary">Editar Cita</h2>
                <img src="../../../../Vista/images/LogoH.png" alt="Logo" height="80" width="80" class="img-fluid mb-3">
            </div>
            <form action="" method="POST">
                <input type="hidden" name="ID_cita" value="<?php echo $cita['ID_cita']; ?>">
                
                <div class="form-group">
                    <label for="ID_Paciente">ID Paciente:</label>
                    <input type="number" class="form-control" id="ID_Paciente" name="ID_Paciente" value="<?php echo $cita['ID_Paciente']; ?>" readonly>
                </div>
                
                
                <div class="form-group">
                    <label for="Area_salud">Área de Salud:</label>
                    <input type="text" class="form-control" id="Area_salud" name="Area_salud" value="<?php echo $cita['Area_salud']; ?>" >
                </div>
                
                <div class="form-group">
                    <label for="Motivo">Motivo:</label>
                    <input type="text" class="form-control" id="Motivo" name="Motivo" value="<?php echo $cita['Motivo']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="Estado_Cita">Estado de la Cita:</label>
                    <select class="form-control" id="Estado_Cita" name="Estado_Cita" required>
                        <option value="Valorada" <?php if ($cita['Estado_Cita'] == 'Valorada') echo 'selected'; ?> disabled>Valorada</option>
                        <option value="Confirmada" <?php if ($cita['Estado_Cita'] == 'Confirmada') echo 'selected'; ?>>Confirmada</option>
                        <option value="Cancelada" <?php if ($cita['Estado_Cita'] == 'Cancelada') echo 'selected'; ?>>Cancelada</option>
                    </select>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="../../../../Vista/html/paneles/admind/citas.php" class="btn btn-secondary btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
