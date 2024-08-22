<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métodos de Pago</title>
    <link rel="icon" href="../images/LogoH.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../Vista/css/metodopago.css">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../../../Vista/images/LogoH.png" alt="Logo" width="50" height="40" class="d-inline-block align-text-top">
                Hospital Serafin Montaña Cuellar
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">
                            <i class="fas fa-arrow-left"></i> Regresar a la página principal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../Vista/html/interfazdeusuario/FormularioCita.php">
                            <i class="fas fa-home"></i> Regresar al formulario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modal">
                            <i class="fas fa-info-circle"></i> Acerca de
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Payment Method Section -->
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1>Método de Pago</h1>
            <p class="lead">Nuestro hospital utiliza exclusivamente Bancolombia como método de pago. Por favor, complete los detalles a continuación para proceder con el pago de su cita médica.</p>
            </div>
        <div class="card shadow-sm p-4">
            <div class="row align-items-center mb-4">
                <div class="col-auto">
                    <img src="../../../Vista/images/bancolombia.png" alt="Bancolombia" width="60" height="60">
                </div>
                <div class="col">
                    <h4 class="mb-0">Pagar con Bancolombia</h4>
                </div>
            </div>
            <form>
                <div class="mb-3">
                    <label for="nombreTitular" class="form-label"><i class="fas fa-user"></i> Nombre del Titular</label>
                    <input type="text" class="form-control" id="nombreTitular" placeholder="Nombre del Titular" required>
                </div>
                <div class="mb-3">
                    <label for="numeroCuenta" class="form-label"><i class="far fa-credit-card"></i> Número de Cuenta</label>
                    <input type="text" class="form-control" id="numeroCuenta" placeholder="Número de Cuenta" required>
                </div>
                <div class="mb-3">
                    <label for="montoPagar" class="form-label"><i class="fas fa-dollar-sign"></i> Monto a Pagar</label>
                    <input type="text" class="form-control" id="montoPagar" placeholder="Monto a Pagar" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Confirmar Pago</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Acerca de</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Para confirmar su cita médica, es necesario que realice el pago utilizando el método de pago disponible en esta sección. Por favor, complete todos sus datos para que su solicitud pueda ser procesada como una cita Particular y así asegurar que reciba la atención requerida.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
