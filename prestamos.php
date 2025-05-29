<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];
$prestador = $conn->query("SELECT u.*, tu.nombre as tipo_usuario 
                          FROM usuarios u
                          JOIN tipo_usuarios tu ON u.id_tipo_usuario = tu.id
                          WHERE u.id = $usuario_id")->fetch_assoc();

// Obtener préstamos activos
$prestamos_activos = $conn->query("SELECT p.*, 
                                  a.nombre as activo_nombre, 
                                  a.codigoBarras,
                                  CONCAT(e.nombre, ' ', e.apellido) as estudiante_nombre,
                                  u.nombre_usuario as prestador_nombre
                                  FROM prestamos p
                                  JOIN activos a ON p.id_activo = a.id
                                  JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
                                  JOIN usuarios u ON p.id_prestador = u.id
                                  WHERE p.estado = 'prestado'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos de Libros</title>
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
            display: flex;
            flex-direction: column;
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
            padding: 40px 10px 30px 10px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-container {
            display: flex;
            gap: 30px;
            justify-content: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: rgba(0, 30, 60, 0.9);
            border-radius: 12px;
            border: 2px solid #FFD700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            padding: 30px 38px;
            align-items: center;
            gap: 10px;
            color: #FFD700;
            min-width: 180px;
            text-align: center;
        }
        .stat-card h3 {
            color: #FFD700;
            margin-bottom: 10px;
        }
        .stat-card p {
            color: #fff;
            font-size: 2rem;
            margin: 0;
        }

        #searchInput {
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #FFD700;
            outline: none;
            background: rgba(255,255,255,0.1);
            color: #fff;
            margin-left: 10px;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255,255,255,0.97);
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            color: #2c3e50;
        }

        .inventory-table th, .inventory-table td {
            padding: 12px 15px;
            text-align: left;
        }

        .inventory-table thead {
            background-color: #FFD700;
            color: #003366;
        }

        .inventory-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .inventory-table tbody tr:hover {
            background-color: #ffe033;
        }

        .form-container {
            background: rgba(0, 30, 60, 0.9);
            border-radius: 12px;
            border: 2px solid #FFD700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            padding: 30px 38px;
            margin-bottom: 30px;
            color: #fff;
        }

        .form-group label {
            color: #FFD700;
            font-weight: bold;
        }

        .form-control, select, input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px 0;
            border-radius: 6px;
            border: 1px solid #FFD700;
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .btn.btn-primary, button[type="submit"] {
            background-color: #FFD700;
            color: #003366;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn.btn-primary:hover, button[type="submit"]:hover {
            background-color: #ffe033;
            color: #003366;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .popup-content {
            background: linear-gradient(rgba(0, 30, 60, 0.95), rgba(0, 20, 50, 0.95));
            border: 2px solid #FFD700;
            border-radius: 10px;
            padding: 25px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            color: #fff;
        }
        
        .close-popup {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #FFD700;
            font-size: 1.8rem;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .close-popup:hover {
            transform: rotate(90deg);
        }
        
        /* Badge styles */
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .badge-prestado {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-devuelto {
            background-color: #28a745;
            color: white;
        }
        
        .badge-atrasado {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-alerta {
            background-color: #ffc107;
            color: #000;
        }

        @media (max-width: 700px) {
            .dashboard {
                padding: 10px 2px;
            }
            .stats-container {
                flex-direction: column;
                gap: 16px;
            }
            .form-container {
                padding: 18px 8px;
            }
            .inventory-table th, .inventory-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1><i class="fas fa-book"></i> Gestión de Préstamos</h1>
        <div style="color: #FFD700;">
            <i class="fas fa-user-shield"></i> <?= htmlspecialchars($prestador['nombre_usuario']) ?> 
            <small>(<?= htmlspecialchars($prestador['tipo_usuario']) ?>)</small>
        </div>
    </nav>

    <div class="dashboard">
        <div class="stats-container">
            <div class="stat-card">
                <h3><i class="fas fa-book-open"></i> Préstamos Activos</h3>
                <p><?= $prestamos_activos->num_rows ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-exclamation-triangle"></i> Atrasados</h3>
                <p>0</p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-check-circle"></i> Completados</h3>
                <p>0</p>
            </div>
        </div>

        <div class="form-container">
            <h2 style="color: #FFD700; margin-bottom: 20px;">
                <i class="fas fa-plus-circle"></i> Registrar Nuevo Préstamo
            </h2>
            <form id="formPrestamo" action="registrar_prestamo.php" method="post">
                <input type="hidden" name="prestador_id" value="<?= $usuario_id ?>">
                
                <div class="form-group">
                    <label for="libro"><i class="fas fa-book"></i> Libro:</label>
                    <select id="libro" name="libro" class="form-control" required>
                        <option value="">Seleccionar libro...</option>
                        <?php
                        $libros = $conn->query("SELECT a.id, a.nombre, a.codigoBarras 
                                              FROM activos a
                                              JOIN categorias c ON a.id_categoria = c.id
                                              WHERE c.nombre = 'Libro' AND a.id_estado != 6");
                        while ($libro = $libros->fetch_assoc()):
                        ?>
                        <option value="<?= $libro['id'] ?>">
                            <?= htmlspecialchars($libro['nombre']) ?> (<?= $libro['codigoBarras'] ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="estudiante"><i class="fas fa-user-graduate"></i> Estudiante:</label>
                    <div style="display: flex; gap: 10px;">
                        <select id="estudiante" name="estudiante" class="form-control" required>
                            <option value="">Seleccionar estudiante...</option>
                            <?php
                            $estudiantes = $conn->query("SELECT * FROM estudiantes WHERE activo = 1");
                            while ($estudiante = $estudiantes->fetch_assoc()):
                            ?>
                            <option value="<?= $estudiante['id_estudiante'] ?>">
                                <?= htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']) ?> 
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="button" class="btn btn-primary" onclick="mostrarPopupEstudiante()">
                            <i class="fas fa-plus"></i> Nuevo
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="dias_prestamo"><i class="fas fa-calendar-alt"></i> Duración:</label>
                    <select id="dias_prestamo" name="dias_prestamo" class="form-control" required>
                        <option value="7">7 días (Estudiante)</option>
                        <option value="15">15 días (Docente)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Préstamo
                </button>
            </form>
        </div>

        <!-- Popup para nuevo estudiante -->
        <div id="popupEstudiante" class="popup-overlay">
            <div class="popup-content">
                <span class="close-popup" onclick="cerrarPopupEstudiante()">&times;</span>
                <h2 style="color: #FFD700; margin-bottom: 20px;">
                    <i class="fas fa-user-plus"></i> Nuevo Estudiante
                </h2>
                <form id="formEstudiante">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <input type="text" id="apellido" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="codigo">Código de Estudiante:</label>
                        <input type="text" id="codigo" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email (opcional):</label>
                        <input type="email" id="email" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="nivel">Nivel:</label>
                        <select id="nivel" class="form-control" required>
                            <option value="primario">Primario</option>
                            <option value="secundario">Secundario</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select id="curso" class="form-control" required>
                            <option value="primero">Primero</option>
                            <option value="segundo">Segundo</option>
                            <option value="tercero">Tercero</option>
                            <option value="cuarto">Cuarto</option>
                            <option value="quinto">Quinto</option>
                            <option value="sexto">Sexto</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="paralelo">Paralelo:</label>
                        <select id="paralelo" class="form-control" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn btn-primary" onclick="registrarEstudiante()">
                        <i class="fas fa-save"></i> Guardar Estudiante
                    </button>
                </form>
            </div>
        </div>

        <!-- Listado de préstamos activos -->
        <h2 style="color: #FFD700; margin: 30px 0 20px 0;">
            <i class="fas fa-list"></i> Préstamos Activos
        </h2>
        
        <div class="search-filter" style="margin-bottom: 15px;">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar préstamo...">
        </div>
        
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libro</th>
                    <th>Código</th>
                    <th>Estudiante</th>
                    <th>Fecha Préstamo</th>
                    <th>Devolución</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($prestamo = $prestamos_activos->fetch_assoc()): 
                    $fecha_devolucion = new DateTime($prestamo['fecha_devolucion_esperada']);
                    $hoy = new DateTime();
                    $estado = $prestamo['estado'];
                    $badge_class = 'badge-prestado';
                    
                    if ($hoy > $fecha_devolucion && $estado == 'prestado') {
                        $badge_class = 'badge-atrasado';
                        $estado = 'Atrasado';
                    } elseif ($hoy->diff($fecha_devolucion)->days <= 1 && $estado == 'prestado') {
                        $badge_class = 'badge-alerta';
                        $estado = 'Por vencer';
                    } else {
                        $estado = ucfirst($estado);
                    }
                ?>
                <tr>
                    <td><?= $prestamo['id'] ?></td>
                    <td><?= htmlspecialchars($prestamo['activo_nombre']) ?></td>
                    <td><?= htmlspecialchars($prestamo['codigoBarras']) ?></td>
                    <td>
                        <?= htmlspecialchars($prestamo['estudiante_nombre']) ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($prestamo['fecha_devolucion_esperada'])) ?></td>
                    <td><span class="badge <?= $badge_class ?>"><?= $estado ?></span></td>
                    <td>
                        <button class="action-btn" title="Devolver" 
                                onclick="devolverPrestamo(<?= $prestamo['id'] ?>)">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="action-btn" title="Notificar" 
                                onclick="notificarUsuario(<?= $prestamo['id'] ?>)">
                            <i class="fas fa-bell"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar/ocultar popup estudiante
        function mostrarPopupEstudiante() {
            document.getElementById('popupEstudiante').style.display = 'flex';
        }
        
        function cerrarPopupEstudiante() {
            document.getElementById('popupEstudiante').style.display = 'none';
        }
        
        // Registrar nuevo estudiante via AJAX
        function registrarEstudiante() {
            const nombre = document.getElementById('nombre').value;
            const apellido = document.getElementById('apellido').value;
            const codigo = document.getElementById('codigo').value;
            const email = document.getElementById('email').value;
            const nivel = document.getElementById('nivel').value;
            const curso = document.getElementById('curso').value;
            const paralelo = document.getElementById('paralelo').value;
            
            if (!nombre || !apellido || !codigo) {
                alert('Por favor complete los campos obligatorios');
                return;
            }
            
            fetch('registrar_estudiante.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `nombre=${encodeURIComponent(nombre)}&apellido=${encodeURIComponent(apellido)}&codigo=${encodeURIComponent(codigo)}&email=${encodeURIComponent(email)}&nivel=${nivel}&curso=${curso}&paralelo=${paralelo}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar el nuevo estudiante al select
                    const select = document.getElementById('estudiante');
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.text = `${nombre} ${apellido} (${codigo})`;
                    select.add(option);
                    select.value = data.id;
                    
                    // Cerrar popup y limpiar formulario
                    cerrarPopupEstudiante();
                    document.getElementById('formEstudiante').reset();
                    
                    alert('Estudiante registrado con éxito');
                } else {
                    alert('Error: ' + (data.message || 'No se pudo registrar el estudiante'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al registrar estudiante');
            });
        }
        
        // Devolver préstamo
        function devolverPrestamo(id) {
            if (confirm('¿Está seguro de marcar este préstamo como devuelto?')) {
                window.location.href = 'devolver_prestamo.php?id=' + id;
            }
        }
        
        // Notificar usuario
        function notificarUsuario(id) {
            fetch('notificar_prestamo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_prestamo=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notificación enviada al estudiante');
                } else {
                    alert('Error al enviar notificación: ' + (data.message || ''));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar notificación');
            });
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