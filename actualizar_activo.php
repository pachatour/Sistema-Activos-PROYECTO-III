<?php
require_once 'conexion.php';

header('Content-Type: application/json');

if (!isset($_POST['id'], $_POST['descripcion'], $_POST['cantidad'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id = intval($_POST['id']);
$descripcion = trim($_POST['descripcion']);
$cantidad = intval($_POST['cantidad']);

if ($id <= 0 || $cantidad <= 0 || $descripcion === '') {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$stmt = $conn->prepare("UPDATE activos SET descripcion = ?, cantidad = ? WHERE id = ?");
$stmt->bind_param("sii", $descripcion, $cantidad, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
}

$stmt->close();
$conn->close();
