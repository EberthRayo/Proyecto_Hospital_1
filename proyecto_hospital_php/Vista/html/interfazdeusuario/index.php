    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InnovaSalud H.S.M.C</title>
        <link rel="icon" href="Vista/images/LogoH.png" type="image/png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <script defer src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://kit.fontawesome.com/5dc078d407.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="Vista/css/estilos.css">

    </head>

    <body>

        <!-- Preloader -->
        <div class="container_loader">
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="ring"></div>
            <span class="loading">Cargando..</span>
        </div>

        <!-- Header -->
        <header class="animate__animated animate__fadeInDown">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-4 col-sm-12 text-center text-md-start mb-2 mb-md-0">
                        <p class="m-0">Bienvenidos, su vida está en buenas manos</p>
                    </div>
                    <div class="col-md-8 col-sm-12 d-flex justify-content-center justify-content-md-end align-items-center">
                        <div class="contact-info d-flex flex-column flex-md-row align-items-center">
                            <span class="phone-icon me-md-3 mb-2 mb-md-0">
                                <a href="tel:+573214277692" class="text-white">
                                    <i class="fas fa-phone"></i> +57 321 4277692
                                </a>
                            </span>
                            <span class="date-icon">
                                <i class="bi bi-calendar-date"></i> Lunes - Sábado: 8:00 AM - 5:00 PM
                            </span>
                            
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="Vista/images/LogoH.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                    <span>InnovaSalud</span> H.S.M.C
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">

                        <li class="nav-item">
                            <a class="nav-link" href="#services">Servicios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">Acerca de Nosotros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#footer">Contacto</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Vista/html/interfazdeusuario/FormularioCita.php">Gestiona tu Cita</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Vista/html/paneles/index.php">Ingresar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Carousel -->
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" style="background-image: url('Vista/images/slider1.jpg');">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Comprometidos con su Salud</h2>
                        <p>Brindamos la mejor atención médica con un equipo altamente calificado.</p>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('Vista/images/slider2.jpg');">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Innovación en Cada Servicio</h2>
                        <p>Utilizamos tecnología de punta para ofrecer tratamientos de calidad.</p>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('Vista/images/slider3.jpg');">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Cuidamos de Usted</h2>
                        <p>Su bienestar es nuestra prioridad.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <br>
        <hr class="my-0">
        <!-- Services -->
        <section id="services" class="text-center">
            <div class="container">
                <h1 class="mb-5">Nuestros Servicios</h1>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="service-box animate__animated animate__fadeIn">
                            <i class="fas fa-stethoscope fa-3x mb-3"></i>
                            <h3>Consulta General</h3>
                            <p>Proveemos consultas médicas generales para todas sus necesidades de salud.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-box animate__animated animate__fadeIn">
                            <i class="fas fa-heartbeat fa-3x mb-3"></i>
                            <h3>Cardiología</h3>
                            <p>Especialistas en el cuidado del corazón, ofreciendo evaluaciones y tratamientos avanzados.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-box animate__animated animate__fadeIn">
                            <i class="fas fa-x-ray fa-3x mb-3"></i>
                            <h3>Radiología</h3>
                            <p>Diagnóstico por imagen de alta precisión para un tratamiento efectivo y seguro.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <hr class="my-0">
        <!-- About Us -->
        <section id="about" class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <img src="Vista/images/Medico1.jpg" class="img-fluid rounded" alt="Acerca de Nosotros">
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="about-content">
                            <h2 class="mb-4 text-success">Nuestra Institución</h2>
                            <p>En el Hospital Serafín Montaña Cuellar, nos dedicamos a proporcionar servicios de salud de calidad, centrados en el bienestar integral de nuestros pacientes. Con un equipo de profesionales altamente capacitados y una infraestructura moderna, nos esforzamos por ofrecer atención médica de primer nivel.</p>
                            <p>Nuestra misión es simple pero fundamental: brindar una atención médica compasiva, ética y efectiva que mejore la calidad de vida de nuestros pacientes. Valoramos la confianza que nuestros pacientes depositan en nosotros y trabajamos arduamente para mantener altos estándares de calidad y excelencia.</p>
                            <p>En InnovaSalud H.S.M.C, creemos en la importancia de la innovación y la mejora continua. Nos esforzamos por implementar tecnologías avanzadas y métodos de tratamiento modernos para garantizar la mejor atención posible para nuestros pacientes.</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h2 class="text-center mb-4">Nuestro Equipo</h2>
                    </div>
                    <div class="col-md-4">
                        <div class="team-member text-center">
                            <i class="fas fa-user-md fa-3x mb-3 text-success"></i>
                            <h4 class="mb-3">Especialistas</h4>
                            <p>Nuestro equipo de especialistas altamente calificados está dedicado a brindar atención médica experta y personalizada en diversas especialidades.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="team-member text-center">
                            <i class="fas fa-heartbeat fa-3x mb-3 text-danger"></i>
                            <h4 class="mb-3">Enfermería</h4>
                            <p>Nuestro equipo de enfermeras y enfermeros comprometidos proporciona atención compasiva y profesional a nuestros pacientes en todas las etapas de su atención médica.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="team-member text-center">
                            <i class="fas fa-user-nurse fa-3x mb-3 text-primary"></i>
                            <h4 class="mb-3">Personal Administrativo</h4>
                            <p>Nuestro equipo administrativo eficiente y dedicado garantiza el funcionamiento sin problemas de todas las operaciones hospitalarias para brindar un entorno seguro y cómodo para nuestros pacientes.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-md-4 text-center">
                        <a href="Vista/html/interfazdeusuario/PersonalHospitalario.php" class="btn btn-primary">Conoce a nuestro equipo</a>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-0">
        <br>
        <!-- Contact -->
        <footer id="footer" class="bg-dark text-light py-5">
            <div class="container">
                <div class="row">
                    <!-- Información de Contacto -->
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-4 mb-lg-0">
                        <h5 class="mb-3"><i class="fas fa-info-circle"></i> Información de Contacto</h5>
                        <ul class="list-unstyled">
                            <a href="Vista/html/interfazdeusuario/Google_maps.php" style="text-decoration: none;">
                                <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Calle 4 Nº 12-52, centro, San Luis Tolima, Tolima</li>
                            </a>
                            <a href="tel:+573214277692" style="text-decoration: none;">
                                <li class="mb-2"><i class="fas fa-phone me-2"></i> Teléfono: +57 321 4277692</li>
                            </a>
                            <li class="mb-2"><i class="fas fa-envelope me-2"></i> Email: <a href="mailto:hospitalserafinsanluis@yahoo.es" class="text-light me-2" style="text-decoration: none;">hospitalserafinsanluis@yahoo.es</a></li>
                            <li><i class="fas fa-calendar-alt me-2"></i> Horarios: Lunes a Viernes, 7:00 AM - 5:00 PM</li>
                        </ul>
                    </div>

                    <!-- Links útiles -->
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mb-lg-0">
                        <h5 class="mb-3">Enlaces Útiles</h5>
                        <ul class="list-unstyled">
                            <li><a href="#services" class="text-light text-decoration-none">Inicio</a></li>
                            <li><a href="#about" class="text-light text-decoration-none" id="about-link">Acerca de Nosotros</a></li>
                            <li><a href="Vista/html/interfazdeusuario/FormularioCita.php" class="text-light text-decoration-none">Pedir Cita</a></li>
                            <li><a href="Vista/html/paneles/index.php" class="text-light text-decoration-none">Ingresar</a></li>
                        </ul>
                    </div>


                    <!-- Redes Sociales -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <h5 class="mb-3">Redes Sociales</h5>
                        <div class="d-flex">
                            <a href="https://www.facebook.com/profile.php?id=100066966576964&mibextid=ZbWKwL" class="text-light me-3 social-icon">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                            <a href="https://wa.me/573214277692" class="text-light me-3 social-icon">
                                <i class="fab fa-whatsapp fa-2x"></i>
                            </a>
                        </div>
                        <br>
                        <h5 class="mb-3">Manuales de usuario</h5>
                        <div class="d-flex">
                            <div id="contenedorQR" class="contenedorQR">

                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <hr>
            <div class="text-center mt-4">
                <p class="mb-0">&copy; 2024 Hospital Serafin Montaña Cuellar. Todos los derechos reservados.</p>
            </div>
            </div>
        </footer>


        <!-- Chatbot -->
        <button id="chatButton" class="floating-button">💬</button>
        <div class="menu-buttons">
            <button class="menu-button" onclick="openChat()"><i class="fa-solid fa-desktop"></i> Chatbot</button>
            <button class="menu-button" onclick="openWhatsApp()"><i class="fa-brands fa-whatsapp"></i> WhatsApp</button>
        </div>
        <div class="container2" id="chatContainer">
            <div class="chat-header">
                <h2>InnovaSalud H.S.M.C</h2>
                <button class="close-button" onclick="closeChat()">&times;</button>
            </div>
            <div id="chatbox" class="chatbox">
                <div class="message bot-message">¡Hola! Soy tu asistente virtual y estoy aquí para ayudarte con la creación, cancelación y consulta de citas. Puedes escribir en el campo de texto y utilizar palabras clave como "cita", "consultar", "cancelar", "ubicación", "teléfono" y "correo" para encontrar la información que necesitas. Estoy aquí para asistirte en todo lo que necesites. <span class="time">${timeString}</span></div>
            </div>
            <div class="input-container">
                <input type="text" id="userInput" placeholder="Escribe tu mensaje aquí...">
                <button id="sendButton">Enviar</button>
            </div>
        </div>




        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="Vista/js/scriptL.js"></script>
        <script defer src="Vista/js/codigo_qr.js"></script>
        <script>
    window.onload = function() {
        setTimeout(function() {
            document.querySelector('.container_loader').classList.add('hidden');
        }, 2000); 
    };
</script>





    </body>

    </html>