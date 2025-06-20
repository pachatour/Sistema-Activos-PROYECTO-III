<?php
require_once 'conexion.php';
include 'verificar_sesion.php';
// Obtener todos los libros
$libros = $conn->query("SELECT a.*, c.nombre as categoria, e.nombre as estado, s.nombre as sitio
    FROM activos a
    JOIN categorias c ON a.id_categoria = c.id
    JOIN estado_activos e ON a.id_estado = e.id
    JOIN sitios s ON a.id_sitio = s.id
    WHERE c.nombre = 'Libro'
    ORDER BY a.id DESC");

// Editar libro
$editando = false;
$libro_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM activos WHERE id=$id_edit");
    $libro_edit = $res->fetch_assoc();
}

// Actualizar libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $codigoBarras = $_POST['codigoBarras'];
    $descripcion = $_POST['descripcion'];
    $cantidad = intval($_POST['cantidad']);
    $conn->query("UPDATE activos SET nombre='$nombre', codigoBarras='$codigoBarras', descripcion='$descripcion', cantidad=$cantidad WHERE id=$id");
    header("Location: crud_libros.php");
    exit();
}

// Eliminar libro
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM activos WHERE id=$id");
    header("Location: crud_libros.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Libros</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stylesara.css">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
    <div class="container-fluid">
        <a class="navbar-brand" href="biblio_dashboard.php">
            <i class="fas fa-boxes"></i>
            <span class="d-none d-sm-inline">ADMINISTRACIÓN LIBROS</span>
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
                        <li><a class="dropdown-item" href="reporte_libros.php"><i class="fas fa-chart-line"></i> &nbsp; Reportes</a></li>
                        <li><a class="dropdown-item" href="dashboard_prestamos.php"><i class="fas fa-history"></i> &nbsp; Prestamos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp; Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container">
        <?php if ($editando && $libro_edit): ?>
        <div class="crud-title">Editar Libro</div>
        <form class="crud-form" method="post" action="crud_libros.php">
            <input type="hidden" name="accion" value="editar">
            <input type="text" name="nombre" placeholder="Nombre" required value="<?= htmlspecialchars($libro_edit['nombre']) ?>">
            <input type="text" name="codigoBarras" placeholder="Código de Barras" required value="<?= htmlspecialchars($libro_edit['codigoBarras']) ?>">
            <input type="number" name="cantidad" placeholder="Cantidad" min="1" required value="<?= htmlspecialchars($libro_edit['cantidad']) ?>">
            <textarea name="descripcion" placeholder="Descripción"><?= htmlspecialchars($libro_edit['descripcion']) ?></textarea>
            <button type="submit">Actualizar</button>
            <a href="crud_libros.php" style="color:#FFD700;font-weight:bold;text-decoration:none;margin-left:10px;">Cancelar</a>
        </form>
        <?php endif; ?>
        <div style="text-align:right; margin-bottom:15px;">
            <input type="text" id="buscadorLibros" placeholder="Buscar libro..." style="padding:8px; border-radius:6px; border:1px solid #FFD700; width:220px; color:#003366;">
        </div>
        <table id="tablaLibros">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $libros->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['codigoBarras']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                    <td><?= htmlspecialchars($row['sitio']) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                    <td class="crud-actions">
                        <a href="crud_libros.php?editar=<?= $row['id'] ?>" class="edit" title="Editar"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
    // Buscador inteligente para la tabla de libros
    document.getElementById('buscadorLibros').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll('#tablaLibros tbody tr');
        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            fila.style.display = textoFila.includes(filtro) ? '' : 'none';
        });
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