<?php
require_once 'conexion.php';
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

// Configuración de préstamos
$config_prestamos = [
    'estudiante' => ['dias' => 7, 'renovaciones' => 1],
    'docente' => ['dias' => 15, 'renovaciones' => 2]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
     <meta charset="UTF-8">
    <title>Gestión de Préstamos - Biblioteca</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <span class="d-none d-sm-inline">PRÉSTAMO DE LIBROS</span>
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
                        <li><a class="dropdown-item" href="crud_estudiantes.php"><i class="fas fa-plus-circle"></i> &nbsp; Usuarios</a></li>
                        <li><a class="dropdown-item" href="reporte_libros.php"><i class="fas fa-chart-line"></i> &nbsp; Reportes</a></li>
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
        <div class="stats-container">
            <div class="stat-card">
                <h3><i class="fas fa-book"></i> Préstamos Activos</h3>
                <p><?php echo $prestamos_activos->num_rows; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-exclamation-triangle"></i> Préstamos Atrasados</h3>
                <p><?php 
                    $atrasados = $conn->query("SELECT COUNT(*) as total FROM prestamos WHERE estado = 'atrasado'")->fetch_assoc();
                    echo $atrasados['total'];
                ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-check-circle"></i> Préstamos Completados</h3>
                <p><?php 
                    $completados = $conn->query("SELECT COUNT(*) as total FROM prestamos WHERE estado = 'devuelto'")->fetch_assoc();
                    echo $completados['total'];
                ?></p>
            </div>
        </div>
        <!-- [Mismo stats-container que antes] -->

        <div class="form-container">
            <h2 style="color: #FFD700; margin-bottom: 20px;"><i class="fas fa-plus-circle"></i> Nuevo Préstamo</h2>
            <form action="registrar_prestamo.php" method="post">
                <div class="form-group">
                    <label for="activo">Activo:</label>
                    <select id="activo" name="id_activo" class="form-control" required>
                        <option value="">Seleccionar activo...</option>
                        <?php
                        $activos = $conn->query("SELECT id, nombre, codigoBarras FROM activos 
                                                WHERE id_categoria = 2 AND id NOT IN (SELECT id_activo FROM prestamos WHERE estado = 'prestado')");
                        while ($activo = $activos->fetch_assoc()) {
                            echo "<option value='{$activo['id']}'>{$activo['nombre']} ({$activo['codigoBarras']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <select id="usuario" name="id_usuario_biblioteca" class="form-control" required>
                        <option value="">Seleccionar usuario...</option>
                        <?php
                        // Cargar todos los usuarios activos de la biblioteca
                        $usuarios = $conn->query("SELECT id, nombre, apellido, tipo FROM usuarios_biblioteca WHERE activo = 1 ORDER BY nombre");
                        while ($usuario = $usuarios->fetch_assoc()) {
                            echo "<option value='{$usuario['id']}' data-tipo='{$usuario['tipo']}'>
                                    {$usuario['nombre']} {$usuario['apellido']} - " . ucfirst($usuario['tipo']) . "
                                </option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="tipo_usuario" name="tipo_usuario" value="">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Préstamo</button>
            </form>
        </div>

        <script>
        // Actualizar el campo oculto tipo_usuario cuando se selecciona un usuario
        document.getElementById('usuario').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                document.getElementById('tipo_usuario').value = selectedOption.dataset.tipo;
            } else {
                document.getElementById('tipo_usuario').value = '';
            }
        });
        </script>

        <!-- Tabla de préstamos activos eliminada -->
    </div>
    <script>
        // Cargar usuarios según tipo seleccionado
        document.getElementById('tipo_usuario').addEventListener('change', function() {
            const tipo = this.value;
            const usuarioSelect = document.getElementById('usuario');
            
            if (!tipo) {
                usuarioSelect.innerHTML = '<option value="">Seleccione tipo primero</option>';
                return;
            }
            
            fetch(`get_usuarios_biblioteca.php?tipo=${tipo}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccionar usuario...</option>';
                    data.forEach(usuario => {
                        options += `<option value="${usuario.id}">${usuario.nombre} ${usuario.apellido}</option>`;
                    });
                    usuarioSelect.innerHTML = options;
                });
        });

       function devolverPrestamo(id) {
            if (confirm('¿Está seguro de marcar este préstamo como devuelto?')) {
                window.location.href = 'devolver_prestamo.php?id=' + id;
            }
        }

        function notificarUsuario(id) {
            // Llama a notificar_prestamo.php por AJAX para enviar el correo
            fetch('notificar_prestamo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_prestamo=' + encodeURIComponent(id)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notificación enviada al usuario por correo.');
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