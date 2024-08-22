<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnovaSalud H.S.M.C</title>
    <link rel="icon" href="../../images/LogoH.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
    <style>
        /* CSS optimizado y mejorado */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        #hero {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            overflow: hidden;
            text-align: center;
            color: white;
        }

        video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            
        }

        .capa {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #1c1c1d;
            opacity: 0.5;
            mix-blend-mode: overlay;
        }

        

        .promo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 40%;
            height: 50%;
            margin: auto;
            position: relative;
            z-index: 2;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            animation: slideIn 2s ease-in-out;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            background-color: #007bff;
            border-radius: 10px;
            overflow: hidden;
        }

        .dropdown-item {
            color: white;
            transition: background-color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <main id="hero">
        <div class="promo">
        <img src="../../images/LogoH.png" alt="Logo" class="logo" width="80px" height="80px">
            <h1>¡Bienvenido a InnovaSalud!</h1>
            <p class="lead mb-4">Estamos encantados de tenerte aquí.</p>
            <div class="dropdown">
                <button class="btn btn-primary btn-lg dropdown-toggle" type="button" id="dashboardSelector" data-bs-toggle="dropdown" aria-expanded="false">
                    Seleccione su perfil
                </button>
                <ul class="dropdown-menu" aria-labelledby="dashboardSelector">
                    <li><a class="dropdown-item" href="../../html/paneles/Login_admin.php">Administrador</a></li>
                    <li><a class="dropdown-item" href="../../html/paneles/Login_personal_Medico.php">Personal Médico</a></li>
                    <li><a class="dropdown-item" href="../../html/paneles/loginJ.php">Jefe Inmediato</a></li>
                </ul>
            </div>
        </div>
        <video muted autoplay loop>
            <source src="../../videos/fondo.mp4" type="video/mp4">
            Tu navegador no soporta la reproducción de video.
        </video>
        <div class="capa"></div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
