<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'conexion.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_prestamo = $_POST['id_prestamo'] ?? 0;

        $prestamo = $conn->query("
            SELECT p.*, a.nombre as libro, 
                   CONCAT(ub.nombre, ' ', ub.apellido) as usuario,
                   ub.telefono, ub.email
            FROM prestamos p
            JOIN activos a ON p.id_activo = a.id
            JOIN usuarios_biblioteca ub ON p.id_usuario_biblioteca = ub.id
            WHERE p.id = $id_prestamo
        ")->fetch_assoc();

        if ($prestamo && !empty($prestamo['email'])) {
            $mensaje = "Estimado {$prestamo['usuario']},\n"
                . "Este es un recordatorio sobre el préstamo del libro:\n"
                . "Libro: {$prestamo['libro']}\n"
                . "Fecha de préstamo: " . date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) . "\n"
                . "Fecha de devolución: " . date('d/m/Y', strtotime($prestamo['fecha_devolucion_esperada'])) . "\n\n"
                . "Por favor, no olvide devolver el libro a tiempo.\n"
                . "Atentamente,\nBiblioteca";

            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ara.odz19@gmail.com'; // Tu correo Gmail
                $mail->Password = 'cntixcmnrrkvigbm'; // Usa una clave de aplicación, no tu contraseña normal
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Configuración del email
                $mail->setFrom('ara.odz19@gmail.com', 'Biblioteca'); // Usa el mismo correo que el Username
                $mail->addAddress($prestamo['email'], $prestamo['usuario']);
                $mail->Subject = '📚 Recordatorio de préstamo de libro';
                $mail->Body = $mensaje;

                $mail->send();
                $response['success'] = true;
                $response['message'] = 'Correo enviado correctamente.';
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error al enviar correo: ' . $mail->ErrorInfo;
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'No se encontró el préstamo o el usuario no tiene correo registrado.';
        }
    }
} catch (Throwable $ex) {
    $response['success'] = false;
    $response['message'] = 'Error interno del servidor: ' . $ex->getMessage();
}

// Asegúrate de que no haya espacios ni líneas en blanco después de este cierre
echo json_encode($response);
$conn->close();
?>