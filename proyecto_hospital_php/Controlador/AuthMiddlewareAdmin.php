<?php
class AuthMiddlewareAdmin {
    public static function checkAuth() {
        session_start();
        var_dump($_SESSION); // Verificar el contenido de la sesión
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/paneles/Login_admin.php");
            exit();
        }

        // Verificar el tiempo de inactividad
        $tiempo_inactividad_maximo = 900; // 15 minutos (900 segundos)
        if (isset($_SESSION['ultimo_movimiento'])) {
            $tiempo_transcurrido = time() - $_SESSION['ultimo_movimiento'];
            if ($tiempo_transcurrido > $tiempo_inactividad_maximo) {
                self::logout();
                exit();
            }
        }

        // Actualizar el tiempo del último movimiento
        $_SESSION['ultimo_movimiento'] = time();
    }

    public static function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /Proyecto_Hospital_1/proyecto_hospital_php/Vista/html/login.php");
        exit();
    }
}


?>
