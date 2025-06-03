<?php
require_once 'conexion.php';

// Verificar que la conexión se realizó correctamente
if (!isset($conn) || !$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Consultas para obtener datos estadísticos
$total_activos = $conn->query("SELECT COUNT(*) as total FROM activos")->fetch_assoc()['total'];
$activos_operativos = $conn->query("SELECT COUNT(*) as total FROM activos WHERE id_estado = 1")->fetch_assoc()['total'];
$total_reportes = $conn->query("SELECT COUNT(*) as total FROM reportes")->fetch_assoc()['total'];
$en_reparacion = $conn->query("SELECT COUNT(*) as total FROM activos WHERE id_estado = 4")->fetch_assoc()['total'];

// Consulta para obtener los activos con información relacionada
$query_activos = "SELECT a.*, 
                 ea.nombre as estado_nombre, 
                 c.nombre as categoria_nombre, 
                 s.nombre as sitio_nombre 
                 FROM activos a
                 LEFT JOIN estado_activos ea ON a.id_estado = ea.id
                 LEFT JOIN categorias c ON a.id_categoria = c.id
                 LEFT JOIN sitios s ON a.id_sitio = s.id
                 ORDER BY a.id";
$resultado_activos = $conn->query($query_activos);

// Consulta para obtener las ubicaciones (sitios) únicas
$sitios_result = $conn->query("SELECT id, nombre FROM sitios ORDER BY nombre ASC");
$sitios = [];
while ($row = $sitios_result->fetch_assoc()) {
    $sitios[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Activos</title>
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

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            background-color: rgba(0, 30, 60, 0.9);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #FFD700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #FFD700;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .stat-card p {
            font-size: 2rem;
            font-weight: bold;
        }

        .inventory-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-filter {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: rgba(255, 255, 255, 0.3);
            padding: 8px 15px;
            border-radius: 30px;
        }

        #searchInput {
            background: transparent;
            border: none;
            color: white;
            padding: 5px 10px;
            width: 200px;
            outline: none;
        }

        #searchInput::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .filter-btn {
            background-color: rgba(0, 30, 60, 0.9);
            color: white;
            border: 1px solid #FFD700;
            padding: 8px 15px;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filter-btn:hover, .filter-btn.active {
            background-color: #FFD700;
            color: #00264d;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(6, 43, 92, 0.94);
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

        .action-btn {
            background: none;
            border: none;
            color: #FFD700;
            cursor: pointer;
            margin: 0 5px;
            font-size: 1rem;
            transition: transform 0.3s;
        }

        .action-btn:hover {
            transform: scale(1.2);
        }

        .location-filter {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 8px 15px;
            margin-left: 10px;
            outline: none;
            font-size: 1rem;
        }
        .location-filter option {
            color: #00264d;
        }

        @media (max-width: 768px) {
            .stats-container {
                flex-direction: column;
            }
            
            .stat-card {
                width: 100%;
            }
            
            .inventory-table {
                display: block;
                overflow-x: auto;
            }
            
            .inventory-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
    <div class="container-fluid">
        <a class="navbar-brand" href="prestamos.php">
            <i class="fas fa-boxes"></i> 
            <span class="d-none d-sm-inline">Sistema de Gestión de Inventario</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                    <!--<li class="nav-item">
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>-->
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
        <div class="inventory-header">
            <div class="filter-buttons">
                <button class="filter-btn active">Todos</button>
                <?php
                // Obtener categorías desde la base de datos
                $categorias_result = $conn->query("SELECT nombre FROM categorias ORDER BY nombre ASC");
                while ($cat = $categorias_result->fetch_assoc()) {
                    echo '<button class="filter-btn">' . htmlspecialchars($cat['nombre']) . '</button>';
                }
                ?>
            </div>
            <div class="search-filter">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar activo...">
                <select id="locationFilter" class="location-filter">
                    <option value="">Todas las ubicaciones</option>
                    <?php foreach ($sitios as $sitio): ?>
                        <option value="<?php echo htmlspecialchars($sitio['nombre']); ?>">
                            <?php echo htmlspecialchars($sitio['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Categoría</th>
                    <th>Ubicación</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($activo = $resultado_activos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($activo['id']); ?></td>
                    <td><?php echo htmlspecialchars($activo['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($activo['descripcion']); ?></td>
                    <td>
                        <?php 
                        $clase_estado = '';
                        switch($activo['id_estado']) {
                            case 1: $clase_estado = 'status-new'; break;
                            case 2: $clase_estado = 'status-used'; break;
                            case 3: $clase_estado = 'status-damaged'; break;
                            case 4: $clase_estado = 'status-repair'; break;
                            case 5: $clase_estado = 'status-renew'; break;
                            default: $clase_estado = 'status-used';
                        }
                        ?>
                        <span class="status-badge <?php echo $clase_estado; ?>">
                            <?php echo htmlspecialchars($activo['estado_nombre']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($activo['categoria_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($activo['sitio_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($activo['cantidad']); ?></td>
                    <td>
                        <button class="action-btn" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="action-btn" title="Historial"><i class="fas fa-history"></i></button>
                        <button class="action-btn" title="Reportar"><i class="fas fa-exclamation-circle"></i></button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Función para filtrar la tabla en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.inventory-table tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;
                
                // Buscar en todas las celdas excepto la última (acciones)
                for (let i = 0; i < cells.length - 1; i++) {
                    if (cells[i].textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            });
        });

        // Función para filtrar por categoría
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Quitar clase active de todos los botones
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Añadir clase active al botón clickeado
                this.classList.add('active');
                
                const categoria = this.textContent.trim();
                const rows = document.querySelectorAll('.inventory-table tbody tr');
                
                rows.forEach(row => {
                    const cellCategoria = row.querySelector('td:nth-child(5)');
                    
                    if (categoria === 'Todos' || cellCategoria.textContent.trim() === categoria) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Filtro por ubicación
        document.getElementById('locationFilter').addEventListener('change', function() {
            const selectedLocation = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('.inventory-table tbody tr');
            rows.forEach(row => {
                const cellLocation = row.querySelector('td:nth-child(6)');
                // Si no hay filtro o coincide la ubicación, mostrar
                if (!selectedLocation || cellLocation.textContent.trim().toLowerCase() === selectedLocation) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            // Si hay texto en el buscador, aplicar también el filtro de búsqueda
            document.getElementById('searchInput').dispatchEvent(new Event('input'));
        });

        // Modificar el filtro de búsqueda para que respete el filtro de ubicación
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const selectedLocation = document.getElementById('locationFilter').value.trim().toLowerCase();
            const rows = document.querySelectorAll('.inventory-table tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;
                // Buscar en todas las celdas excepto la última (acciones)
                for (let i = 0; i < cells.length - 1; i++) {
                    if (cells[i].textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                        break;
                    }
                }
                // Filtro por ubicación
                const cellLocation = row.querySelector('td:nth-child(6)');
                const locationMatch = !selectedLocation || cellLocation.textContent.trim().toLowerCase() === selectedLocation;
                row.style.display = (found && locationMatch) ? '' : 'none';
            });
        });

        // Modificar el filtro por categoría para que respete el filtro de ubicación y búsqueda
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const categoria = this.textContent.trim();
                const selectedLocation = document.getElementById('locationFilter').value.trim().toLowerCase();
                const searchValue = document.getElementById('searchInput').value.toLowerCase();
                const rows = document.querySelectorAll('.inventory-table tbody tr');
                rows.forEach(row => {
                    const cellCategoria = row.querySelector('td:nth-child(5)');
                    const cellLocation = row.querySelector('td:nth-child(6)');
                    // Filtro por categoría
                    const categoriaMatch = (categoria === 'Todos' || cellCategoria.textContent.trim() === categoria);
                    // Filtro por ubicación
                    const locationMatch = !selectedLocation || cellLocation.textContent.trim().toLowerCase() === selectedLocation;
                    // Filtro por búsqueda
                    const cells = row.querySelectorAll('td');
                    let found = false;
                    for (let i = 0; i < cells.length - 1; i++) {
                        if (cells[i].textContent.toLowerCase().includes(searchValue)) {
                            found = true;
                            break;
                        }
                    }
                    row.style.display = (categoriaMatch && locationMatch && found) ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>