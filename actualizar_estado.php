<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: dashboard_admin.html.php");
    exit;
}

if (!isset($_POST['activo_id']) || !isset($_POST['nuevo_estado']) || !isset($_POST['sitio_destino'])) {
    echo "Error: Datos incompletos";
    exit;
}

// Obtener los datos del formulario
$activo_id = intval($_POST['activo_id']);
$nuevo_estado = intval($_POST['nuevo_estado']);
$sitio_destino = intval($_POST['sitio_destino']);
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

// En un entorno real, esto vendría de la sesión
$usuario_id = 1; // Usuario actual (simulado)

// Conectar a la base de datos
include 'conexion.php';

// Obtener el sitio actual del activo
$consulta = "SELECT id_sitio, id_estado FROM activos WHERE id = ?";
$stmt = $conn->prepare($consulta);
$stmt->bind_param("i", $activo_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $sitio_origen = $fila['id_sitio'];
    $estado_anterior = $fila['id_estado'];
    
    // Determinar el tipo de movimiento
    $tipo_movimiento = 'modificación';
    if ($sitio_origen != $sitio_destino) {
        $tipo_movimiento = 'traslado';
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Actualizar el estado y sitio del activo
        $actualizar = "UPDATE activos SET id_estado = ?, id_sitio = ? WHERE id = ?";
        $stmt = $conn->prepare($actualizar);
        $stmt->bind_param("iii", $nuevo_estado, $sitio_destino, $activo_id);
        $stmt->execute();
        
        // Registrar en el historial
        $historial = "INSERT INTO historial_activos 
                     (id_activo, id_sitio_origen, id_sitio_destino, id_usuario, tipo_movimiento, observaciones) 
                     VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($historial);
        $stmt->bind_param("iiiiss", $activo_id, $sitio_origen, $sitio_destino, $usuario_id, $tipo_movimiento, $observaciones);
        $stmt->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        // Redirigir con mensaje de éxito
        header("Location: dashboard_admin.html?success=1");
        exit;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        
        // Redirigir con mensaje de error
        header("Location: dashboard_admin.html?error=1&message=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // No se encontró el activo
    header("Location: dashboard_admin.html?error=1&message=Activo no encontrado");
    exit;
}

// Cerrar conexión
$conn->close();