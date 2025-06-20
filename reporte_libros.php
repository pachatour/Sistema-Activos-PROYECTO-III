<?php
require_once 'conexion.php';
include 'verificar_sesion.php';
// Consulta de reportes de libros (préstamos y devoluciones)
$reportes = $conn->query("
    SELECT p.id, 
           a.nombre AS libro,
           a.codigoBarras,
           ub.nombre AS usuario_nombre,
           ub.apellido AS usuario_apellido,
           ub.tipo AS tipo_usuario,
           p.fecha_prestamo,
           p.fecha_devolucion_esperada,
           p.fecha_devolucion_real,
           p.estado,
           p.observaciones
    FROM prestamos p
    JOIN activos a ON p.id_activo = a.id
    JOIN usuarios_biblioteca ub ON p.id_usuario_biblioteca = ub.id
    WHERE a.id_categoria = 2
    ORDER BY p.fecha_prestamo DESC
");

// Datos para gráficos: contar libros por estado
$grafico_estados = $conn->query("
    SELECT p.estado, COUNT(*) as total
    FROM prestamos p
    JOIN activos a ON p.id_activo = a.id
    WHERE a.id_categoria = 2
    GROUP BY p.estado
");
$labels = [];
$values = [];
while ($row = $grafico_estados->fetch_assoc()) {
    $labels[] = ucfirst($row['estado']);
    $values[] = (int)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Libros - Biblioteca</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .navbar h1 {
            font-size: 1.5rem;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }
        .dashboard {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .table-responsive {
            background: rgba(0, 30, 60, 0.93);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(0, 30, 60, 0.8);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        .inventory-table th {
            background-color: #FFD700;
            color: #00264d;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        .inventory-table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .inventory-table tr:last-child td {
            border-bottom: none;
        }
        .inventory-table tr:hover {
            background-color: rgba(255, 215, 0, 0.1);
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-prestado { background-color: #28a745; }
        .status-devuelto { background-color: #17a2b8; }
        .status-atrasado { background-color: #dc3545; }
        .status-disponible { background-color: #6c757d; }
        .status-alerta { background-color: #ffc107; color: #000; }
        .obs-cell { max-width: 250px; white-space: pre-line; word-break: break-word; }
        .charts-row {
            display: flex;
            gap: 30px;
            margin: 40px 0 0 0;
            flex-wrap: wrap;
            justify-content: center;
        }
        .chart-card {
            background: rgba(0, 30, 60, 0.93);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            padding: 24px 20px 16px 20px;
            min-width: 320px;
            max-width: 420px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-card h3 {
            color: #FFD700;
            font-size: 1.1em;
            margin-bottom: 18px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .dashboard { padding: 10px; }
            .table-responsive { padding: 5px; }
            .inventory-table th, .inventory-table td { padding: 8px 6px; }
            .charts-row { flex-direction: column; gap: 18px; }
            .chart-card { min-width: 0; max-width: 100%; }
        }
    </style>


    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
    <div class="container-fluid">
        <a class="navbar-brand" href="biblio_dashboard.php">
            <i class="fas fa-boxes"></i>
            <span class="d-none d-sm-inline">Reporte de Libros (Préstamos y Devoluciones)</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-arrow-alt-circle-down"></i> Ir a 
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="biblio_dashboard.php"><i class="fas fa-home"></i> &nbsp; Inicio</a></li>
                        <li><a class="dropdown-item" href="prestamos.php"><i class='fas fa-cubes'></i> &nbsp; Prestar </a></li>
                        <li><a class="dropdown-item" href="crud_estudiantes.php"><i class="fas fa-plus-circle"></i> &nbsp; Usuarios</a></li>
                        <li><a class="dropdown-item" href="crud_libros.php"><i class="fas fa-file-alt"></i> &nbsp; Libros</a></li>
                        <li><a class="dropdown-item" href="dashboard_prestamos.php"><i class="fas fa-history"></i> &nbsp; Prestamos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp; Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="dashboard">
    <div class="table-responsive">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libro</th>
                    <th>Código</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Fecha Préstamo</th>
                    <th>Devolución Esperada</th>
                    <th>Devolución Real</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $reportes->fetch_assoc()): 
                    $estado_clase = 'status-' . strtolower($r['estado']);
                    $estado_texto = ucfirst($r['estado']);
                    if ($r['estado'] === 'prestado') {
                        $fecha_devolucion = new DateTime($r['fecha_devolucion_esperada']);
                        $hoy = new DateTime();
                        if ($hoy > $fecha_devolucion) {
                            $estado_clase = 'status-atrasado';
                            $estado_texto = 'Atrasado';
                        } elseif ($hoy->diff($fecha_devolucion)->days <= 1) {
                            $estado_clase = 'status-alerta';
                            $estado_texto = 'Por vencer';
                        }
                    }
                ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['libro']) ?></td>
                    <td><?= htmlspecialchars($r['codigoBarras']) ?></td>
                    <td><?= htmlspecialchars($r['usuario_nombre'] . ' ' . $r['usuario_apellido']) ?></td>
                    <td><?= ucfirst($r['tipo_usuario']) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['fecha_prestamo'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['fecha_devolucion_esperada'])) ?></td>
                    <td><?= $r['fecha_devolucion_real'] ? date('d/m/Y', strtotime($r['fecha_devolucion_real'])) : '-' ?></td>
                    <td><span class="status-badge <?= $estado_clase ?>"><?= $estado_texto ?></span></td>
                    <td class="obs-cell"><?= $r['observaciones'] ? htmlspecialchars($r['observaciones']) : '-' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Gráficos de libros por estado -->
    <div class="charts-row">
        <div class="chart-card">
            <h3>Libros por Estado (Pastel)</h3>
            <canvas id="pieLibrosEstado"></canvas>
        </div>
        <div class="chart-card">
            <h3>Libros por Estado (Barras)</h3>
            <canvas id="barLibrosEstado"></canvas>
        </div>
    </div>
</div>
<script>
    // Gráficos de libros por estado
    const labels = <?= json_encode($labels) ?>;
    const values = <?= json_encode($values) ?>;

    // Pastel
    new Chart(document.getElementById('pieLibrosEstado'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#28a745', '#17a2b8', '#dc3545', '#ffc107', '#6c757d'
                ]
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = values.reduce((a,b)=>a+b,0);
                            let value = context.parsed;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + percent + '%)';
                        }
                    }
                }
            }
        }
    });

    // Barras
    new Chart(document.getElementById('barLibrosEstado'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad de Libros',
                data: values,
                backgroundColor: '#FFD700'
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = values.reduce((a,b)=>a+b,0);
                            let value = context.parsed.y;
                            let percent = total ? (value/total*100).toFixed(1) : 0;
                            return value + ' (' + percent + '%)';
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #002244; color: #fff;">
        <div class="modal-header">
            <h5 class="modal-title" id="logoutModalLabel"><i class="fas fa-exclamation-circle text-warning"></i> Confirmar salida</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            ¿Estás seguro de que deseas cerrar sesión?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
        </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
$conn->close();
?>