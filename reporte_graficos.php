<?php
include 'conexion.php';

// Obtener filtros
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Consultar categorías y estados únicos
$categorias = [];
$estados = [];
$res = $conn->query("SELECT id, nombre FROM categorias");
while ($row = $res->fetch_assoc()) $categorias[] = $row;
$res = $conn->query("SELECT id, nombre FROM estado_activos");
while ($row = $res->fetch_assoc()) $estados[] = $row;

// Construir consulta filtrada
$where = [];
if ($estado) $where[] = "a.id_estado='" . $conn->real_escape_string($estado) . "'";
if ($categoria) $where[] = "a.id_categoria='" . $conn->real_escape_string($categoria) . "'";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Datos para gráfica de pastel (por estado)
$data_estado = [];
$sql_estado = "
    SELECT ea.nombre as estado, COUNT(*) as total
    FROM activos a
    JOIN estado_activos ea ON a.id_estado = ea.id
    JOIN categorias c ON a.id_categoria = c.id
    $where_sql
    GROUP BY ea.nombre
";
$res = $conn->query($sql_estado);
$total_estado = 0;
while ($row = $res->fetch_assoc()) {
    $data_estado[$row['estado']] = $row['total'];
    $total_estado += $row['total'];
}

// Datos para gráfica de barras (por categoría)
$data_categoria = [];
$sql_categoria = "
    SELECT c.nombre as categoria, COUNT(*) as total
    FROM activos a
    JOIN categorias c ON a.id_categoria = c.id
    JOIN estado_activos ea ON a.id_estado = ea.id
    $where_sql
    GROUP BY c.nombre
";
$res = $conn->query($sql_categoria);
while ($row = $res->fetch_assoc()) {
    $data_categoria[$row['categoria']] = $row['total'];
}

// Datos para gráfica de línea (evolución de activos por estado, ejemplo ficticio)
$data_line_labels = array_keys($data_estado);
$data_line = array_values($data_estado);

// Datos para gráfica doughnut (por categoría)
$data_doughnut_labels = array_keys($data_categoria);
$data_doughnut = array_values($data_categoria);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gráficos de Activos</title>
    <link rel="stylesheet" href="Carita.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="css/reporte_graficos.css">
    <style>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="crud_libros.php">
                <img src="https://cdn-icons-png.freepik.com/256/1321/1321887.png?semt=ais_hybrid" alt="Logo" />
                <span class="d-none d-sm-inline">  REPORTE GRÁFICO DE ACTIVOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <i class="fas fa-exchange-alt me-1"></i> Estado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="historiales.php">
                            <i class="fas fa-history"></i>  Historiales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <i class="fas fa-plus-circle"></i> Registrar activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reportes.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger logout-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i><b> Cerrar Sesión</b>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="dashboard">
        <form method="get" class="assets-header">
            <div class="filter-buttons">
                <select name="estado" class="filter-btn">
                    <option value="">Todos los estados</option>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= htmlspecialchars($e['id']) ?>" <?= $estado==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="categoria" class="filter-btn">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= htmlspecialchars($c['id']) ?>" <?= $categoria==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="filter-btn active">Filtrar</button>
                <button type="button" id="exportAllBtn" class="filter-btn active">
                    <i class="fas fa-download"></i> Exportar Todo
                </button>
            </div>
        </form>
        <div class="cards-row">
            <div class="card">
                <h2>Distribución por Estado (Pastel)</h2>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
                
                <!-- Tabla de datos para gráfico de pastel -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_pie = array_sum($data_estado);
                        foreach ($data_estado as $estado_nombre => $cantidad): 
                            $porcentaje = $total_pie > 0 ? round(($cantidad / $total_pie) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($estado_nombre) ?></td>
                            <td><?= $cantidad ?></td>
                            <td><?= $porcentaje ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td><strong>Total</strong></td>
                            <td><strong><?= $total_pie ?></strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="chart-actions">
                    <button class="chart-btn" onclick="exportChart('pieChart', 'distribucion_estado_pastel')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                    <button class="chart-btn" onclick="printChart('pieChart')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
            <div class="card">
                <h2>Distribución por Categoría (Barras)</h2>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
                
                <!-- Tabla de datos para gráfico de barras -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_bar = array_sum($data_categoria);
                        foreach ($data_categoria as $categoria_nombre => $cantidad): 
                            $porcentaje = $total_bar > 0 ? round(($cantidad / $total_bar) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($categoria_nombre) ?></td>
                            <td><?= $cantidad ?></td>
                            <td><?= $porcentaje ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td><strong>Total</strong></td>
                            <td><strong><?= $total_bar ?></strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="chart-actions">
                    <button class="chart-btn" onclick="exportChart('barChart', 'distribucion_categoria_barras')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                    <button class="chart-btn" onclick="printChart('barChart')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
        <div class="cards-row">
            <div class="card">
                <h2>Distribución por Estado (Línea)</h2>
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
                
                <!-- Tabla de datos para gráfico de línea -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_line = array_sum($data_estado);
                        foreach ($data_estado as $estado_nombre => $cantidad): 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($estado_nombre) ?></td>
                            <td><?= $cantidad ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td><strong>Total</strong></td>
                            <td><strong><?= $total_line ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="chart-actions">
                    <button class="chart-btn" onclick="exportChart('lineChart', 'distribucion_estado_linea')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                    <button class="chart-btn" onclick="printChart('lineChart')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
            <div class="card">
                <h2>Distribución por Categoría (Dona)</h2>
                <div class="chart-container">
                    <canvas id="doughnutChart"></canvas>
                </div>
                
                <!-- Tabla de datos para gráfico de dona -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_doughnut = array_sum($data_categoria);
                        foreach ($data_categoria as $categoria_nombre => $cantidad): 
                            $porcentaje = $total_doughnut > 0 ? round(($cantidad / $total_doughnut) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($categoria_nombre) ?></td>
                            <td><?= $cantidad ?></td>
                            <td><?= $porcentaje ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td><strong>Total</strong></td>
                            <td><strong><?= $total_doughnut ?></strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="chart-actions">
                    <button class="chart-btn" onclick="exportChart('doughnutChart', 'distribucion_categoria_dona')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                    <button class="chart-btn" onclick="printChart('doughnutChart')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    <footer>
        Sistema de Gestión de Activos &copy; <?= date('Y') ?> | Dashboard Gráficos
    </footer>
    <script>
    // Datos para gráfica de pastel
    const pieLabels = <?= json_encode(array_keys($data_estado)) ?>;
    const pieData = <?= json_encode(array_values($data_estado)) ?>;
    // Datos para gráfica de barras
    const barLabels = <?= json_encode(array_keys($data_categoria)) ?>;
    const barData = <?= json_encode(array_values($data_categoria)) ?>;
    // Datos para gráfica de línea
    const lineLabels = <?= json_encode($data_line_labels) ?>;
    const lineData = <?= json_encode($data_line) ?>;
    // Datos para gráfica doughnut
    const doughnutLabels = <?= json_encode($data_doughnut_labels) ?>;
    const doughnutData = <?= json_encode($data_doughnut) ?>;

    // Colores consistentes para los gráficos
    const chartColors = [
        '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8',
        '#6610f2', '#e83e8c', '#fd7e14', '#20c997', '#0dcaf0'
    ];

    // Gráfica de pastel
    const pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: chartColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = pieData.reduce((a,b)=>a+b,0);
                            let value = context.parsed;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return `${context.label}: ${value} (${percent}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribución de Activos por Estado',
                    font: {
                        size: 16
                    }
                }
            }
        }
    });

    // Gráfica de barras
    const barChart = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Cantidad de Activos',
                data: barData,
                backgroundColor: chartColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = barData.reduce((a,b)=>a+b,0);
                            let value = context.parsed.y;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return `${value} activos (${percent}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Activos por Categoría',
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                x: { 
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Activos'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Categorías'
                    }
                }
            }
        }
    });

    // Gráfica de línea
    const lineChart = new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [{
                label: 'Activos por Estado',
                data: lineData,
                fill: false,
                borderColor: '#3c8dbc',
                backgroundColor: '#3c8dbc',
                borderWidth: 3,
                pointBackgroundColor: '#3c8dbc',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} activos`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribución de Activos por Estado',
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Activos'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Estados'
                    }
                }
            }
        }
    });

    // Gráfica doughnut
    const doughnutChart = new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: doughnutLabels,
            datasets: [{
                data: doughnutData,
                backgroundColor: chartColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = doughnutData.reduce((a,b)=>a+b,0);
                            let value = context.parsed;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return `${context.label}: ${value} (${percent}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribución de Activos por Categoría',
                    font: {
                        size: 16
                    }
                }
            },
            cutout: '60%'
        }
    });

    // Función para exportar un gráfico como imagen
    function exportChart(chartId, fileName) {
        const chartCanvas = document.getElementById(chartId);
        const link = document.createElement('a');
        link.download = `${fileName}.png`;
        link.href = chartCanvas.toDataURL('image/png');
        link.click();
    }

    // Función para imprimir un gráfico
    function printChart(chartId) {
        const chartCanvas = document.getElementById(chartId);
        const win = window.open('', '', 'width=800,height=600');
        win.document.write('<html><head><title>Imprimir Gráfico</title></head><body>');
        win.document.write(`<img src="${chartCanvas.toDataURL('image/png')}" style="max-width:100%;"/>`);
        win.document.write('</body></html>');
        win.document.close();
        win.focus();
        setTimeout(() => {
            win.print();
            win.close();
        }, 500);
    }

    // Función para exportar todos los gráficos como PDF
    document.getElementById('exportAllBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');
        const charts = ['pieChart', 'barChart', 'lineChart', 'doughnutChart'];
        const promises = [];
        
        charts.forEach((chartId, index) => {
            const canvas = document.getElementById(chartId);
            promises.push(
                html2canvas(canvas).then(canvasImage => {
                    const imgData = canvasImage.toDataURL('image/png');
                    if (index === 0) {
                        doc.addImage(imgData, 'PNG', 20, 20, 250, 150);
                    } else if (index === 1) {
                        doc.addImage(imgData, 'PNG', 20, 180, 250, 150);
                    } else if (index === 2) {
                        doc.addPage();
                        doc.addImage(imgData, 'PNG', 20, 20, 250, 150);
                    } else if (index === 3) {
                        doc.addImage(imgData, 'PNG', 20, 180, 250, 150);
                    }
                })
            );
        });
        
        Promise.all(promises).then(() => {
            doc.save('reporte_graficos_activos.pdf');
        });
    });
    </script>
</body>
</html>