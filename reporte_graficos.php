<?php
require_once 'conexion.php';

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

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        body {
            color: #fff;
            background: linear-gradient(rgba(0, 0, 80, 0.85), rgba(0, 0, 60, 0.9)),
                        url('https://miro.medium.com/v2/resize:fit:1400/1*cRjevzZSKByeCrwjFmBrIg.jpeg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            width: 100%;
            background-color: rgba(0, 30, 60, 0.95);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        .navbar img {
            height: 50px;
        }
        .navbar h1 {
            font-size: 1.5rem;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }
        .dashboard {
            max-width: 1200px;
            margin: 40px auto 0 auto;
            padding: 0 20px 40px 20px;
            flex-grow: 1;
        }
        .assets-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px 0 30px 0;
            gap: 20px;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
        }
        .filter-btn {
            padding: 8px 16px;
            background: #f1f1f1;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            color: #00264d;
            transition: background 0.2s, color 0.2s;
        }
        .filter-btn.active, .filter-btn:focus {
            background: #FFD700;
            color: #00264d;
        }
        .cards-row {
            display: flex;
            gap: 24px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .card {
            flex: 1 1 320px;
            background-color: rgba(0, 30, 60, 0.92);
            border-radius: 12px;
            border: 2px solid #FFD700;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            padding: 24px 20px 16px 20px;
            margin-bottom: 0;
            min-width: 320px;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card h2 {
            font-size: 1.1em;
            color: #FFD700;
            margin-bottom: 18px;
            text-align: center;
            text-shadow: 1px 1px 3px #000;
        }
        .card canvas {
            margin: 0 auto;
            display: block;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 30, 60, 0.85);
            margin-top: auto;
        }
        @media (max-width: 900px) {
            .cards-row { flex-direction: column; }
            .dashboard { padding: 0 5px 40px 5px; }
            .card { min-width: 0; max-width: 100%; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <i class="fas fa-users me-1"></i> Historiales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <i class="fas fa-chart-bar me-1"></i> Registrar activos
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes graficos
                        </a>
                    </li>-->
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
            </div>
        </form>
        <div class="cards-row">
            <div class="card">
                <h2>Distribución por Estado (Pastel)</h2>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="card">
                <h2>Distribución por Categoría (Barras)</h2>
                <canvas id="barChart"></canvas>
            </div>
        </div>
        <div class="cards-row">
            <div class="card">
                <h2>Distribución por Estado (Línea)</h2>
                <canvas id="lineChart"></canvas>
            </div>
            <div class="card">
                <h2>Distribución por Categoría </h2>
                <canvas id="doughnutChart"></canvas>
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

    // Gráfica de pastel
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8'
                ],
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = pieData.reduce((a,b)=>a+b,0);
                            let value = context.parsed;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + percent + '%)';
                        }
                    }
                }
            }
        }
    });

    // Gráfica de barras
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Cantidad de Activos',
                data: barData,
                backgroundColor: '#1976d2'
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = barData.reduce((a,b)=>a+b,0);
                            let value = context.parsed.x;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return value + ' (' + percent + '%)';
                        }
                    }
                }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // Gráfica de línea
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [{
                label: 'Activos por Estado',
                data: lineData,
                fill: false,
                borderColor: '#3c8dbc',
                backgroundColor: '#3c8dbc',
                tension: 0.3
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfica doughnut
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: doughnutLabels,
            datasets: [{
                data: doughnutData,
                backgroundColor: [
                    '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40'
                ]
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    </script>
</body>
</html>
