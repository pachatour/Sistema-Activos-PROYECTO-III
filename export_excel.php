
<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_activos.xls");

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

// Tabla
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Código</th><th>Categoría</th><th>Estado</th><th>Sitio</th><th>Cantidad</th></tr>";
foreach ($activos as $a) {
    echo "<tr>
            <td>{$a['id']}</td>
            <td>{$a['nombre']}</td>
            <td>{$a['codigoBarras']}</td>
            <td>{$a['categoria']}</td>
            <td>{$a['estado']}</td>
            <td>{$a['sitio']}</td>
            <td>{$a['cantidad']}</td>
        </tr>";

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
        echo "<tr><td colspan='7'>
                <table border='1' width='100%'>
                    <tr><th>Fecha</th><th>Tipo</th><th>Origen</th><th>Destino</th><th>Usuario</th><th>Obs.</th></tr>";
        foreach ($movimientos as $m) {
            echo "<tr>
                    <td>{$m['fecha_movimiento']}</td>
                    <td>{$m['tipo_movimiento']}</td>
                    <td>{$m['origen']}</td>
                    <td>{$m['destino']}</td>
                    <td>{$m['nombre_usuario']}</td>
                    <td>{$m['observaciones']}</td>
                  </tr>";
        }
        echo "</table></td></tr>";
    } else {
        echo "<tr><td colspan='7'>Sin historial de movimientos</td></tr>";
    }
}
echo "</table>";
exit;
?>
 