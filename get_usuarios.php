<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$tipo = $_GET['tipo'] ?? '';
$response = [];

if (in_array($tipo, ['estudiante', 'docente'])) {
    $query = "SELECT id, nombre, apellido FROM usuarios_biblioteca WHERE tipo = ? AND activo = 1 ORDER BY nombre";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
}

echo json_encode($response);
?>