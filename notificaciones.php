<?php
require_once 'conexion.php';

// Obtener préstamos que vencen en 24 horas
$hoy = date('Y-m-d H:i:s');
$manana = date('Y-m-d H:i:s', strtotime('+1 day'));

$prestamos_por_vencer = $conn->query("SELECT p.*, a.nombre as activo_nombre, u.nombre_usuario, u.email 
                                     FROM prestamos p
                                     JOIN activos a ON p.id_activo = a.id
                                     JOIN usuarios u ON p.id_usuario = u.id
                                     WHERE p.estado = 'activo' 
                                     AND p.fecha_devolucion_esperada BETWEEN '$hoy' AND '$manana'");

// Obtener préstamos atrasados (más de 2 días)
$dos_dias_atras = date('Y-m-d H:i:s', strtotime('-2 days'));
$prestamos_atrasados = $conn->query("SELECT p.*, a.nombre as activo_nombre, u.nombre_usuario, u.email 
                                   FROM prestamos p
                                   JOIN activos a ON p.id_activo = a.id
                                   JOIN usuarios u ON p.id_usuario = u.id
                                   WHERE p.estado = 'activo' 
                                   AND p.fecha_devolucion_esperada < '$dos_dias_atras'");

// Enviar notificaciones (simulado)
function enviarNotificacion($email, $mensaje) {
    // En producción usarías mail() o una librería de correo
    error_log("Notificación a $email: $mensaje");
    return true;
}

// Notificar préstamos por vencer
while ($prestamo = $prestamos_por_vencer->fetch_assoc()) {
    $mensaje = "Recordatorio: El préstamo de '{$prestamo['activo_nombre']}' vence mañana.";
    enviarNotificacion($prestamo['email'], $mensaje);
}

// Notificar préstamos atrasados
while ($prestamo = $prestamos_atrasados->fetch_assoc()) {
    $mensaje = "Alerta: El préstamo de '{$prestamo['activo_nombre']}' está atrasado por más de 2 días.";
    enviarNotificacion($prestamo['email'], $mensaje);
    
    // Actualizar estado a atrasado
    $conn->query("UPDATE prestamos SET estado = 'atrasado' WHERE id = {$prestamo['id']}");
}

$conn->close();
?>