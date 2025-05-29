<?php
require_once 'conexion.php';

// Crear tabla de préstamos si no existe
$conn->query("CREATE TABLE IF NOT EXISTS prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_activo INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo_usuario ENUM('estudiante', 'docente') NOT NULL,
    fecha_prestamo DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_devolucion_esperada DATETIME NOT NULL,
    fecha_devolucion_real DATETIME NULL,
    estado ENUM('activo', 'devuelto', 'atrasado') DEFAULT 'activo',
    FOREIGN KEY (id_activo) REFERENCES activos(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
)");

// Obtener préstamos activos
$prestamos_activos = $conn->query("SELECT p.*, a.nombre as activo_nombre, u.nombre_usuario 
                                  FROM prestamos p
                                  JOIN activos a ON p.id_activo = a.id
                                  JOIN usuarios u ON p.id_usuario = u.id
                                  WHERE p.estado = 'activo'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos iguales al sistema anterior */
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
            background-color: rgba(255, 255, 255, 0.1);
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
            background-color: rgba(0, 30, 60, 0.8);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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

        .status-activo {
            background-color: #28a745;
        }

        .status-devuelto {
            background-color: #17a2b8;
        }

        .status-atrasado {
            background-color: #dc3545;
        }

        .status-alerta {
            background-color: #ffc107;
            color: #000;
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

        .form-container {
            background-color: rgba(0, 30, 60, 0.9);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 2px solid #FFD700;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #FFD700;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            color: white;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #FFD700;
            color: #00264d;
        }

        .btn-primary:hover {
            background-color: #e6c200;
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
    <nav class="navbar">
        <h1><i class="fas fa-hand-holding"></i> Gestión de Préstamos</h1>
    </nav>

    <div class="dashboard">
        <div class="stats-container">
            <div class="stat-card">
                <h3><i class="fas fa-book"></i> Préstamos Activos</h3>
                <p><?php echo $prestamos_activos->num_rows; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-exclamation-triangle"></i> Préstamos Atrasados</h3>
                <p>0</p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-check-circle"></i> Préstamos Completados</h3>
                <p>0</p>
            </div>
        </div>

        <div class="form-container">
            <h2 style="color: #FFD700; margin-bottom: 20px;"><i class="fas fa-plus-circle"></i> Nuevo Préstamo</h2>
            <form action="registrar_prestamo.php" method="post">
                <div class="form-group">
                    <label for="activo">Activo (Buscar por nombre o código):</label>
                    <input type="text" id="activo" name="activo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <select id="usuario" name="usuario" class="form-control" required>
                        <option value="">Seleccionar usuario...</option>
                        <?php
                        $usuarios = $conn->query("SELECT id, nombre_usuario FROM usuarios");
                        while ($usuario = $usuarios->fetch_assoc()) {
                            echo "<option value='{$usuario['id']}'>{$usuario['nombre_usuario']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuario:</label>
                    <select id="tipo_usuario" name="tipo_usuario" class="form-control" required>
                        <option value="estudiante">Estudiante (7 días)</option>
                        <option value="docente">Docente (15 días)</option>
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
                    <th>Usuario</th>
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
                    $estado_clase = 'status-activo';
                    $estado_texto = 'Activo';
                    
                    if ($hoy > $fecha_devolucion) {
                        $estado_clase = 'status-atrasado';
                        $estado_texto = 'Atrasado';
                    } elseif ($dias_restantes <= 1) {
                        $estado_clase = 'status-alerta';
                        $estado_texto = 'Por vencer';
                    }
                ?>
                <tr>
                    <td><?php echo $prestamo['id']; ?></td>
                    <td><?php echo htmlspecialchars($prestamo['activo_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['nombre_usuario']); ?></td>
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
            alert('Notificación enviada al usuario sobre el préstamo #' + id);
            // En una implementación real, aquí iría una llamada AJAX
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.inventory-table tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;
                
                for (let i = 0; i < cells.length - 1; i++) {
                    if (cells[i].textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>