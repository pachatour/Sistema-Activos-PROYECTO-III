<?php
require_once 'conexion.php';
include 'verificar_sesion.php';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="css/inventario.css">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
         <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-boxes"></i> 
                <span class="d-none d-sm-inline">INVENTARIO</span>
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
                            <li><a class="dropdown-item" href="reportes.php"><i class="fas fa-file-alt"></i> &nbsp Reportes</a></li>
                            <li><a class="dropdown-item" href="reporte_graficos.php"><i class="fas fa-chart-pie"></i> &nbsp Reportes Gráficos</a></li>
                            <li><a class="dropdown-item" href="regresion.php"><i class="fas fa-project-diagram"></i> &nbsp Regresión</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    

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
                const cellLocation = row.querySelector('td:nth-child(4)');
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
                const cellLocation = row.querySelector('td:nth-child(4)');
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
                    const cellLocation = row.querySelector('td:nth-child(4)');
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
                const cantidad = row.querySelector('td:nth-child(5)').textContent.trim();

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

                // Manejar el envío del formulario (guardar en BD vía AJAX)
                document.getElementById('editForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    fetch('actualizar_activo.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar la fila en la tabla con los nuevos valores
                            row.querySelector('td:nth-child(2)').textContent = formData.get('descripcion');
                            row.querySelector('td:nth-child(5)').textContent = formData.get('cantidad');
                            modal.hide();
                        } else {
                            alert('Error al guardar los cambios: ' + (data.message || ''));
                        }
                    })
                    .catch(() => {
                        alert('Error de conexión al guardar los cambios.');
                    });
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