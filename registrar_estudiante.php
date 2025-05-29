<?php
require_once 'conexion.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $email = $_POST['email'] ?? '';
    $nivel = $_POST['nivel'] ?? 'primario';
    $curso = $_POST['curso'] ?? 'primero';
    $paralelo = $_POST['paralelo'] ?? 'A';
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($apellido) || empty($codigo)) {
        $response['message'] = 'Nombre, apellido y código son obligatorios';
        echo json_encode($response);
        exit();
    }
    
    // Verificar si el código ya existe
    $existe = $conn->query("SELECT id_estudiante FROM estudiantes WHERE codigo_estudiante = '$codigo'");
    
    if ($existe->num_rows > 0) {
        $response['message'] = 'El código de estudiante ya está registrado';
    } else {
        // Insertar nuevo estudiante
        $stmt = $conn->prepare("INSERT INTO estudiantes 
                              (nombre, apellido, codigo_estudiante, email, nivel, curso, paralelo) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nombre, $apellido, $codigo, $email, $nivel, $curso, $paralelo);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['id'] = $stmt->insert_id;
            $response['message'] = 'Estudiante registrado con éxito';
        } else {
            $response['message'] = 'Error al registrar estudiante: ' . $conn->error;
        }
    }
}

echo json_encode($response);
$conn->close();
?>