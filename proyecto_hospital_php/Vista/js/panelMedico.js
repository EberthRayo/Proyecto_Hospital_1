document.getElementById('logoutButton').addEventListener('click', function (event) {
    event.preventDefault(); // Previene el envío del formulario

    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres cerrar sesión?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit(); // Envía el formulario si el usuario confirma
        }
    });
});
function actualizarNotificaciones() {
    fetch('http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/notificaciones.php') // Ajusta la ruta según la ubicación real del archivo
        .then(response => response.json())
        .then(data => {
            const notificaciones = data.notificaciones;
            const campanita = document.querySelector('#campanita');
            campanita.textContent = notificaciones > 0 ? `(${notificaciones})` : '';
        })
        .catch(error => console.error('Error al obtener notificaciones:', error));
}

// Verifica las notificaciones cada 10 segundos (ajusta según tus necesidades)
setInterval(actualizarNotificaciones, 10000);
actualizarNotificaciones(); // Actualiza inmediatamente al cargar la página
