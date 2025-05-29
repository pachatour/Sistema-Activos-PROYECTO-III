
<?php
require 'vendor/autoload.php'; // Asegúrate de instalar Dompdf con Composer

use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión
include 'conexion.php';

// Filtros
$filtros = [
    'categoria' => $_GET['categoria'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'sitio' => $_GET['sitio'] ?? ''
];

// Consulta de activos con historial
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

// HTML base
$html = "<h2>Reporte de Activos</h2>";
foreach ($activos as $a) {
    $html .= "<hr><strong>ID:</strong> {$a['id']}

              <strong>Nombre:</strong> {$a['nombre']}

              <strong>Código:</strong> {$a['codigoBarras']}

              <strong>Categoría:</strong> {$a['categoria']}

              <strong>Estado:</strong> {$a['estado']}

              <strong>Ubicación:</strong> {$a['sitio']}

              <strong>Cantidad:</strong> {$a['cantidad']}
";

    // Historial del activo
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
        $html .= "<table border='1' cellspacing='0' cellpadding='5' style='width:100%; margin-top:10px'>
                    <tr>
                        <th>Fecha</th><th>Movimiento</th><th>Origen</th><th>Destino</th><th>Usuario</th><th>Obs.</th>
                    </tr>";
        foreach ($movimientos as $m) {
            $html .= "<tr>
                        <td>{$m['fecha_movimiento']}</td>
                        <td>{$m['tipo_movimiento']}</td>
                        <td>{$m['origen']}</td>
                        <td>{$m['destino']}</td>
                        <td>{$m['nombre_usuario']}</td>
                        <td>{$m['observaciones']}</td>
                    </tr>";
        }
        $html .= "</table>";
    } else {
        $html .= "<em>Sin historial registrado.</em>";
    }
}

// Generar PDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_activos.pdf", ["Attachment" => true]);
exit;
?>
 