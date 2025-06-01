<?php
require_once 'conexion.php';

// Obtener datos del formulario
$id_activo = $_POST['id_activo'];
$id_usuario_biblioteca = $_POST['id_usuario_biblioteca'];
$tipo_usuario = $_POST['tipo_usuario']; // Ahora viene del campo oculto

// Validar datos
if (empty($id_activo) || empty($id_usuario_biblioteca) || empty($tipo_usuario)) {
    die("Error: Faltan datos requeridos");
}

// Obtener configuración de préstamos para este tipo de usuario
$config = $conn->query("SELECT * FROM configuracion_prestamos WHERE tipo_usuario = '$tipo_usuario'")->fetch_assoc();

if (!$config) {
    die("Error: Configuración no encontrada para este tipo de usuario");
}

// Calcular fechas
$fecha_prestamo = date('Y-m-d H:i:s');
$fecha_devolucion = date('Y-m-d H:i:s', strtotime("+{$config['dias_prestamo']} days"));

// Registrar el préstamo
$query = "INSERT INTO prestamos (
            id_activo, 
            id_usuario_biblioteca, 
            tipo_usuario, 
            fecha_prestamo, 
            fecha_devolucion_esperada, 
            estado
          ) VALUES (?, ?, ?, ?, ?, 'prestado')";

$stmt = $conn->prepare($query);
$stmt->bind_param("iisss", $id_activo, $id_usuario_biblioteca, $tipo_usuario, $fecha_prestamo, $fecha_devolucion);

if ($stmt->execute()) {
    header("Location: prestamos.php?success=1");
} else {
    header("Location: prestamos.php?error=1");
}

$stmt->close();
$conn->close();
?>