<?php
$conexion = new mysqli("localhost", "root", "", "sistema_activos");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Total de activos
$res_activos = $conexion->query("SELECT COUNT(*) AS total FROM activos");
$total_activos = $res_activos->fetch_assoc()['total'];

// Total de activos operativos (id_estado = 1)
$res_operativos = $conexion->query("SELECT COUNT(*) AS total FROM activos WHERE id_estado = 1");
$activos_operativos = $res_operativos->fetch_assoc()['total'];

// Total de reportes
$res_reportes = $conexion->query("SELECT COUNT(*) AS total FROM reportes");
$total_reportes = $res_reportes->fetch_assoc()['total'];


$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Gestión de Activos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .dashboard {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            padding: 20px;
            margin: 0 10px;
            border-radius: 12px;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .card.azul { background-color: #007bff; }
        .card.blanco { background-color:rgb(22, 184, 95); }
        .card.amarillo {
            background-color: #ffc107;
            color: #000;
        }

        .content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .metrics, .events {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .metrics h3, .events h3 {
            margin-bottom: 15px;
        }

        .metrics ul, .events ul {
            list-style: none;
            padding: 0;
        }

        .metrics li, .events li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .badge {
            background-color: #007bff;
            padding: 5px 10px;
            border-radius: 20px;
            color: #fff;
            font-weight: bold;
        }

        .event {
            display: flex;
            align-items: center;
        }

        .icon {
            font-size: 24px;
            margin-right: 10px;
        }

        .assets-header {
            margin: 30px 0 10px 0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            background-color: #e0e0e0;
            transition: background-color 0.3s;
        }

        .filter-btn.active, .filter-btn:hover {
            background-color: #007bff;
            color: #fff;
        }

        .search-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #searchInput {
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }

        .filter-advanced {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            background-color: #6c757d;
            color: #fff;
            cursor: pointer;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .styled-table th, .styled-table td {
            padding: 12px 15px;
            text-align: left;
        }

        .styled-table thead {
            background-color: #007bff;
            color: #fff;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Estilos para los nuevos divs */
        .action-panels {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .panel {
            flex: 1;
            min-width: 500px;
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .panel h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .history-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-date {
            color: #6c757d;
            font-size: 0.9em;
        }

        .history-details {
            margin-top: 5px;
        }

        .badge-status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .badge-move {
            background-color: #17a2b8;
            color: white;
        }

        .badge-assign {
            background-color: #28a745;
            color: white;
        }

        .badge-modify {
            background-color: #ffc107;
            color: black;
        }

        .badge-remove {
            background-color: #dc3545;
            color: white;
        }

        /* Estilos para el modal de confirmación */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .modal-title {
            margin-top: 0;
            color: #333;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 10px;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .hidden {
            display: none;
        }

        #historialContenido {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="row">
            <!-- Total de Activos -->
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Activos Totales</h5>
                        <h2 class="card-text"><?php echo $total_activos; ?></h2>
                    </div>
                </div>
            </div>

            <!-- Activos Operativos -->
            <div class="col-md-4">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Activos Operativos</h5>
                        <h2 class="card-text"><?php echo $activos_operativos; ?></h2>
                    </div>
                </div>
            </div>

            <!-- Reportes Generados -->
            <div class="col-md-4">
                <div class="card text-dark bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Reportes Generados</h5>
                        <h2 class="card-text"><?php echo $total_reportes; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
            <div class="events">
                <h3>Últimos Reportes</h3>
                <ul>
                    <li>
                        <div class="event">
                            <div class="icon">📝</div>
                            <div class="info">
                                <strong>Proyector AULA 3</strong><br>
                                <small>2025-04-09 10:30</small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="event">
                            <div class="icon">📝</div>
                            <div class="info">
                                <strong>Impresora Oficina</strong><br>
                                <small>2025-04-08 14:10</small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="event">
                            <div class="icon">📝</div>
                            <div class="info">
                                <strong>Router Principal</strong><br>
                                <small>2025-04-08 09:55</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Nuevos paneles de acción -->
        <div class="action-panels">
            <!-- Panel para actualizar estados de activos -->
            <div class="panel">
                <h3>Actualizar Estado de Activo</h3>
                <form id="updateAssetForm" method="post" action="actualizar_estado.php">
                    <div class="form-group">
                        <label for="activo_id">Seleccionar Activo:</label>
                        <select class="form-control" id="activo_id" name="activo_id" required>
                            <option value="">-- Seleccionar activo --</option>
                            <?php
                            $conexion = new mysqli("localhost", "root", "", "sistema_activos");
                            if ($conexion->connect_error) {
                                die("Error de conexión: " . $conexion->connect_error);
                            }

                            $consulta = "SELECT a.id, a.nombre, c.nombre as categoria 
                                        FROM activos a 
                                        JOIN categorias c ON a.id_categoria = c.id 
                                        ORDER BY a.nombre";
                            $resultado = $conexion->query($consulta);

                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $fila['id'] . "'>" . htmlspecialchars($fila['nombre']) . " (" . htmlspecialchars($fila['categoria']) . ")</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nuevo_estado">Nuevo Estado:</label>
                        <select class="form-control" id="nuevo_estado" name="nuevo_estado" required>
                            <option value="">-- Seleccionar estado --</option>
                            <?php
                            $consulta = "SELECT id, nombre FROM estado_activos ORDER BY nombre";
                            $resultado = $conexion->query($consulta);

                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $fila['id'] . "'>" . htmlspecialchars($fila['nombre']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sitio_destino">Sitio Destino:</label>
                        <select class="form-control" id="sitio_destino" name="sitio_destino" required>
                            <option value="">-- Seleccionar sitio --</option>
                            <?php
                            $consulta = "SELECT id, nombre FROM sitios ORDER BY nombre";
                            $resultado = $conexion->query($consulta);

                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $fila['id'] . "'>" . htmlspecialchars($fila['nombre']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                    </div>
                </form>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activo_id'])) {
                    $activo_id = $_POST['activo_id'];
                    $nuevo_estado = $_POST['nuevo_estado'];
                    $sitio_destino = $_POST['sitio_destino'];
                    $observaciones = $_POST['observaciones'];
                    $usuario_id = 1; // Esto debería ser el ID del usuario logueado

                    // Obtener el sitio actual del activo
                    $consulta = "SELECT id_sitio FROM activos WHERE id = $activo_id";
                    $resultado = $conexion->query($consulta);
                    if ($fila = $resultado->fetch_assoc()) {
                        $sitio_origen = $fila['id_sitio'];
                    }

                    // Actualizar el estado del activo
                    $actualizacion = "UPDATE activos SET id_estado = $nuevo_estado, id_sitio = $sitio_destino WHERE id = $activo_id";
                    if ($conexion->query($actualizacion) === TRUE) {
                        // Registrar en el historial
                        $insercion = "INSERT INTO historial_activos (id_activo, id_sitio_origen, id_sitio_destino, id_usuario, tipo_movimiento, observaciones) 
                                    VALUES ($activo_id, $sitio_origen, $sitio_destino, $usuario_id, 'modificación', '$observaciones')";
                        if ($conexion->query($insercion) === TRUE) {
                            echo "<div style='color: green; margin-top: 10px;'>Estado actualizado correctamente y registrado en el historial.</div>";
                        } else {
                            echo "<div style='color: red; margin-top: 10px;'>Error al registrar en historial: " . $conexion->error . "</div>";
                        }
                    } else {
                        echo "<div style='color: red; margin-top: 10px;'>Error al actualizar estado: " . $conexion->error . "</div>";
                    }
                }
                ?>
            </div>

            <!-- Panel para ver historial de activos -->
            <div class="panel">
                <h3>Historial de Activos</h3>
                <div class="form-group">
                    <label for="activo_historial">Seleccionar Activo:</label>
                    <select class="form-control" id="activo_historial" name="activo_historial" onchange="cargarHistorial()">
                        <option value="">-- Seleccionar activo --</option>
                        <?php
                        $consulta = "SELECT a.id, a.nombre, c.nombre as categoria 
                                    FROM activos a 
                                    JOIN categorias c ON a.id_categoria = c.id 
                                    ORDER BY a.nombre";
                        $resultado = $conexion->query($consulta);

                        if ($resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                                echo "<option value='" . $fila['id'] . "'>" . htmlspecialchars($fila['nombre']) . " (" . htmlspecialchars($fila['categoria']) . ")</option>";
                            }
                        }
                        $conexion->close();
                        ?>
                    </select>
                </div>
                <div id="historialContenido">
                    <p>Seleccione un activo para ver su historial.</p>
                    <!-- Aquí se mostrará el historial mediante AJAX -->
                </div>
            </div>
        </div>

        <div class="assets-header">
            <div class="filter-buttons">
                <button class="filter-btn active">🔘 Todos</button>
                <button class="filter-btn">📁 Mis datos</button>
            </div>
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="🔍 Filtrar por palabra clave">
            </div>
        </div>

        <table class="styled-table" id="assetTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>ID Estado</th>
                    <th>ID Categoría</th>
                    <th>ID Sitio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conexion = new mysqli("localhost", "root", "", "sistema_activos");
                if ($conexion->connect_error) {
                    die("Error de conexión: " . $conexion->connect_error);
                }

                $consulta = "SELECT * FROM activos";
                $resultado = $conexion->query($consulta);

                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
                        echo "<td>" . $fila['id_estado'] . "</td>";
                        echo "<td>" . $fila['id_categoria'] . "</td>";
                        echo "<td>" . $fila['id_sitio'] . "</td>";
                        echo "<td>" . $fila['cantidad'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron activos.</td></tr>";
                }
                $conexion->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Confirmar Actualización</h3>
            <p>¿Está seguro que desea actualizar el estado del activo?</p>
            <div class="modal-buttons">
                <button id="cancelBtn" class="btn btn-secondary">Cancelar</button>
                <button id="confirmBtn" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        // Función para filtrar activos en la tabla
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#assetTable tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let found = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        found = true;
                    }
                });

                row.style.display = found ? '' : 'none';
            });
        });

        // Función para cargar el historial de un activo
        function cargarHistorial() {
            const activoId = document.getElementById('activo_historial').value;
            const historialDiv = document.getElementById('historialContenido');
            
            if (!activoId) {
                historialDiv.innerHTML = "<p>Seleccione un activo para ver su historial.</p>";
                return;
            }
            
            // Simulación de carga con AJAX (en un entorno real usaríamos XMLHttpRequest o fetch)
            historialDiv.innerHTML = "<p>Cargando historial...</p>";
            
            // En un entorno real, aquí se haría la petición AJAX
            // Por ahora, simularemos la respuesta
            setTimeout(() => {
                // Crear el archivo historial_activo.php para procesar esto
                let xhr = new XMLHttpRequest();
                xhr.open('GET', 'historial_activo.php?id=' + activoId, true);
                xhr.onload = function() {
                    if (this.status == 200) {
                        historialDiv.innerHTML = this.responseText;
                    } else {
                        historialDiv.innerHTML = "<p>Error al cargar el historial.</p>";
                    }
                };
                xhr.onerror = function() {
                    historialDiv.innerHTML = "<p>Error de conexión al cargar el historial.</p>";
                };
                xhr.send();
            }, 500);
        }

        // Función para mostrar el modal de confirmación
        document.getElementById('updateAssetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const modal = document.getElementById('confirmModal');
            modal.style.display = 'block';
        });

        // Función para cerrar el modal y cancelar
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').style.display = 'none';
        });

        // Función para confirmar la actualización
        document.getElementById('confirmBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').style.display = 'none';
            document.getElementById('updateAssetForm').submit();
        });
    </script>
</body>
</html>