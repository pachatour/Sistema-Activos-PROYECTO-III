<?php
require_once 'conexion.php';

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
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
    <div class="container-fluid">
        <!-- Logo y título -->
        <a class="navbar-brand" href="prestamos.php">
            <i class="fas fa-hand-holding me-2"></i>
            <span class="d-none d-sm-inline">GESTIÓN DE PRESTAMOS</span>
        </a>
        <!-- Botón hamburguesa para móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Menú de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="biblio_dashboard.php">
                        <i class="fa-brands fa-wpforms"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="crud_libros.php">
                        <i class="fas fa-book me-1"></i> Libros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="crud_estudiantes.php">
                        <i class="fas fa-users me-1"></i> Crear Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="reporte_libros.php">
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

<!-- Tabla de Préstamos Activos -->
<div class="container mt-4">
    <h2 style="color: #FFD700; margin: 30px 0 20px 0;"><i class="fas fa-list"></i> Préstamos Activos</h2>
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notificación enviada al usuario por WhatsApp.');
        } else {
            alert('No se pudo enviar la notificación: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        alert('Error al enviar la notificación.');
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
</body>
</html>
<?php
$conn->close();
?>