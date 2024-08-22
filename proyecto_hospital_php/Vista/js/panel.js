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
    function updateTime() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        // Convertir a formato de 12 horas
        hours = hours % 12;
        hours = hours ? hours : 12; // La hora '0' debe ser '12'
        hours = String(hours).padStart(2, '0');

        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
    }

    // Actualiza la hora inmediatamente y luego cada segundo
    updateTime();
    setInterval(updateTime, 1000);