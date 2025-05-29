<?php
require_once 'conexion.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prestamo = $_POST['id_prestamo'] ?? 0;
    
    // Obtener información del préstamo
    $prestamo = $conn->query("SELECT p.*, a.nombre as libro, 
                             CONCAT(e.nombre, ' ', e.apellido) as estudiante,
                             e.email
                             FROM prestamos p
                             JOIN activos a ON p.id_activo = a.id
                             JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
                             WHERE p.id = $id_prestamo")->fetch_assoc();
    
    if ($prestamo) {
        $asunto = "Recordatorio de préstamo: " . $prestamo['libro'];
        $mensaje = "Estimado {$prestamo['estudiante']},\n\n";
        $mensaje .= "Este es un recordatorio sobre el préstamo del libro:\n";
        $mensaje .= "Libro: {$prestamo['libro']}\n";
        $mensaje .= "Fecha de préstamo: " . date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) . "\n";
        $mensaje .= "Fecha de devolución: " . date('d/m/Y', strtotime($prestamo['fecha_devolucion_esperada'])) . "\n\n";
        $mensaje .= "Por favor, no olvide devolver el libro a tiempo.\n\n";
        $mensaje .= "Atentamente,\nBiblioteca";
        
        // En producción, enviaríamos el correo aquí
        // mail($prestamo['email'], $asunto, $mensaje);
        
        // Por ahora solo simulamos el envío
        error_log("Notificación enviada a {$prestamo['email']}: $asunto");
        
        $response['success'] = true;
        $response['message'] = 'Notificación preparada para enviar';
    } else {
        $response['message'] = 'Préstamo no encontrado';
    }
}

echo json_encode($response);
$conn->close();
?>