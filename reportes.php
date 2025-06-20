<?php
include 'conexion.php';
include 'verificar_sesion.php';
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

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-boxes"></i> 
                <span class="d-none d-sm-inline">REPORTES</span>
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
                            <li><a class="dropdown-item" href="dashboard_admin.php"><i class="fas fa-home"></i> &nbsp; Inicio</a></li>
                            <li><a class="dropdown-item" href="formulario.php"><i class="fas fa-plus-circle"></i> &nbsp Formulario</a></li>
                            <li><a class="dropdown-item" href="estado_activos.php"><i class="fas fa-chart-line"></i> &nbsp Estado Activos</a></li>
                            <li><a class="dropdown-item" href="historiales.php"><i class="fas fa-history"></i> &nbsp Historiales</a></li>
                            <li><a class="dropdown-item" href="reporte_graficos.php"><i class="fas fa-chart-pie"></i> &nbsp Reportes Gráficos</a></li>
                            <li><a class="dropdown-item" href="regresion.php"><i class="fas fa-project-diagram"></i> &nbsp Regresión</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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