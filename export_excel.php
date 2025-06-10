<?php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_activos.xls");
echo "\xEF\xBB\xBF"; // BOM para UTF-8

$pdo = new PDO("mysql:host=localhost;dbname=activos;charset=utf8", "root", "");

$filtros = [
    'categoria' => $_GET['categoria'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'sitio' => $_GET['sitio'] ?? ''
];

$sql = "SELECT a.id, a.nombre, a.codigoBarras, c.nombre AS categoria, e.nombre AS estado,
               s.nombre AS sitio, a.cantidad
        FROM activos a
        INNER JOIN categorias c ON a.id_categoria = c.id
        INNER JOIN estado_activos e ON a.id_estado = e.id
        INNER JOIN sitios s ON a.id_sitio = s.id
        WHERE 1=1";

$params = [];
if (!empty($filtros['categoria'])) {
    $sql .= " AND a.id_categoria = ?";
    $params[] = $filtros['categoria'];
}
if (!empty($filtros['estado'])) {
    $sql .= " AND a.id_estado = ?";
    $params[] = $filtros['estado'];
}
if (!empty($filtros['sitio'])) {
    $sql .= " AND a.id_sitio = ?";
    $params[] = $filtros['sitio'];
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$activos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tabla con estilos mejorados
echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;font-family:Arial,sans-serif;font-size:13px;'>";
echo "<tr style='background:#FFD700;color:#00264d;font-weight:bold;text-align:center;'>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Estado</th>
        <th>Ubicación</th>
        <th>Cantidad</th>
      </tr>";
foreach ($activos as $a) {
    echo "<tr style='background:#f8f9fa;'>";
    echo "<td>" . htmlspecialchars($a['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "<td>" . htmlspecialchars($a['categoria'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "<td>" . htmlspecialchars($a['estado'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "<td>" . htmlspecialchars($a['sitio'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "<td style='text-align:center;'>" . htmlspecialchars($a['cantidad'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "</tr>";

    // Historial por activo
    $hist = $pdo->prepare("SELECT h.fecha_movimiento, h.tipo_movimiento, s1.nombre AS origen, 
                                  s2.nombre AS destino, u.nombre_usuario, h.observaciones
                           FROM historial_activos h
                           LEFT JOIN sitios s1 ON h.id_sitio_origen = s1.id
                           INNER JOIN sitios s2 ON h.id_sitio_destino = s2.id
                           INNER JOIN usuarios u ON h.id_usuario = u.id
                           WHERE h.id_activo = ?
                           ORDER BY h.fecha_movimiento DESC");
    $hist->execute([$a['id']]);
    $movimientos = $hist->fetchAll(PDO::FETCH_ASSOC);

    if ($movimientos) {
        echo "<tr><td colspan='5' style='padding:0;'>";
        echo "<table border='1' width='100%' style='border-collapse:collapse;font-size:12px;'>";
        echo "<tr style='background:#00264d;color:#FFD700;text-align:center;'>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Usuario</th>
                <th>Observaciones</th>
              </tr>";
        foreach ($movimientos as $m) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($m['fecha_movimiento'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($m['tipo_movimiento'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($m['origen'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($m['destino'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($m['nombre_usuario'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($m['observaciones'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "</tr>";
        }
        echo "</table></td></tr>";
    } else {
        echo "<tr><td colspan='5' style='text-align:center;color:#dc3545;'>Sin historial de movimientos</td></tr>";
    }
}
echo "</table>";
exit;
?>
