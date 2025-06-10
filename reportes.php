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
    <title>Reporte de activos</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="css/reportes.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="reportes.php">
                <i class="fas fa-clipboard-list" style='font-size:24px'></i>
                <span class="d-none d-sm-inline">REPORTE DE ACTIVOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                      <li class="nav-item">
                        <a class="nav-link active" href="dashboard_admin.html">
                            <i class='fas fa-home' ></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-steps" viewBox="0 0 16 16">
                            <path d="M.5 0a.5.5 0 0 1 .5.5v15a.5.5 0 0 1-1 0V.5A.5.5 0 0 1 .5 0M2 1.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5z"/>
                            </svg> Estado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="historiales.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                            </svg> Historiales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                            </svg> Registrar activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                             <i class='fas fa-chart-pie'></i> Reportes graficos
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
                    <!-- <th>ID</th> -->
                    <th>Nombre</th>
                    <!-- <th>Código</th> -->
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
                        <!-- <td><?= $a['id'] ?></td> -->
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <!-- <td><?= htmlspecialchars($a['codigoBarras']) ?></td> -->
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
                        <td colspan="6" class="text-center">No se encontraron activos con los filtros aplicados</td>
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