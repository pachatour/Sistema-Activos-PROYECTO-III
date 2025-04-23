<?php
session_start();
include 'conexion.php';

// Función para obtener opciones de las tablas relacionadas
function obtenerOpciones($conexion, $tabla, $campo_id, $campo_nombre) {
    $opciones = [];
    $query = "SELECT $campo_id, $campo_nombre FROM $tabla";
    $result = $conexion->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $opciones[$row[$campo_id]] = $row[$campo_nombre];
        }
    }
    return $opciones;
}

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar conexión a la base de datos
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Recoger y sanitizar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $id_estado = intval($_POST['id_estado'] ?? 0);
    $id_sitio = intval($_POST['id_sitio'] ?? 0);
    $id_categoria = intval($_POST['id_categoria'] ?? 0);

    // Validar campos requeridos
    if (empty($nombre) || empty($descripcion) || $id_estado <= 0 || $id_sitio <= 0 || $id_categoria <= 0) {
        $_SESSION['error'] = "Todos los campos requeridos deben estar completos";
        header('Location: registro_activos.php');
        exit();
    }

    // Insertar el activo (el código de barras se generará después)
    $stmt = $conexion->prepare("INSERT INTO activos (nombre, descripcion, id_estado, id_sitio, id_categoria) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $nombre, $descripcion, $id_estado, $id_sitio, $id_categoria);

    if ($stmt->execute()) {
        $id_activo = $conexion->insert_id;
        
        // Generar código de barras basado en el ID del activo
        $codigoBarras = "ACT-" . str_pad($id_activo, 6, '0', STR_PAD_LEFT);
        
        // Actualizar el activo con el código de barras generado
        $update_stmt = $conexion->prepare("UPDATE activos SET codigoBarras = ? WHERE id = ?");
        $update_stmt->bind_param("si", $codigoBarras, $id_activo);
        $update_stmt->execute();
        $update_stmt->close();
        
        $_SESSION['mensaje'] = "Activo registrado correctamente. Código de barras: " . $codigoBarras;
        $_SESSION['codigo_barras'] = $codigoBarras;
    } else {
        $_SESSION['error'] = "Error al registrar el activo: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
    
    // Redirigir para evitar reenvío del formulario
    header('Location: registro_activos.php');
    exit();
}
?>