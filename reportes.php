<?php
include 'conexion.php';

// Obtener filtros desde POST
$filtros = [
    'categoria' => $_POST['categoria'] ?? '',
    'estado' => $_POST['estado'] ?? '',
    'sitio' => $_POST['sitio'] ?? ''
];

function getOptions($conn, $tabla) {
    $result = $conn->query("SELECT id, nombre FROM $tabla");
    $options = [];
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}

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

$categorias = getOptions($conn, 'categorias');
$estados = getOptions($conn, 'estado_activos');
$sitios = getOptions($conn, 'sitios');
$activos = getActivosFiltrados($conn, $filtros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Activos</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .form-container {
            background-color: rgba(0, 30, 60, 0.9);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 2px solid #FFD700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #FFD700;
            font-weight: bold;
        }

        .form-select {
            width: 100%;
            padding: 10px;
            background-color: rgba(7, 12, 77, 0.68);
            border: 1px solid #FFD700;
            border-radius: 6px;
            color: white;
            outline: none;
        }

        .form-select:focus {
            border-color: #3498db;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: #FFD700;
            color: #00264d;
        }

        .btn-primary:hover {
            background-color: #ffe033;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #bd2130;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(6, 43, 92, 0.94);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            margin-top: 20px;
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
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

        .status-new {
            background-color: #28a745;
        }

        .status-used {
            background-color: #17a2b8;
        }

        .status-damaged {
            background-color: #dc3545;
        }

        .status-repair {
            background-color: #ffc107;
            color: #000;
        }

        .status-renew {
            background-color: #6c757d;
        }

        .text-center {
            text-align: center;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .inventory-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="crud_libros.php">
                <i class="fas fa-clipboard-list" style='font-size:24px'></i>
                <span class="d-none d-sm-inline">REPORTE DE ACTIVOS</span>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes graficos
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link active" href="reportes.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes
                        </a>
                    </li>-->
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
        <div class="form-container">
            <form method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $filtros['categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($estados as $est): ?>
                                <option value="<?= $est['id'] ?>" <?= $est['id'] == $filtros['estado'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($est['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Ubicación</label>
                        <select name="sitio" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($sitios as $sit): ?>
                                <option value="<?= $sit['id'] ?>" <?= $sit['id'] == $filtros['sitio'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sit['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="reportes.php" class="btn btn-secondary">
                        <i class="fas fa-eraser"></i> Limpiar
                    </a>
                    <a href="export_pdf.php?<?= http_build_query($filtros) ?>" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="export_excel.php?<?= http_build_query($filtros) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </div>
            </form>
        </div>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Cantidad</th>
                    <th>Último Reporte</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activos as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['codigoBarras']) ?></td>
                        <td><?= htmlspecialchars($a['categoria']) ?></td>
                        <td>
                            <?php 
                            $clase_estado = '';
                            switch(strtolower($a['estado'])) {
                                case 'nuevo': $clase_estado = 'status-new'; break;
                                case 'usado': $clase_estado = 'status-used'; break;
                                case 'dañado': $clase_estado = 'status-damaged'; break;
                                case 'en reparación': $clase_estado = 'status-repair'; break;
                                case 'necesita renovacion': $clase_estado = 'status-renew'; break;
                                default: $clase_estado = 'status-used';
                            }
                            ?>
                            <span class="status-badge <?= $clase_estado ?>">
                                <?= htmlspecialchars($a['estado']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($a['sitio']) ?></td>
                        <td><?= $a['cantidad'] ?></td>
                        <td><?= $a['reporte'] ? htmlspecialchars($a['reporte']) : 'Sin reportes' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($activos)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No se encontraron activos con los filtros aplicados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Función para filtrar la tabla en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.id = 'searchInput';
            searchInput.placeholder = 'Buscar activo...';
            searchInput.style.marginLeft = '10px';
            searchInput.style.padding = '8px';
            searchInput.style.borderRadius = '20px';
            searchInput.style.border = '1px solid #FFD700';
            searchInput.style.background = 'rgba(255,255,255,0.1)';
            searchInput.style.color = 'white';
            searchInput.style.outline = 'none';
            
            const searchContainer = document.createElement('div');
            searchContainer.style.display = 'flex';
            searchContainer.style.alignItems = 'center';
            searchContainer.style.marginBottom = '20px';
            
            const searchIcon = document.createElement('i');
            searchIcon.className = 'fas fa-search';
            searchIcon.style.marginRight = '10px';
            
            searchContainer.appendChild(searchIcon);
            searchContainer.appendChild(searchInput);
            
            const table = document.querySelector('.inventory-table');
            table.parentNode.insertBefore(searchContainer, table);
            
            searchInput.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('.inventory-table tbody tr');
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let found = false;
                    
                    for (let i = 0; i < cells.length; i++) {
                        if (cells[i].textContent.toLowerCase().includes(searchValue)) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>