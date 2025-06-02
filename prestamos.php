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
                <!--<li class="nav-item">
                    <a class="nav-link active" href="prestamos.php">
                        <i class="fas fa-exchange-alt me-1"></i> Préstamos
                    </a>
                </li>-->
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
        // logout.js - Para confirmar antes de cerrar sesión
        document.querySelectorAll('.logout-link').forEach(link => {link.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
            window.location.href = this.href;
        }
    });
});
    </script>
    
</body>
</html>
<?php
$conn->close();
?>



















<!--
   
</head>
<body>
    

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

        <div class="form-container">
            <h2 style="color: #FFD700; margin-bottom: 20px;"><i class="fas fa-plus-circle"></i> Nuevo Préstamo</h2>
            <form action="registrar_prestamo.php" method="post">
                <div class="form-group">
                    <label for="activo">Activo:</label>
                    <select id="activo" name="id_activo" class="form-control" required>
                        <option value="">Seleccionar activo...</option>
                        <?php
                        $activos = $conn->query("SELECT id, nombre, codigoBarras FROM activos WHERE id NOT IN (SELECT id_activo FROM prestamos WHERE estado = 'prestado')");
                        while ($activo = $activos->fetch_assoc()) {
                            echo "<option value='{$activo['id']}'>{$activo['nombre']} ({$activo['codigoBarras']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuario:</label>
                    <select id="tipo_usuario" name="tipo_usuario" class="form-control" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="estudiante">Estudiante</option>
                        <option value="docente">Docente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <select id="usuario" name="id_usuario" class="form-control" required>
                        <option value="">Seleccionar usuario...</option>
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Préstamo</button>
            </form>
        </div>

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
                    $diferencia = $hoy->diff($fecha_devolucion);
                    $dias_restantes = $diferencia->days;
                    $estado_clase = 'status-prestado';
                    $estado_texto = 'Prestado';
                    
                    if ($hoy > $fecha_devolucion) {
                        $estado_clase = 'status-atrasado';
                        $estado_texto = 'Atrasado';
                    } elseif ($dias_restantes <= 1) {
                        $estado_clase = 'status-alerta';
                        $estado_texto = 'Por vencer';
                    }
                    
                    $nombre_completo = trim($prestamo['nombre_completo'] . ' ' . $prestamo['apellido_completo']);
                ?>
                <tr>
                    <td><?php echo $prestamo['id']; ?></td>
                    <td><?php echo htmlspecialchars($prestamo['activo_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['codigoBarras']); ?></td>
                    <td><?php echo htmlspecialchars($nombre_completo ?: $prestamo['usuario_nombre']); ?></td>
                    <td><?php echo ucfirst($prestamo['tipo_usuario']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prestamo['fecha_prestamo'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prestamo['fecha_devolucion_esperada'])); ?></td>
                    <td><span class="status-badge <?php echo $estado_clase; ?>"><?php echo $estado_texto; ?></span></td>
                    <td>
                        <button class="action-btn" title="Devolver" onclick="devolverPrestamo(<?php echo $prestamo['id']; ?>)">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="action-btn" title="Notificar" onclick="notificarUsuario(<?php echo $prestamo['id']; ?>)">
                            <i class="fas fa-bell"></i>
                        </button>
                        <button class="action-btn" title="Renovar" onclick="renovarPrestamo(<?php echo $prestamo['id']; ?>, '<?php echo $prestamo['tipo_usuario']; ?>')">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Cargar usuarios según tipo seleccionado
        document.getElementById('tipo_usuario').addEventListener('change', function() {
            const tipoUsuario = this.value;
            const usuarioSelect = document.getElementById('usuario');
            
            if (!tipoUsuario) {
                usuarioSelect.innerHTML = '<option value="">Seleccionar usuario...</option>';
                return;
            }
            
            fetch(`get_usuarios.php?tipo=${tipoUsuario}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccionar usuario...</option>';
                    data.forEach(usuario => {
                        options += `<option value="${usuario.id}">${usuario.nombre}</option>`;
                    });
                    usuarioSelect.innerHTML = options;
                });
        });

        
    </script>
</body>
</html>
<?php
$conn->close();
?>