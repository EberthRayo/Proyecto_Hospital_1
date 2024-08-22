<?php
// Controlador/logout.php

session_start(); // Inicia la sesión

// Destruir todas las variables de sesión.
$_SESSION = array();

// Si se desea destruir la sesión completamente, también se debe borrar la cookie de sesión.
// Nota: Esto destruirá la sesión y no solo los datos de la sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Regenerar el ID de sesión para eliminar la sesión actual.
session_regenerate_id(true);

// Finalmente, destruir la sesión.
session_destroy();

// Asegurarse de que no hay salida antes de la redirección.
ob_start();

// Redirigir al usuario a la página de inicio de sesión.
// Usar una ruta absoluta desde la raíz del servidor web.
header("Location: /Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/Login_admin.php");
ob_end_flush();
exit();
?>
