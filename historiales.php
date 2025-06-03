<?php
include 'conexion.php';
$activos = [];
$result = $conn->query("SELECT a.id, a.nombre, c.nombre AS categoria FROM activos a INNER JOIN categorias c ON a.id_categoria = c.id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $activos[] = $row;
    }
}
function obtenerHistorial($conn, $activo_id) {
    $sql = "SELECT h.fecha_movimiento, h.tipo_movimiento, h.observaciones, 
                   so.nombre AS sitio_origen, sd.nombre AS sitio_destino, u.nombre_usuario
            FROM historial_activos h
            LEFT JOIN sitios so ON h.id_sitio_origen = so.id
            LEFT JOIN sitios sd ON h.id_sitio_destino = sd.id
            LEFT JOIN usuarios u ON h.id_usuario = u.id
            WHERE h.id_activo = ?
            ORDER BY h.fecha_movimiento DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $activo_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $historial = [];
    while ($row = $res->fetch_assoc()) {
        $historial[] = $row;
    }
    return $historial;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Historiales - Administración de Activos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .navbar img {
            height: 50px;
        }

        .navbar h1 {
            font-size: 1.5rem;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .container {
            flex-grow: 1;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Estilos para los paneles de acción */
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
            background-color: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 215, 0, 0.3);
        }

        .panel h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #001e3c;
            border-bottom: 2px solid #FFD700;
            padding-bottom: 10px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #001e3c;
            color: white;
            border: 2px solid #FFD700;
        }

        .btn-primary:hover {
            background-color: #003366;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .history-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
            border-left: 4px solid #FFD700;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-date {
            color: #6c757d;
            font-size: 0.9em;
            font-weight: bold;
        }

        .history-details {
            margin-top: 8px;
            color: #333;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin-right: 8px;
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
            background-color: rgba(0,0,0,0.7);
        }

        .modal-content {
            background-color: #fff;
            color: #333;
            margin: 10% auto;
            padding: 25px;
            border-radius: 12px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            border: 2px solid #FFD700;
        }

        .modal-title {
            margin-top: 0;
            color: #001e3c;
            border-bottom: 2px solid #FFD700;
            padding-bottom: 10px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 10px;
        }

        .success-message {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .error-message {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }

        #historialContenido {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            background-color: #f8f9fa;
        }

        footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .panel {
                min-width: 100%;
            }
            
            .navbar h1 {
                font-size: 1.1rem;
            }

            .navbar img {
                height: 40px;
            }

            .container {
                padding: 20px 10px;
            }

            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
        }
    </style>
</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="crud_libros.php">
                <i class='fas fa-book-open' style='font-size:24px'></i>
                <span class="d-none d-sm-inline">HISTORIAL DE ACTIVOS</span>
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
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <i class="fas fa-exchange-alt me-1"></i> Estado
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link active" href="historiales.php">
                            <i class="fas fa-users me-1"></i> Historiales
                        </a>
                    </li>-->
                    <li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <i class="fas fa-chart-bar me-1"></i> Registrar activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes graficos
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

    <div class="container">
        <div class="action-panels">
            <div class="panel">
                <h3><i class="fas fa-history"></i> Historial de Activos</h3>
                <div class="form-group">
                    <form method="get" id="formHistorial">
                        <label for="activo_historial"><i class="fas fa-search"></i> Seleccionar Activo:</label>
                        <select class="form-control" id="activo_historial" name="activo_historial" onchange="document.getElementById('formHistorial').submit()">
                            <option value="">-- Seleccionar activo --</option>
                            <?php foreach ($activos as $a): ?>
                                <option value="<?= htmlspecialchars($a['id']) ?>" <?= (isset($_GET['activo_historial']) && $_GET['activo_historial'] == $a['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre']) ?> (<?= htmlspecialchars($a['categoria']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div id="historialContenido">
                    <?php
                    if (!isset($_GET['activo_historial']) || !$_GET['activo_historial']) {
                        echo '<p style="text-align: center; color: #6c757d; padding: 20px;">
                            <i class="fas fa-info-circle"></i> Seleccione un activo para ver su historial.
                        </p>';
                    } else {
                        $historial = obtenerHistorial($conn, intval($_GET['activo_historial']));
                        if (empty($historial)) {
                            echo '<p style="text-align: center; color: #6c757d; padding: 20px;">
                                <i class="fas fa-exclamation-circle"></i> No hay historial disponible para este activo.
                            </p>';
                        } else {
                            foreach ($historial as $item) {
                                $badgeClass = 'badge-modify';
                                if ($item['tipo_movimiento'] == 'traslado') $badgeClass = 'badge-move';
                                elseif ($item['tipo_movimiento'] == 'asignación') $badgeClass = 'badge-assign';
                                elseif ($item['tipo_movimiento'] == 'baja') $badgeClass = 'badge-remove';
                                echo '<div class="history-item">
                                    <div class="history-date">
                                        <i class="fas fa-calendar-alt"></i> '.htmlspecialchars($item['fecha_movimiento']).'
                                    </div>
                                    <div class="history-details">
                                        <span class="badge-status '.$badgeClass.'">'.strtoupper(htmlspecialchars($item['tipo_movimiento'])).'</span>
                                        '.htmlspecialchars($item['observaciones']).'<br>
                                        <small><i class="fas fa-map-marker-alt"></i> ';
                                if ($item['sitio_origen']) echo htmlspecialchars($item['sitio_origen']).' → ';
                                echo htmlspecialchars($item['sitio_destino']);
                                echo '</small><br>';
                                echo '<small><i class="fas fa-user"></i> '.htmlspecialchars($item['nombre_usuario']).'</small>';
                                echo '</div></div>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>© 2025 Luz a las Naciones</p>
    </footer>
</body>
</html>