
document.addEventListener("DOMContentLoaded", function() {
    // Selecciona la imagen del chat
    const chatboxOpen = document.querySelector(".chatbox-open");
    // Selecciona el chatbox
    const chatbox = document.querySelector(".chatbox");

    // Agrega un evento de clic a la imagen del chat
    chatboxOpen.addEventListener("click", function(e) {
        // Evita que el enlace se comporte como un enlace normal
        e.preventDefault();
        // Muestra el chatbox
        chatbox.style.display = "block";
    });

    // Agrega un evento de clic al botón de cerrar del chatbox
    const chatboxClose = document.querySelector(".chatbox-close");
    chatboxClose.addEventListener("click", function() {
        // Oculta el chatbox al hacer clic en el botón de cerrar
        chatbox.style.display = "none";
    });

    // Función para validar los campos del formulario
    function validarCampos() {
        var campos = document.querySelectorAll('input, select, textarea');
        var algunCampoVacio = false;
        campos.forEach(function(campo) {
            if (campo.value.trim() === '') {
                algunCampoVacio = true;
            }
        });
        if (algunCampoVacio) {
            alert('Algunos de los campos están vacíos, por favor llenarlos todos');
            return false;
        } else {
            return true;
        }
    }

    // Función para mostrar la sección de métodos de pago y ocultar las otras secciones
    function mostrarMetodosPago() {
        document.getElementById('metodosPago').style.display = 'block';
        document.getElementById('contacto').style.display = 'none';
        document.getElementById('servicios').style.display = 'none';
    }

    // Función para mostrar la sección de servicios y ocultar las otras secciones
    function mostrarServicios() {
        document.getElementById('metodosPago').style.display = 'none';
        document.getElementById('contacto').style.display = 'none';
        document.getElementById('servicios').style.display = 'block';
    }

    // Función para mostrar la sección de contacto y ocultar las otras secciones
    function mostrarContacto() {
        document.getElementById('metodosPago').style.display = 'none';
        document.getElementById('contacto').style.display = 'block';
        document.getElementById('servicios').style.display = 'none';
    }

    // Escuchar clics en el enlace de Inicio
    document.querySelector('a.nav-link[href="#metodospago"]').addEventListener('click', mostrarMetodosPago);

    // Escuchar clics en el enlace de Servicios
    document.querySelector('a.nav-link[href="#servicios"]').addEventListener('click', mostrarServicios);

    // Escuchar clics en el enlace de Contacto en la barra de navegación principal
    document.querySelector('a.nav-link[href="#contacto"]').addEventListener('click', mostrarContacto);

    // Al cargar la página, mostrar la sección de métodos de pago por defecto
    window.addEventListener('load', mostrarMetodosPago);
});
