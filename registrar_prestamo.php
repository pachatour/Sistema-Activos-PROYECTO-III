<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_activo = $_POST['activo']; // En realidad deberías buscar el ID del activo
    $id_usuario = $_POST['usuario'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    // Calcular fecha de devolución
    $dias_prestamo = ($tipo_usuario === 'estudiante') ? 7 : 15;
    $fecha_devolucion = date('Y-m-d H:i:s', strtotime("+$dias_prestamo days"));
    
    // Insertar préstamo
    $stmt = $conn->prepare("INSERT INTO prestamos (id_activo, id_usuario, tipo_usuario, fecha_devolucion_esperada) 
                           VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_activo, $id_usuario, $tipo_usuario, $fecha_devolucion);
    
    if ($stmt->execute()) {
        header("Location: prestamos.php?success=1");
    } else {
        header("Location: prestamos.php?error=1");
    }
    exit();
}
?>