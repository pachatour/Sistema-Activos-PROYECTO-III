<?php
include 'conexion.php';
include 'verificar_sesion.php';
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">
    <link rel="stylesheet" href="css/historiales.css">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
       <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-boxes"></i> 
                <span class="d-none d-sm-inline">HISTORIALES</span>
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
                            <li><a class="dropdown-item" href="dashboard_admin.php"><i class="fas fa-home"></i> &nbsp; Inicio</a></li>
                            <li><a class="dropdown-item" href="formulario.php"><i class="fas fa-plus-circle"></i> &nbsp Formulario</a></li>
                            <li><a class="dropdown-item" href="estado_activos.php"><i class="fas fa-chart-line"></i> &nbsp Estado Activos</a></li>
                            <li><a class="dropdown-item" href="reportes.php"><i class="fas fa-file-alt"></i> &nbsp Reportes</a></li>
                            <li><a class="dropdown-item" href="reporte_graficos.php"><i class="fas fa-chart-pie"></i> &nbsp Reportes Gráficos</a></li>
                            <li><a class="dropdown-item" href="regresion.php"><i class="fas fa-project-diagram"></i> &nbsp Regresión</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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