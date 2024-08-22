document.addEventListener('DOMContentLoaded', () => {
    // Obtén el hash actual de la URL
    const currentHash = window.location.hash.substring(1);

    // Muestra la sección y subsección correspondientes según el hash
    if (currentHash) {
        showSection(currentHash);
    } else {
        // Si no hay hash, muestra una sección predeterminada
        showSection('appointment'); // Cambia esto a la sección predeterminada que quieras mostrar
    }

    // Muestra la subsección de epsValidationForm si está presente en el hash
    if (currentHash === 'appointment') {
        showSubsection('epsValidationForm');
    }

    // Inicializa la URL con el hash actual
    updateURL(currentHash);
});

$(document).ready(function() {
    $('#validateEPSForm').on('submit', function(event) {
        event.preventDefault(); // Evita el envío predeterminado del formulario

        const epsCode = $('#epsSelect').val(); // Obtén el código de EPS seleccionado
        $('#selectedEpsCode').val(epsCode); // Asigna el código al campo oculto

        const formData = $(this).serialize(); // Serializa los datos del formulario

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'), // Usa la URL del action del formulario
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        // Mostrar el formulario de creación de cita
                        showSubsection('appointmentForm'); // Ajusta esta función según tu estructura

                        // Autocompletar el formulario de cita con los datos del paciente
                        $('#appointmentDocumento').val(response.data.ID_Paciente);
                        $('#Nombre_completo').val(response.data.Nombres);
                        $('#appointmentTipoDocumento').val(response.data.Tipo_documento);
                        $('#FechaNacimiento').val(response.data.Fecha_Nacimiento);
                        $('#Genero').val(response.data.Genero);
                        $('#Direccion').val(response.data.Direccion_Residencia);
                        $('#Telefono').val(response.data.Numero_Telefono);
                        $('#Correo').val(response.data.Correo_Electronico);
                        $('#Edad').val(response.data.Edad);
                        
                        // Completar el campo de EPS usando el código del formulario de validación
                        $('#appointmentEps').val($('#epsSelect option:selected').text()); // Completa el campo de texto con el nombre de EPS
                        $('#selectedEpsCode').val(epsCode); // Completa el campo oculto con el código

                        // Mostrar el botón para crear una cita particular
                        $('#createParticularAppointment').show();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'Intentar de nuevo'
                    }).then(() => {
                        $('#createParticularAppointment').hide();
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });

    $('#epsSelect').on('change', function() {
        const documento = $('#appointmentDocumento').val();
        const epsCode = $(this).val();

        fetch('http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Modelo/simulacion_eps.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                documento: documento,
                eps: epsCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Autocompletar los datos del paciente si el formulario de cita no está visible
                if ($('#createParticularAppointment').is(':hidden')) {
                    const paciente = data.data;
                    $('#Nombre_completo').val(paciente.Nombres);
                    $('#FechaNacimiento').val(paciente.Fecha_Nacimiento);
                    $('#Genero').val(paciente.Genero);
                    $('#Direccion').val(paciente.Direccion_Residencia);
                    $('#Telefono').val(paciente.Numero_Telefono);
                    $('#Correo').val(paciente.Correo_Electronico);

                    // Completar el campo de EPS usando el código del formulario de validación
                    $('#appointmentEps').val($('#epsSelect option:selected').text()); // Completa el campo de texto con el nombre de EPS
                    $('#selectedEpsCode').val(epsCode); // Completa el campo oculto con el código
                }
            } 
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema con la validación.'
            });
        });
    });
});


$(document).ready(function() {
    $('#CrearcitaEps').on('submit', function(event) {
        event.preventDefault(); // Evita el envío predeterminado del formulario

        const formData = $(this).serialize(); // Serializa los datos del formulario

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log("Respuesta del servidor (JSON):", response);
                if (response && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        $('#CrearcitaEps')[0].reset();
                        window.location.href = 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/interfazdeusuario/FormularioCita.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'Intentar de nuevo'
                    }).then(() => {
                        $('#CrearcitaEps')[0].reset();
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("Detalles del error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud. Código de estado: ' + xhr.status + ', Mensaje: ' + error,
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });
});



$(document).ready(function() {
    // Manejo del formulario de búsqueda de citas
    $('#cancelarCitaForm').on('submit', function(event) {
        event.preventDefault(); // Evita el envío predeterminado del formulario

        const formData = $(this).serialize(); // Serializa los datos del formulario

        $.ajax({
            type: 'POST',
            url: 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/buscar_cancelar_cita.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                let html = '<h2>Resultados</h2>';

                // Instrucciones para el paciente
                html += `
                    <div class="alert alert-info">
                        <strong>Instrucciones:</strong><br>
                        En esta sección, puedes ver los detalles de tus citas actuales. Si deseas cancelar una cita, localiza la cita correspondiente en la lista y proporciona un motivo para la cancelación. Asegúrate de revisar la información cuidadosamente antes de proceder con la cancelación.<br>
                        Si la cita ya está cancelada, no podrás cancelar nuevamente.
                    </div>
                `;

                if (response.success) {
                    response.data.forEach(function(resultado) {
                        html += `<p><strong>ID Paciente:</strong> ${resultado.ID_Paciente}</p>`;
                        html += `<p><strong>Nombre: </strong>${resultado.Nombres}</p>`;
                        html += `<p><strong>Fecha y Hora: </strong>${resultado.Fecha_Hora}</p>`;
                        html += `<p><strong>Motivo: </strong>${resultado.Motivo}</p>`;
                        html += `<p><strong>Estado: </strong>${resultado.Estado_Cita}</p>`;
                        if (resultado.Estado_Cita !== 'Cancelada') {
                            html += `<form method="POST" class="cancelarCitaForm">`;
                            html += `<input type="hidden" name="id_cita" value="${resultado.ID_cita}">`;
                            html += `<input type="hidden" name="documento" value="${resultado.ID_Paciente}">`;
                            html += `<div class="mb-3">`;
                            html += `<label for="MotivoCancelacion" class="form-label"><strong>Motivo</strong></label>`;
                            html += `<textarea class="form-control" name="Motivocan" id="MotivoCancelacion" placeholder="Comenta el motivo por el cual deseas cancelar la cita" required></textarea>`;
                            html += `</div>`;
                            html += `<button type="button" class="btn btn-danger cancelarCitaButton" data-id="${resultado.ID_cita}">Cancelar Cita</button>`;
                            html += `</form>`;
                        }
                        html += `<hr>`;
                    });
                    $('#resultadosCitas').html(html);
                } else {
                    $('#resultadosCitas').html(`<p>${response.message}</p>`);
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });



    // Manejo del formulario de cancelación de cita
    $(document).on('click', '.cancelarCitaButton', function() {
        const form = $(this).closest('form');
        const formData = form.serialize(); // Serializa los datos del formulario de cancelación

        $.ajax({
            type: 'POST',
            url: 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/cancelarCita.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        // Redirigir después de mostrar el mensaje
                        window.location.href = 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/interfazdeusuario/FormularioCita.php#cancelar';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'Intentar de nuevo'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });
});

document.getElementById('buscarDocumento').addEventListener('input', function() {
    const documento = this.value;
    
    if (documento.length > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'http://localhost/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/buscar_cita.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (this.status === 200) {
                console.log('Respuesta del servidor:', this.responseText); // Verifica la respuesta
                try {
                    const response = JSON.parse(this.responseText);
                    
                    const resultado = document.getElementById('resultadoConsulta');
                    if (response.error) {
                        resultado.innerHTML = `<div class="alert alert-danger">${response.error}</div>`;
                    } else {
                        // Determinar el color de la alerta basado en el estado
                        let alertClass;
                        switch (response.estado) {
                            case 'Confirmada':
                                alertClass = 'alert-success'; // Verde
                                break;
                            case 'Validada':
                                alertClass = 'alert-warning'; // Amarillo
                                break;
                            case 'Cancelada':
                                alertClass = 'alert-danger'; // Rojo
                                break;
                            default:
                                alertClass = 'alert-info'; // Color predeterminado
                                break;
                        }

                        resultado.innerHTML = `
                            <div class="card card-animated">
                                <div class="card-body">
                                    <h5 class="card-title">Detalles de la Cita</h5>
                                    <p><strong>Numero de la cita :</strong> ${response.numero_cita}</p>
                                    <p><strong>Paciente:</strong> ${response.nombre_paciente}</p>
                                    <p><strong>Fecha y Hora de la cita:</strong> ${response.fecha_cita}</p>
                                    <p><strong>Especialidad:</strong> ${response.nombre_especialidad}</p>
                                    <p><strong>Identificación Doctor:</strong> ${response.id_medico}</p>
                                    <p><strong>Doctor:</strong> ${response.nombre_medico}</p>
                                    <p><strong>Consultorio:</strong> ${response.consultorio}</p>
                                    <p><strong>Motivo:</strong> ${response.motivo}</p>
                                    <div class="alert ${alertClass} mt-3" role="alert">
                                        <strong>Instrucciones:</strong> ${response.instrucciones}
                                    </div>
                                </div>
                            </div>
                        `;
                        // Añadir la clase para animar la tarjeta
                        setTimeout(() => {
                            document.querySelector('.card-animated').classList.add('show');
                        }, 100); // Pequeño retraso para que la animación sea visible
                    }
                } catch (e) {
                    console.error('Error al analizar JSON:', e);
                }
            }
        };        

        xhr.send(`documento=${encodeURIComponent(documento)}`);
    } else {
        document.getElementById('resultadoConsulta').innerHTML = '';
    }
});





// Función para mostrar subsecciones específicas
function showSubsection(subsectionId) {
    const subsections = document.querySelectorAll('.subsection');
    subsections.forEach(subsection => {
        if (subsection.id === subsectionId) {
            subsection.classList.add('active');
        } else {
            subsection.classList.remove('active');
        }
    });
}

// Función para mostrar una sección completa
function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
        if (section.id === sectionId) {
            section.classList.add('active');
            updateURL(sectionId); // Actualiza la URL con el ID de la sección activa
        } else {
            section.classList.remove('active');
        }
    });
}

// Actualiza la URL sin recargar la página
function updateURL(sectionId) {
    const url = new URL(window.location);
    url.hash = sectionId;
    history.pushState(null, '', url);
}

// Mostrar subsección de cita particular al hacer clic en el botón
document.getElementById('particularAppointmentBtn').addEventListener('click', function() {
    showSubsection('particularForm');
});

// Manejar el clic en los enlaces del sidebar para cambiar de sección
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        const sectionId = this.getAttribute('href').replace('#', '');
        showSection(sectionId);
    });
});
