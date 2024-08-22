<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <!-- Incluir el CSS de FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
</head>
<body>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="calendar"><i class="fa-solid fa-calendar-day" style="color: #74C0FC;"></i> Fechas y horas disponibles</label>
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Incluir el JS de FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('../../../../Modelo/fetch_events.php') // Cambia esta URL por la ruta correcta hacia tu archivo PHP
                        .then(response => response.json())
                        .then(data => {
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Error fetching events:', error);
                            failureCallback(error);
                        });
                },
                dateClick: function(info) {
                    // Manejar el clic en una fecha
                    console.log('Clicked on: ' + info.dateStr);
                },
                eventClick: function(info) {
                    // Manejar el clic en un evento
                    console.log('Event: ' + info.event.title);
                    console.log('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
                    console.log('View: ' + info.view.type);
                }
            });

            calendar.render(); // Renderizar el calendario
        });
    </script>
</body>
</html>
