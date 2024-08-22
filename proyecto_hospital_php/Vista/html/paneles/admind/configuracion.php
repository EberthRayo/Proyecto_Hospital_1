<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sitio</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        #configuracion {
            background-color: #ffffff;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 2rem;
        }
        #configuracion h2 {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }
        #configuracion .form-control {
            border-radius: 5px;
        }
        #configuracion button {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        #configuracion button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<!-- Configuración -->
<section id="configuracion" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Configuración del Sitio</h2>
        <form action="ruta_del_controlador" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="colorPrincipal" class="form-label">Color Principal:</label>
                <input type="color" id="colorPrincipal" name="colorPrincipal" class="form-control">
            </div>
            <div class="form-group">
                <label for="informacionGeneral" class="form-label">Información General:</label>
                <textarea id="informacionGeneral" name="informacionGeneral" class="form-control" rows="5" placeholder="Escribe aquí la información general del sitio..."></textarea>
            </div>
            <div class="form-group">
                <label for="logo" class="form-label">Logo del Sitio:</label>
                <input type="file" id="logo" name="logo" class="form-control-file">
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</section>

<!-- Bootstrap JS (opcional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
