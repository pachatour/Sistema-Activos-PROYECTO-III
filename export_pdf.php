<?php
require('fpdf/fpdf.php');
include 'conexion.php';

// Obtener filtros de la URL (GET)
$filtros = [
    'categoria' => $_GET['categoria'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'sitio' => $_GET['sitio'] ?? ''
];

// Función para obtener activos filtrados (igual que en tu código)
function getActivosFiltrados($conn, $filtros) {
    $sql = "SELECT a.id, a.nombre, a.codigoBarras, c.nombre AS categoria, e.nombre AS estado,
                   s.nombre AS sitio, a.cantidad, r.descripcion AS reporte
            FROM activos a
            INNER JOIN categorias c ON a.id_categoria = c.id
            INNER JOIN estado_activos e ON a.id_estado = e.id
            INNER JOIN sitios s ON a.id_sitio = s.id
            LEFT JOIN (
                SELECT id_activo, MAX(fecha_generacion) AS ultima_fecha, descripcion
                FROM reportes GROUP BY id_activo
            ) r ON a.id = r.id_activo
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

    if ($params) {
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }

    $activos = [];
    while ($row = $result->fetch_assoc()) {
        $activos[] = $row;
    }
    return $activos;
}

$activos = getActivosFiltrados($conn, $filtros);

// Crear PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Reporte de Activos',0,1,'C');
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(255, 215, 0);
        $this->Cell(10,8,'ID',1,0,'C',true);
        $this->Cell(40,8,'Nombre',1,0,'C',true);
        $this->Cell(25,8,'Código',1,0,'C',true);
        $this->Cell(30,8,'Categoría',1,0,'C',true);
        $this->Cell(25,8,'Estado',1,0,'C',true);
        $this->Cell(30,8,'Ubicación',1,0,'C',true);
        $this->Cell(15,8,'Cantidad',1,0,'C',true);
        $this->Cell(50,8,'Último Reporte',1,1,'C',true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

if (count($activos) === 0) {
    $pdf->Cell(0,10,'No se encontraron activos con los filtros aplicados.',0,1,'C');
} else {
    foreach ($activos as $a) {
        $pdf->Cell(10,8,$a['id'],1);
        $pdf->Cell(40,8,utf8_decode($a['nombre']),1);
        $pdf->Cell(25,8,utf8_decode($a['codigoBarras']),1);
        $pdf->Cell(30,8,utf8_decode($a['categoria']),1);
        $pdf->Cell(25,8,utf8_decode($a['estado']),1);
        $pdf->Cell(30,8,utf8_decode($a['sitio']),1);
        $pdf->Cell(15,8,$a['cantidad'],1,0,'C');
        $pdf->Cell(50,8,utf8_decode($a['reporte'] ?: 'Sin reportes'),1,1);
    }
}

$pdf->Output('I', 'reporte_activos.pdf');
$conn->close();
?>
