// Obtener los elementos necesarios
const chatButton = document.getElementById('chatButton');
const menuButtons = document.querySelector('.menu-buttons');
const chatContainer = document.getElementById('chatContainer');

// Evento para mostrar/ocultar los botones del menú
chatButton.addEventListener('click', () => {
    menuButtons.style.display = menuButtons.style.display === 'none' ? 'flex' : 'none';
});

// Función para abrir el chat
function openChat() {
    menuButtons.style.display = 'none';
    chatContainer.style.display = 'block';

    const message = 'Hola, soy Serafín, tu asistente virtual. ¿En qué puedo ayudarte?';
    showTemporaryMessage(message, 5000);
    appendMessage(message, 'bot-message');
}

// Función para abrir WhatsApp
function openWhatsApp() {
    menuButtons.style.display = 'none';
    const phoneNumber = '573214277692'; // Reemplaza con el número de teléfono de WhatsApp
    const message = 'Hola, necesito ayuda con...'; // Mensaje predefinido
    const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

// Función para enviar un mensaje
function sendMessage() {
    const userInput = document.getElementById('userInput').value;
    if (userInput.trim() === '') return;

    appendMessage(userInput, 'user-message');
    document.getElementById('userInput').value = '';

    // Respuesta del chatbot
    let botResponse = getBotResponse(userInput);
    appendMessage(botResponse, 'bot-message');
}
// Función para cerrar el chat
function closeChat() {
    chatContainer.style.display = 'none';
    menuButtons.style.display = 'block'; // Mostrar nuevamente el menú principal
    clearChatbox(); // Opcional: Limpiar el historial de mensajes al cerrar el chat
}

function appendMessage(message, className) {
    const messageElement = document.createElement('div');
    messageElement.className = `message ${className}`;
    const timeString = getTimeString();

    // Si es un mensaje del bot, añade un icono
    if (className === 'bot-message') {
        const icon = '<i class="fas fa-robot"></i> <br>'; // Cambia 'fa-robot' por el icono que prefieras
        messageElement.innerHTML = `${icon}${message} <span class="time">${timeString}</span>`;
    } else {
        messageElement.innerHTML = `${message} <br><span class="time">${timeString}</span>`;
    }

    document.getElementById('chatbox').appendChild(messageElement);
    document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
}

function getCurrentTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}

// Obtener la hora actual en formato colombiano
const timeString = new Date().toLocaleTimeString('es-CO', { hour: 'numeric', minute: 'numeric', hour12: true });

// Agregar el mensaje del bot con la hora actual
document.getElementById('chatbox').innerHTML = `
    <div class="message bot-message">
        ¡Hola! Soy tu asistente virtual y estoy aquí para ayudarte con la creación, cancelación y consulta de citas. 
        Puedes escribir en el campo de texto y utilizar palabras clave como "cita", "consultar", "cancelar", "ubicación", 
        "teléfono" y "correo" para encontrar la información que necesitas. Estoy aquí para asistirte en todo lo que necesites. 
        <span class="time">${timeString}</span>
    </div>`;

// Función para obtener la hora en el formato correcto
function getTimeString() {
    const now = new Date();
    return now.toLocaleTimeString('es-CO', { hour: 'numeric', minute: 'numeric', hour12: true });
}

// Función para obtener la respuesta del chatbot
function getBotResponse(input) {
    const normalizedInput = normalizeInput(input);

    const responses = [{
            keywords: ['agendar', 'programar', 'reservar', 'hacer', 'pedir', 'solicitar'],
            questions: [
                'necesito agendar una cita',
                'necesito agendar una cita medica',
                'necesito hacer una cita medica',
                '¿Cómo puedo agendar una cita?',
                '¿Cómo programo una cita?',
                '¿Cómo reservo una cita?',
                '¿Cómo hago una cita?',
                '¿Cómo puedo pedir una cita?',
                '¿Cómo solicito una cita?',
                '¿Quiero agendar una cita?',
                '¿Necesito agendar una cita?',
                '¿Me gustaría agendar una cita?',
                '¿Quisiera agendar una cita?',
                '¿Quiero hacer una cita?',
                '¿Cómo agendo una cita?',
                '¿Cómo planifico una cita?',
                '¿Cómo gestiono una cita?',
                '¿Cómo concierto una cita?',
                '¿Cómo pido una cita previa?',
                '¿Cómo fijo una cita con el doctor?',
                '¿Cómo organizo una cita?',
                '¿Cómo obtengo una cita?',
                '¿Cómo aseguro una cita?',
                '¿Cómo reservo mi cita?',
                '¿Cómo organizo un encuentro?',
                '¿Cómo establezco una cita?',
                '¿Cómo agendo un encuentro?',
                '¿Cómo fijo una cita?',
                '¿Cómo organizo una reunión?',
                '¿Cómo coordinar una cita?',
                '¿Cómo gestiono una cita?',
                '¿Cómo concierto una cita con el médico?',
                '¿Cómo planear una cita?',
                '¿Cómo reservo un encuentro?',
                'quiero crear una cita medica',
                'quiero saber como puedo sacar una cita',
            ],
            topic: 'cita',
            response: 'Para agendar una cita, por favor visita el siguiente enlace <a href="Vista/html/interfazdeusuario/FormularioCita.php" target="_blank" style="color:black;"class="blink">este enlace</a>.'
        },
        {
            keywords: ['consultar', 'revisar', 'ver', 'confirmar', 'detalles'],
            questions: [
                'cómo puedo consultar una cita',
                'cómo reviso una cita',
                'cómo veo una cita',
                'cómo confirmo una cita',
                'cuáles son los detalles de mi cita',
                'quiero consultar una cita',
                'necesito consultar una cita',
                'me gustaría consultar una cita',
                'quisiera consultar una cita'
            ],
            topic: 'cita',
            response: 'Para consultar los detalles de tu cita, visita <a href="Vista/html/interfazdeusuario/FormularioCita.php#consultar" target="_blank" style="color:black;" class="blink">este enlace</a>.'
        },
        {
            keywords: ['cancelar', 'anular', 'eliminar', 'terminar'],
            questions: [
                'cómo puedo cancelar una cita',
                'cómo anulo una cita',
                'cómo elimino una cita',
                'cómo termino una cita',
                'quiero cancelar una cita',
                'necesito cancelar una cita',
                'necesito cancelar una cita medica',
                'me gustaría cancelar una cita',
                'quisiera cancelar una cita'
            ],
            topic: 'cita',
            response: 'Para cancelar una cita, visita <a href="Vista/html/interfazdeusuario/FormularioCita.php#cancelar" target="_blank" style="color:black;" class="blink">este enlace</a>.'
        },
        {
            keywords: ['ubicacion', 'donde', 'direccion', 'llegar'],
            questions: [
                'dónde está el hospital',
                'cuál es la ubicación del hospital',
                'cuál es la dirección del hospital',
                'cómo llego al hospital',
                'quiero saber la ubicación del hospital',
                'necesito saber la ubicación del hospital',
                'me gustaría saber la ubicación del hospital',
                'quisiera saber la ubicación del hospital',
                'quiero conocer la ubicacion',
                'quisiera saber la ubicación del hospital'
            ],
            topic: 'hospital',
            response: 'Para conocer la ubicación del hospital, visita <a href="Vista/html/interfazdeusuario/Google_maps.php" target="_blank" style="color:black;" class="blink">este enlace contiene la ubicación</a>.'
        },
        {
            keywords: ['correo', 'email', 'contacto', 'direccion'],
            questions: [
                'cuál es el correo del hospital',
                'cuál es el email del hospital',
                'cómo contacto por correo',
                'cuál es la dirección de correo',
                'quiero saber el correo del hospital',
                'necesito saber el correo del hospital',
                'me gustaría saber el correo del hospital',
                'quisiera saber el correo del hospital'
            ],
            topic: 'hospital',
            response: 'El correo Electronico del Hospital es: hospitalserafinsanluis@yahoo.es.'
        },
        {
            keywords: ['telefono', 'numero', 'contacto'],
            questions: [
                'cuál es el teléfono del hospital',
                'cuál es el número del hospital',
                'cómo contacto por teléfono',
                'cuál es el número de contacto',
                'quiero saber el teléfono del hospital',
                'necesito saber el teléfono del hospital',
                'me gustaría saber el teléfono del hospital',
                'quiero saber cual es el numero de telefono',
                'quisiera saber el teléfono del hospital'
            ],
            topic: 'hospital',
            response: 'El número de contacto del Hospital es: <br> <a href="tel:+573214277692" class="text-white"> +57 321 4277692</a> .'
        }
    ];
    

    // Buscar respuesta basada en palabras clave y tema
    for (let i = 0; i < responses.length; i++) {
        const topicMatches = normalizedInput.includes(responses[i].topic);
        const keywordMatches = responses[i].keywords.some(keyword => normalizedInput.includes(normalizeInput(keyword)));
        const questionMatches = responses[i].questions.some(question => normalizeInput(question).includes(normalizedInput));

        if ((topicMatches || keywordMatches) && questionMatches) {
            return responses[i].response;
        }
    }

    // Respuestas genéricas para saludos
    if (isSimilar(normalizedInput, 'hola')) {
        return '¡Hola! ¿Cómo estás?';
    } else if (isSimilar(normalizedInput, 'Buen día, ¿cómo va todo?')) {
        return '¡Hola! Todo va bien, ¿y contigo?!';
    } else if (isSimilar(normalizedInput, 'Bien ')) {
        return '¡Qué bueno saber que estás bien! ¿En qué puedo asistirte hoy?"';
    } else if (isSimilar(normalizedInput, 'adios')) {
        return '¡Adiós! ¡Que tengas un buen día!';
    } else if (isSimilar(normalizedInput, 'chao')) {
        return 'Hasta pronto, espero te vaya bien ';
    } else {
        return 'Lo siento, no entiendo tu pregunta. Intenta preguntarme sobre citas, ubicación, contacto, etc.';
    }
}

function normalizeInput(input) {
    // Mapa para reemplazar letras acentuadas con sus equivalentes sin acentos
    const accentsMap = new Map([
        ['á', 'a'],
        ['é', 'e'],
        ['í', 'i'],
        ['ó', 'o'],
        ['ú', 'u'],
        ['ü', 'u'],
        ['Á', 'a'],
        ['É', 'e'],
        ['Í', 'i'],
        ['Ó', 'o'],
        ['Ú', 'u'],
        ['Ü', 'u'],
        ['ñ', 'n'],
        ['Ñ', 'n']
    ]);

    return input.toLowerCase()
        .split('')
        .map(char => accentsMap.get(char) || char) // Reemplaza letras acentuadas
        .join('')
        .replace(/[^a-zñáéíóúü\s]/gi, '') // Elimina caracteres especiales no deseados
        .trim();
}

// Función para verificar similitud
function isSimilar(input, phrase) {
    const similarityThreshold = 0.6;
    return getSimilarity(input, phrase) > similarityThreshold;
}

// Función para obtener la similitud entre dos cadenas
function getSimilarity(s1, s2) {
    const longer = s1.length > s2.length ? s1 : s2;
    const shorter = s1.length > s2.length ? s2 : s1;
    const longerLength = longer.length;
    if (longerLength === 0) {
        return 1.0;
    }
    return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
}

// Función para calcular la distancia de edición
function editDistance(s1, s2) {
    s1 = normalizeInput(s1);
    s2 = normalizeInput(s2);

    const costs = [];
    for (let i = 0; i <= s1.length; i++) {
        let lastValue = i;
        for (let j = 0; j <= s2.length; j++) {
            if (i === 0)
                costs[j] = j;
            else {
                if (j > 0) {
                    let newValue = costs[j - 1];
                    if (s1.charAt(i - 1) !== s2.charAt(j - 1))
                        newValue = Math.min(Math.min(newValue, lastValue), costs[j]) + 1;
                    costs[j - 1] = lastValue;
                    lastValue = newValue;
                }
            }
        }
        if (i > 0)
            costs[s2.length] = lastValue;
    }
    return costs[s2.length];
}

// Event listener para el botón de enviar mensaje
document.getElementById('sendButton').addEventListener('click', sendMessage);
document.getElementById('userInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
