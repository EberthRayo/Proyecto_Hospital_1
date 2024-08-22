const contenedorQR = document.getElementById('contenedorQR');

        new QRCode(contenedorQR, {
            text: 'https://drive.google.com/drive/folders/1X3UiEBNX4YDaVSuUftX5biC3wURNZdHp',
            width: 100, // Ancho del código QR en píxeles
            height: 100, // Alto del código QR en píxeles
            colorDark : "#000000", // Color del código QR
            colorLight : "#ffffff", // Color del fondo del código QR
            correctLevel : QRCode.CorrectLevel.H // Nivel de corrección de errores
        });