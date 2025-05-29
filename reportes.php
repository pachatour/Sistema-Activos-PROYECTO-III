<?php
include 'conexion.php';


// Obtener filtros desde POST
$filtros = [
    'categoria' => $_POST['categoria'] ?? '',
    'estado' => $_POST['estado'] ?? '',
    'sitio' => $_POST['sitio'] ?? ''
];

// Función para obtener opciones de filtros
function getOptions($pdo, $tabla) {
    $stmt = $pdo->query("SELECT id, nombre FROM $tabla");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener activos con reportes
function getActivosFiltrados($pdo, $filtros) {
    $sql = "SELECT a.id, a.nombre, a.codigoBarras, c.nombre AS categoria, e.nombre AS estado,
                   s.nombre AS sitio, a.cantidad, r.descripcion AS reporte
            FROM activos a
            INNER JOIN categorias c ON a.id_categoria = c.id
            INNER JOIN estado_activos e ON a.id_estado = e.id
            INNER JOIN sitios s ON a.id_sitio = s.id
            LEFT JOIN (
                SELECT id_activo, MAX(fecha_generacion) AS ultima_fecha, descripcion
                FROM reportes GROUP BY id_activo
            ) r ON a.id = r.id_activo
            WHERE 1=1";

    $params = [];
    if (!empty($filtros['categoria'])) {
        $sql .= " AND a.id_categoria = ?";
        $params[] = $filtros['categoria'];
    }
    if (!empty($filtros['estado'])) {
        $sql .= " AND a.id_estado = ?";
        $params[] = $filtros['estado'];
    }
    if (!empty($filtros['sitio'])) {
        $sql .= " AND a.id_sitio = ?";
        $params[] = $filtros['sitio'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener datos
$categorias = getOptions($pdo, 'categorias');
$estados = getOptions($pdo, 'estado_activos');
$sitios = getOptions($pdo, 'sitios');
$activos = getActivosFiltrados($pdo, $filtros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Activos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
            background-color: rgba(0, 30, 60, 0.8);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.5);
        }

        label, select, th, td {
            color: #fff;
        }

        .btn-primary {
            background-color: #FFD700;
            color: #000;
            border: none;
        }

        .btn-primary:hover {
            background-color: #e6c200;
        }

        .btn-secondary, .btn-danger, .btn-success {
            border: none;
        }

        .table-bordered {
            border-color: #fff;
        }

        footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>

</head>
<body>
<div class="container mt-4">
    <h1>Reporte y Registro de Activos</h1>
    <form method="post" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Categoría</label>
            <select name="categoria" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $filtros['categoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($estados as $est): ?>
                    <option value="<?= $est['id'] ?>" <?= $est['id'] == $filtros['estado'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($est['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ubicación</label>
            <select name="sitio" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($sitios as $sit): ?>
                    <option value="<?= $sit['id'] ?>" <?= $sit['id'] == $filtros['sitio'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sit['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Filtrar</button>
            <a href="reportes.php" class="btn btn-secondary">Limpiar</a>
            <a href="export_pdf.php?<?= http_build_query($filtros) ?>" class="btn btn-danger">Exportar PDF</a>
            <a href="export_excel.php?<?= http_build_query($filtros) ?>" class="btn btn-success">Exportar Excel</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Cantidad</th>
                    <th>Último Reporte</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activos as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['codigoBarras']) ?></td>
                        <td><?= htmlspecialchars($a['categoria']) ?></td>
                        <td><?= htmlspecialchars($a['estado']) ?></td>
                        <td><?= htmlspecialchars($a['sitio']) ?></td>
                        <td><?= $a['cantidad'] ?></td>
                        <td><?= $a['reporte'] ? htmlspecialchars($a['reporte']) : 'Sin reportes' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($activos)): ?>
                    <tr><td colspan="8" class="text-center">No se encontraron activos con los filtros aplicados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
 