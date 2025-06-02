<?php
session_start();
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id_prestamo = $_GET['id'];
    // Evitar error si no existe la sesión
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $fecha_devolucion = date('Y-m-d H:i:s');
    
    // Obtener ID del libro para actualizar su estado
    $prestamo = $conn->query("SELECT id_activo FROM prestamos WHERE id = $id_prestamo")->fetch_assoc();
    $id_libro = $prestamo['id_activo'];
    
    // Actualizar préstamo (elimina devuelto_por porque no existe esa columna)
    $stmt = $conn->prepare("UPDATE prestamos 
                           SET fecha_devolucion_real = ?, estado = 'devuelto'
                           WHERE id = ?");
    $stmt->bind_param("si", $fecha_devolucion, $id_prestamo);
    
    if ($stmt->execute()) {
        // Actualizar estado del libro a "Disponible" (estado 1)
        $conn->query("UPDATE activos SET id_estado = 1 WHERE id = $id_libro");
        
        header("Location: prestamos.php?success=2");
    } else {
        header("Location: prestamos.php?error=2");
    }
    exit();
}
?>