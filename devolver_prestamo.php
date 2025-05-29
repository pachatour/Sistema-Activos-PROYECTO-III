<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id_prestamo = $_GET['id'];
    $fecha_devolucion = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("UPDATE prestamos 
                           SET fecha_devolucion_real = ?, estado = 'devuelto' 
                           WHERE id = ?");
    $stmt->bind_param("si", $fecha_devolucion, $id_prestamo);
    
    if ($stmt->execute()) {
        header("Location: prestamos.php?success=2");
    } else {
        header("Location: prestamos.php?error=2");
    }
    exit();
}
?>