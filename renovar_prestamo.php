<?php
include 'conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de préstamo no válido.";
    exit;
}

$id_prestamo = intval($_GET['id']);

// Obtener datos del préstamo
$stmt = $conn->prepare("SELECT tipo_usuario, fecha_devolucion_esperada, renovaciones FROM prestamos WHERE id = ?");
$stmt->bind_param("i", $id_prestamo);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $tipo_usuario = $row['tipo_usuario'];
    $fecha_devolucion_esperada = $row['fecha_devolucion_esperada'];
    $renovaciones = $row['renovaciones'];

    // Determinar días a agregar según tipo de usuario
    $dias = ($tipo_usuario === 'docente') ? 15 : 7;

    // Calcular nueva fecha de devolución
    $nueva_fecha = date('Y-m-d H:i:s', strtotime($fecha_devolucion_esperada . " +$dias days"));

    // Actualizar préstamo
    $stmt2 = $conn->prepare("UPDATE prestamos SET fecha_devolucion_esperada = ?, renovaciones = renovaciones + 1 WHERE id = ?");
    $stmt2->bind_param("si", $nueva_fecha, $id_prestamo);
    if ($stmt2->execute()) {
        echo "Préstamo renovado correctamente. Nueva fecha de devolución: " . $nueva_fecha;
    } else {
        echo "Error al renovar el préstamo.";
    }
    $stmt2->close();
} else {
    echo "Préstamo no encontrado.";
}
$stmt->close();
$conn->close();
?>
