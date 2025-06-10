<?php
date_default_timezone_set('America/Mexico_City');
require('fpdf/fpdf.php');
include 'conexion.php';

// Obtener filtros de la URL (GET)
$filtros = [
    'categoria' => $_GET['categoria'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'sitio' => $_GET['sitio'] ?? ''
];

// Función para obtener activos filtrados
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

// Función para truncar texto sin cortar palabras
function truncarTexto($texto, $maxLength = 80) {
    if (strlen($texto) <= $maxLength) return $texto;
    $cortado = substr($texto, 0, $maxLength);
    $ultimoEspacio = strrpos($cortado, ' ');
    return substr($cortado, 0, $ultimoEspacio) . '...';
}

$activos = getActivosFiltrados($conn, $filtros);

// Crear clase PDF
class PDF extends FPDF {
    public $categoriaLabel = '';

    function Header() {
        $this->Image('img/logo.jpg',10,8,15);

        // Fondo superior
        $this->SetFillColor(0, 30, 60); 
        $this->Rect(0, 0, $this->w, 30, 'F');

        $this->SetY(8);
        $this->SetFont('Arial','B',14);
        $this->SetTextColor(255, 215, 0);
        $this->Cell(0,7,utf8_decode('REPORTE DE ACTIVOS'),0,1,'C', false);

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(255,255,255);
        $this->Cell(0,6,utf8_decode($this->categoriaLabel),0,1,'C', false);

        $this->SetFont('Arial','',9);
        $this->Cell(0,6,'Fecha: ' . $this->fechaEsp(),0,1,'C', false);
        $this->Ln(2);
        
        // Encabezado de tabla
        $this->SetFillColor(255, 215, 0);
        $this->SetTextColor(0, 38, 77);
        $this->SetFont('Arial','B',9);
        // Agregar espacio antes de la tabla
        $this->Ln(5);

        $this->Cell(35,8,utf8_decode('Nombre'),1,0,'C',true);
        $this->Cell(22,8,utf8_decode('Estado'),1,0,'C',true);
        $this->Cell(28,8,utf8_decode('Ubicación'),1,0,'C',true);
        $this->Cell(15,8,'Cant.',1,0,'C',true);
        $this->Cell(80,8,utf8_decode('Último Reporte'),1,1,'C',true);

        $this->SetFont('Arial','',9);
        $this->SetTextColor(0,0,0);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',10);
        $this->SetTextColor(0, 5, 0);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
    }

    function fechaEsp() {
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es', 'ES');
        $now = new DateTime('now'); // Esto garantiza la fecha actual
        return ucfirst(strftime('%d de %B de %Y', $now->getTimestamp()));
    }
}

// Obtener categoría seleccionada (sin cambios)
$categoriaLabel = 'Todas las categorías';
if (!empty($filtros['categoria'])) {
    $catId = intval($filtros['categoria']);
    $catRes = $conn->query("SELECT nombre FROM categorias WHERE id = $catId");
    if ($catRes && $catRow = $catRes->fetch_assoc()) {
        $categoriaLabel = 'Categoría: ' . $catRow['nombre'];
    }
}

$pdf = new PDF();
$pdf->categoriaLabel = $categoriaLabel;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(245, 245, 245);

if (count($activos) === 0) {
    $pdf->SetTextColor(220,53,69);
    $pdf->Cell(0,10,'No se encontraron activos con los filtros aplicados.',0,1,'C');
} else {
    $fill = false;
    foreach ($activos as $a) {
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 240 : 255, $fill ? 240 : 255);
        $pdf->SetTextColor(0,0,0);

        $estado = strtolower($a['estado']);
        $estadoColor = [23, 162, 184];
        if ($estado === 'nuevo') $estadoColor = [40, 167, 69];
        elseif ($estado === 'dañado') $estadoColor = [220, 53, 69];
        elseif ($estado === 'en reparación') $estadoColor = [255, 193, 7];
        elseif ($estado === 'necesita renovacion') $estadoColor = [108, 117, 125];

        $pdf->Cell(35,8,utf8_decode($a['nombre']),1,0,'L',$fill);

        $pdf->SetFillColor($estadoColor[0], $estadoColor[1], $estadoColor[2]);
        $pdf->Cell(22,8,utf8_decode($a['estado']),1,0,'C',true);
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 240 : 255, $fill ? 240 : 255);

        $pdf->Cell(28,8,utf8_decode($a['sitio']),1,0,'L',$fill);
        $pdf->Cell(15,8,$a['cantidad'],1,0,'C',$fill);

        $reporte = $a['reporte'] ? utf8_decode(truncarTexto($a['reporte'], 150)) : 'Sin reportes';
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(80,8,$reporte,1,'L',$fill);
        $h = $pdf->GetY() - $y;
        if ($h < 8) {
            $pdf->SetY($y + 8);
        }

        $fill = !$fill;
    }
}

$pdf->Output('I', 'reporte_activos.pdf');
$conn->close();
?>
