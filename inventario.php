<?php
require_once 'conexion.php';

// Verificar que la conexión se realizó correctamente
if (!isset($conn) || !$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
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
    <title>Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="css/inventario.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
    <div class="container-fluid">
        <a class="navbar-brand" href="inventario.php">
            <i class="fas fa-boxes"></i> 
            <span class="d-none d-sm-inline">INVENTARIO</span>
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
                    <td class="categoria"><?php echo htmlspecialchars($activo['categoria_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($activo['sitio_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($activo['cantidad']); ?></td>
                    <td>
                        <button class="action-btn edit-btn" data-id="<?php echo $activo['id']; ?>" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn history-btn" data-id="<?php echo $activo['id']; ?>" title="Historial">
                            <i class="fas fa-history"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para Editar -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(rgba(0, 0, 80, 0.95), rgba(0, 0, 60, 0.98)); border: 2px solid #FFD700; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.7);">
          <div class="modal-header" style="border-bottom: 1px solid #FFD700;">
            <h5 class="modal-title" id="editModalLabel" style="color: #FFD700; font-weight: bold;">
                <i class="fas fa-edit me-2"></i>Editar Activo
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body" id="editModalBody" style="color: #fff;">
            <div class="text-center">
                <div class="spinner-border text-warning" role="status"></div>
                <span class="ms-2">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para Historial -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: linear-gradient(rgba(0, 0, 80, 0.95), rgba(0, 0, 60, 0.98)); border: 2px solid #FFD700; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.7);">
          <div class="modal-header" style="border-bottom: 1px solid #FFD700;">
            <h5 class="modal-title" id="historyModalLabel" style="color: #FFD700; font-weight: bold;">
                <i class="fas fa-history me-2"></i>Historial del Activo
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body" id="historyModalBody" style="color: #fff;">
            <div class="text-center">
                <div class="spinner-border text-warning" role="status"></div>
                <span class="ms-2">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const categoria = this.textContent.trim();
                const rows = document.querySelectorAll('.inventory-table tbody tr');

                rows.forEach(row => {
                    const cellCategoria = row.querySelector('td.categoria');
                    if (!cellCategoria) return;

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
                    const cellCategoria = row.querySelector('td.categoria');

//                    const cellCategoria = row.querySelector('td:nth-child(5)');
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

        // Modal Editar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                // Busca la fila correspondiente
                const row = this.closest('tr');
                const descripcion = row.querySelector('td:nth-child(2)').textContent.trim();
                const cantidad = row.querySelector('td:nth-child(6)').textContent.trim();

                // Formulario de edición en el modal (solo descripción y cantidad)
                document.getElementById('editModalBody').innerHTML = `
                    <form id="editForm">
                        <input type="hidden" name="id" value="${id}">
                        <div class="mb-3">
                            <label class="form-label" style="color:#FFD700;">Descripción</label>
                            <textarea class="form-control" name="descripcion" required style="background:rgba(0,30,60,0.85);color:#fff;border:1px solid #FFD700;">${descripcion}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color:#FFD700;">Cantidad</label>
                            <input type="number" class="form-control" name="cantidad" value="${cantidad}" min="1" required style="background:rgba(0,30,60,0.85);color:#fff;border:1px solid #FFD700;">
                        </div>
                        <button type="submit" class="btn btn-warning" style="color:#00264d;font-weight:bold;">Guardar Cambios</button>
                    </form>
                `;

                // Manejar el envío del formulario (solo frontend, sin guardar en BD)
                document.getElementById('editForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    modal.hide();
                    // Actualizar la fila en la tabla con los nuevos valores
                    row.querySelector('td:nth-child(2)').textContent = this.descripcion.value;
                    row.querySelector('td:nth-child(6)').textContent = this.cantidad.value;
                });

                modal.show();
            });
        });

        // Modal Historial
        document.querySelectorAll('.history-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const modal = new bootstrap.Modal(document.getElementById('historyModal'));
                document.getElementById('historyModalBody').innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-warning" role="status"></div>
                        <span class="ms-2">Cargando...</span>
                    </div>
                `;
                fetch('historial_activo.php?id=' + encodeURIComponent(id))
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('historyModalBody').innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById('historyModalBody').innerHTML = '<div class="alert alert-danger">Error al cargar el historial.</div>';
                    });
                modal.show();
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>