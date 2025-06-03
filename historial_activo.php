<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>ID de activo no proporcionado.</p>";
    exit;
}

$id_activo = intval($_GET['id']);

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sistema_activos");
if ($conexion->connect_error) {
    die("<p>Error de conexión: " . $conexion->connect_error . "</p>");
}

// Primero obtener información del activo
$consulta_activo = "SELECT nombre FROM activos WHERE id = $id_activo";
$resultado_activo = $conexion->query($consulta_activo);

if ($resultado_activo->num_rows == 0) {
    echo "<p>No se encontró el activo solicitado.</p>";
    exit;
}

$fila_activo = $resultado_activo->fetch_assoc();
$nombre_activo = $fila_activo['nombre'];

// Consultar el historial del activo
$consulta = "SELECT 
    h.id, 
    h.fecha_movimiento, 
    h.tipo_movimiento,
    h.observaciones,
    IFNULL(so.nombre, 'N/A') as sitio_origen,
    sd.nombre as sitio_destino,
    u.nombre_usuario
FROM 
    historial_activos h
    LEFT JOIN sitios so ON h.id_sitio_origen = so.id
    JOIN sitios sd ON h.id_sitio_destino = sd.id
    JOIN usuarios u ON h.id_usuario = u.id
WHERE 
    h.id_activo = $id_activo
ORDER BY 
    h.fecha_movimiento DESC";

$resultado = $conexion->query($consulta);

// Mostrar el historial
echo "<h4>Historial para: $nombre_activo</h4>";

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $tipo_clase = '';
        switch ($fila['tipo_movimiento']) {
            case 'traslado':
                $tipo_clase = 'badge-move';
                break;
            case 'asignación':
                $tipo_clase = 'badge-assign';
                break;
            case 'modificación':
                $tipo_clase = 'badge-modify';
                break;
            case 'baja':
                $tipo_clase = 'badge-remove';
                break;
        }
        
        echo "<div class='history-item'>";
        echo "<div class='history-date'>" . date('d/m/Y H:i', strtotime($fila['fecha_movimiento'])) . " - <span class='badge-status $tipo_clase'>" . ucfirst($fila['tipo_movimiento']) . "</span></div>";
        echo "<div class='history-details'>";
        echo "<strong>De:</strong> " . htmlspecialchars($fila['sitio_origen']) . " <strong>A:</strong> " . htmlspecialchars($fila['sitio_destino']) . "<br>";
        echo "<strong>Usuario:</strong> " . htmlspecialchars($fila['nombre_usuario']) . "<br>";
        if (!empty($fila['observaciones'])) {
            echo "<strong>Observaciones:</strong> " . htmlspecialchars($fila['observaciones']);
        }
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No hay registros en el historial para este activo.</p>";
}

$conexion->close();
?>