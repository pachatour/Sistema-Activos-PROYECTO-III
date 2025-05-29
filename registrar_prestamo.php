<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_libro = $_POST['libro'];
    $prestador_id = $_POST['prestador_id'];
    $id_estudiante = $_POST['estudiante'];
    $dias_prestamo = $_POST['dias_prestamo'];
    
    // Calcular fecha de devolución
    $fecha_devolucion = date('Y-m-d H:i:s', strtotime("+$dias_prestamo days"));
    
    // Insertar préstamo
    $stmt = $conn->prepare("INSERT INTO prestamos 
                          (id_activo, id_prestador, id_estudiante, fecha_devolucion_esperada, dias_prestamo, estado) 
                          VALUES (?, ?, ?, ?, ?, 'prestado')");
    $stmt->bind_param("iiisi", $id_libro, $prestador_id, $id_estudiante, $fecha_devolucion, $dias_prestamo);
    
    if ($stmt->execute()) {
        // Actualizar estado del libro a "Prestado" (estado 6)
        $conn->query("UPDATE activos SET id_estado = 6 WHERE id = $id_libro");
        
        header("Location: prestamos.php?success=1");
    } else {
        header("Location: prestamos.php?error=1");
    }
    exit();
}
?>