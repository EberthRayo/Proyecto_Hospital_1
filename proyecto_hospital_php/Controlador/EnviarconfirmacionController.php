<?php
include __DIR__ . '/../Modelo/Cita.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CitasConfirmacionController {

    public function validarYEnviarCorreo($idCita) {
        $citaModel = new CitaConfirmacion();
        $data = $citaModel->obtenerDatosCita($idCita);

        if ($data) {
            // Realiza la validación aquí
            $estadoCita = $data[0]['Estado_Cita'];

            if ($estadoCita == 'Confirmada' || $estadoCita == 'Cancelada') {
                // Generar el PDF con los detalles de la cita
                $pdf = new PDF();
                $pdf->AddPage();
                $pdf->AliasNbPages();
                $pdf->ReportData($data);

                $pdfFilePath = '../temp/Reporte_Citas_Medicas.pdf';  
                $pdf->Output($pdfFilePath, 'F');

                // Enviar el PDF por correo
                $this->enviarCorreo($data[0]['email'], $pdfFilePath);

                echo "<script>alert('El correo ha sido enviado exitosamente');</script>";
            } else {
                echo "<script>alert('La cita no cumple con los requisitos para enviar el correo.');</script>";
            }
        } else {
            echo "<script>alert('No se encontraron registros para la cita proporcionada.');</script>";
        }
    }

    private function enviarCorreo($destinatario, $pdfFilePath) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Cambia a tu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'danicrg05@gmail.com';  // Cambia a tu correo
            $mail->Password = 'oqbi utlj zkjp xgsg';  // Cambia a tu contraseña
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('hospital@innovasalud.com', 'InnovaSalud');
            $mail->addAddress($destinatario);

            // Adjuntar PDF generado
            $mail->addAttachment($pdfFilePath, 'Reporte_Citas_Medicas.pdf');

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Estado de tu Cita Médica';
            $mail->Body = 'Adjunto encontrarás el estado de tu cita médica.';
            $mail->AltBody = 'Adjunto encontrarás el estado de tu cita médica en formato PDF.';

            // Enviar correo
            $mail->send();
        } catch (Exception $e) {
            echo "El correo no pudo ser enviado. Error: {$mail->ErrorInfo}";
        }
    }
}
