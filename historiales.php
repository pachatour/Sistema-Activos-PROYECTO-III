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

// AJAX endpoint para historial dinámico
if (isset($_GET['ajax_historial']) && isset($_GET['activo_id'])) {
    header('Content-Type: application/json');
    $historial = obtenerHistorial($conn, intval($_GET['activo_id']));
    echo json_encode($historial);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Historiales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/historiales.css">
</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.html">
                <i class='fas fa-book-open' style='font-size:24px'></i>
                <span class="d-none d-sm-inline">HISTORIAL DE ACTIVOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-steps" viewBox="0 0 16 16">
                            <path d="M.5 0a.5.5 0 0 1 .5.5v15a.5.5 0 0 1-1 0V.5A.5.5 0 0 1 .5 0M2 1.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5z"/>
                            </svg> Estado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                            </svg> Registrar activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                            <i class='fas fa-chart-pie'></i> Reportes graficos
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
                    <form method="get" id="formHistorial" autocomplete="off">
                        <label for="activo_historial"><i class="fas fa-search"></i> Seleccionar Activo:</label>
                        <select class="form-control" id="activo_historial" name="activo_historial">
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
                                $icon = '<i class="fas fa-edit history-icon"></i>';
                                if ($item['tipo_movimiento'] == 'traslado') {
                                    $badgeClass = 'badge-move';
                                    $icon = '<i class="fas fa-exchange-alt history-icon"></i>';
                                }
                                elseif ($item['tipo_movimiento'] == 'asignación') {
                                    $badgeClass = 'badge-assign';
                                    $icon = '<i class="fas fa-user-plus history-icon"></i>';
                                }
                                elseif ($item['tipo_movimiento'] == 'baja') {
                                    $badgeClass = 'badge-remove';
                                    $icon = '<i class="fas fa-trash-alt history-icon"></i>';
                                }
                                echo '<div class="history-item">'
                                    .$icon.
                                    '<div class="history-content">'
                                    .'<div class="history-date">'
                                        .'<i class="fas fa-calendar-alt"></i> '.htmlspecialchars($item['fecha_movimiento']).
                                    '</div>'
                                    .'<div class="history-details">'
                                        .'<span class="badge-status '.$badgeClass.'">'.strtoupper(htmlspecialchars($item['tipo_movimiento'])).'</span> '
                                        .htmlspecialchars($item['observaciones']).'<br>'
                                        .'<small><i class="fas fa-map-marker-alt"></i> ';
                                if ($item['sitio_origen']) echo htmlspecialchars($item['sitio_origen']).' → ';
                                echo htmlspecialchars($item['sitio_destino']);
                                echo '</small><br>';
                                echo '<small><i class="fas fa-user"></i> '.htmlspecialchars($item['nombre_usuario']).'</small>';
                                echo '</div></div></div>';
                            }
                        }
                    }
                    ?>
                </div>
                <div id="historialLoader" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> Cargando historial...
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>© 2025 Luz a las Naciones</p>
    </footer>
    <script>
    // UX: Carga dinámica del historial con feedback visual
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('activo_historial');
        const historialDiv = document.getElementById('historialContenido');
        const loader = document.getElementById('historialLoader');
        select.addEventListener('change', function() {
            const activoId = select.value;
            if (!activoId) {
                historialDiv.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;"><i class="fas fa-info-circle"></i> Seleccione un activo para ver su historial.</p>';
                return;
            }
            loader.style.display = 'block';
            historialDiv.style.opacity = '0.5';
            fetch('?ajax_historial=1&activo_id=' + encodeURIComponent(activoId))
                .then(r => r.json())
                .then(historial => {
                    loader.style.display = 'none';
                    historialDiv.style.opacity = '1';
                    if (!historial.length) {
                        historialDiv.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;"><i class="fas fa-exclamation-circle"></i> No hay historial disponible para este activo.</p>';
                        return;
                    }
                    let html = '';
                    historial.forEach(item => {
                        let badgeClass = 'badge-modify', icon = '<i class="fas fa-edit history-icon"></i>';
                        if (item.tipo_movimiento === 'traslado') {
                            badgeClass = 'badge-move';
                            icon = '<i class="fas fa-exchange-alt history-icon"></i>';
                        } else if (item.tipo_movimiento === 'asignación') {
                            badgeClass = 'badge-assign';
                            icon = '<i class="fas fa-user-plus history-icon"></i>';
                        } else if (item.tipo_movimiento === 'baja') {
                            badgeClass = 'badge-remove';
                            icon = '<i class="fas fa-trash-alt history-icon"></i>';
                        }
                        html += `<div class="history-item">${icon}
                            <div class="history-content">
                                <div class="history-date"><i class="fas fa-calendar-alt"></i> ${item.fecha_movimiento}</div>
                                <div class="history-details">
                                    <span class="badge-status ${badgeClass}">${item.tipo_movimiento.toUpperCase()}</span>
                                    ${item.observaciones}<br>
                                    <small><i class="fas fa-map-marker-alt"></i> ${item.sitio_origen ? item.sitio_origen + ' → ' : ''}${item.sitio_destino}</small><br>
                                    <small><i class="fas fa-user"></i> ${item.nombre_usuario}</small>
                                </div>
                            </div>
                        </div>`;
                    });
                    historialDiv.innerHTML = html;
                    historialDiv.scrollTo({top: 0, behavior: 'smooth'});
                })
                .catch(() => {
                    loader.style.display = 'none';
                    historialDiv.style.opacity = '1';
                    historialDiv.innerHTML = '<p class="error-message"><i class="fas fa-exclamation-triangle"></i> Error al cargar el historial.</p>';
                });
        });
        // Si hay un activo seleccionado al cargar, dispara el evento para AJAX
        if (select.value) {
            select.dispatchEvent(new Event('change'));
        }
    });
    </script>
</body>
</html>