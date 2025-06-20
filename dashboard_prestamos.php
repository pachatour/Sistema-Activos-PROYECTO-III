<?php
include 'conexion.php';
include 'verificar_sesion.php';
// Obtener préstamos activos
$prestamos_activos = $conn->query("
    SELECT p.*, 
           a.nombre as activo_nombre, 
           a.codigoBarras,
           ub.nombre as usuario_nombre,
           ub.apellido as usuario_apellido,
           ub.tipo as tipo_usuario
    FROM prestamos p
    JOIN activos a ON p.id_activo = a.id
    JOIN usuarios_biblioteca ub ON p.id_usuario_biblioteca = ub.id
    WHERE p.estado = 'prestado'
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos - Biblioteca</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stylesara.css">
    
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
<!-- Tabla de Préstamos Activos -->
<div class="container mt-4">
    <table class="inventory-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Activo</th>
                <th>Código</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Fecha Préstamo</th>
                <th>Devolución Esperada</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($prestamo = $prestamos_activos->fetch_assoc()): 
                $fecha_devolucion = new DateTime($prestamo['fecha_devolucion_esperada']);
                $hoy = new DateTime();
                $estado_clase = 'status-prestado';
                $estado_texto = 'Prestado';
                if ($hoy > $fecha_devolucion) {
                    $estado_clase = 'status-atrasado';
                    $estado_texto = 'Atrasado';
                } elseif ($hoy->diff($fecha_devolucion)->days <= 1) {
                    $estado_clase = 'status-alerta';
                    $estado_texto = 'Por vencer';
                }
            ?>
            <tr>
                <td><?= $prestamo['id'] ?></td>
                <td><?= htmlspecialchars($prestamo['activo_nombre']) ?></td>
                <td><?= htmlspecialchars($prestamo['codigoBarras']) ?></td>
                <td><?= htmlspecialchars($prestamo['usuario_nombre'].' '.$prestamo['usuario_apellido']) ?></td>
                <td><?= ucfirst($prestamo['tipo_usuario']) ?></td>
                <td><?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
                <td><?= date('d/m/Y', strtotime($prestamo['fecha_devolucion_esperada'])) ?></td>
                <td><span class="status-badge <?= $estado_clase ?>"><?= $estado_texto ?></span></td>
                <td>
                    <button class="action-btn" title="Devolver" onclick="devolverPrestamo(<?= $prestamo['id'] ?>)">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button class="action-btn" title="Notificar" onclick="notificarUsuario(<?= $prestamo['id'] ?>)">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="action-btn" title="Renovar" onclick="renovarPrestamo(<?= $prestamo['id'] ?>, '<?= $prestamo['tipo_usuario'] ?>')">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function devolverPrestamo(id) {
    if (confirm('¿Está seguro de marcar este préstamo como devuelto?')) {
        window.location.href = 'devolver_prestamo.php?id=' + id;
    }
}
function notificarUsuario(id) {
    fetch('notificar_prestamo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_prestamo=' + encodeURIComponent(id)
    })
    .then(response => {
        // Verifica si la respuesta es JSON antes de intentar parsear
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                alert('Respuesta inesperada del servidor:\n' + text);
                throw e;
            }
        });
    })
    .then(data => {
        if (data && data.success) {
            alert(data.message || 'Notificación enviada correctamente.');
        } else if (data && data.message) {
            alert('No se pudo enviar la notificación: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al enviar la notificación: ' + error);
    });
}
function renovarPrestamo(id, tipoUsuario) {
    if (confirm(`¿Desea renovar este préstamo por ${tipoUsuario === 'estudiante' ? '7' : '15'} días más?`)) {
        window.location.href = 'renovar_prestamo.php?id=' + id;
    }
}
// logout.js - Para confirmar antes de cerrar sesión
document.querySelectorAll('.logout-link').forEach(link => {link.addEventListener('click', function(e) {
    e.preventDefault();
    if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
        window.location.href = this.href;
    }
});});
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