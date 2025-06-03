<?php
require_once 'conexion.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prestamo = $_POST['id_prestamo'] ?? 0;

    // Obtener información del préstamo y usuario
    $prestamo = $conn->query("
        SELECT p.*, a.nombre as libro, 
               CONCAT(ub.nombre, ' ', ub.apellido) as usuario,
               ub.telefono, ub.email
        FROM prestamos p
        JOIN activos a ON p.id_activo = a.id
        JOIN usuarios_biblioteca ub ON p.id_usuario_biblioteca = ub.id
        WHERE p.id = $id_prestamo
    ")->fetch_assoc();

    if ($prestamo) {
        $mensaje = "Estimado {$prestamo['usuario']},\n"
            . "Este es un recordatorio sobre el préstamo del libro:\n"
            . "Libro: {$prestamo['libro']}\n"
            . "Fecha de préstamo: " . date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) . "\n"
            . "Fecha de devolución: " . date('d/m/Y', strtotime($prestamo['fecha_devolucion_esperada'])) . "\n\n"
            . "Por favor, no olvide devolver el libro a tiempo.\n"
            . "Atentamente,\nBiblioteca";

        // Enviar correo si hay email
        if (!empty($prestamo['email'])) {
            $para = $prestamo['email'];
            $asunto = "📚 Recordatorio de préstamo de libro";
            $cabeceras = "From: ola.ari1902@gmail.com\r\n";
            $cabeceras .= "Reply-To: ola.ari1902@gmail.com\r\n";
            $cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($para, $asunto, $mensaje, $cabeceras)) {
                $response['correo'] = 'Correo enviado correctamente.';
            } else {
                $response['correo'] = 'No se pudo enviar el correo.';
            }
        } else {
            $response['correo'] = 'El usuario no tiene correo registrado.';
        }
    } else {
        $response['message'] = 'No se encontró el préstamo o el usuario.';
    }
}

echo json_encode($response);
$conn->close();
?>
