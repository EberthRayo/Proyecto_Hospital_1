<?php
// validate_eps_api.php

// Configuración de la API
$apiUrl = 'https://api.tu-empresa.com/validar-eps';
$apiKey = 'tu_api_key_aqui'; // Si la API requiere autenticación

// Verifica si se ha enviado un número de documento
if (isset($_POST['documento'])) {
    $documento = trim($_POST['documento']);

    // Realiza la solicitud a la API
    $ch = curl_init($apiUrl);
    
    // Configura las opciones de CURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey // Si es necesario
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['documento' => $documento]));

    // Ejecuta la solicitud y obtiene la respuesta
    $response = curl_exec($ch);
    
    // Maneja errores de CURL
    if (curl_errno($ch)) {
        $message = 'Error de CURL: ' . curl_error($ch);
    } else {
        // Cierra CURL
        curl_close($ch);

        // Decodifica la respuesta JSON
        $data = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if ($data['valid']) {
                $message = 'El documento ha sido validado con éxito.';
            } else {
                $message = 'El número de documento no está registrado con una EPS válida.';
            }
        } else {
            $message = 'Error al procesar la respuesta de la API.';
        }
    }
} else {
    $message = 'Número de documento no proporcionado.';
}

// Redirige al formulario con el mensaje de resultado
header('Location: index.php?message=' . urlencode($message));
exit();
?>
