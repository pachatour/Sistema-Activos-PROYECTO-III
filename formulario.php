<?php
include 'conexion.php';
include 'verificar_sesion.php';
$estados = $conn->query("SELECT id, nombre FROM estado_activos ORDER BY id");
$sitios = $conn->query("SELECT id, nombre FROM sitios ORDER BY id");
$categorias = $conn->query("SELECT id, nombre FROM categorias ORDER BY id");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar campos no vacíos
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $cantidad = isset($_POST["cantidad"]) ? (int)$_POST["cantidad"] : null;

    if (empty($nombre) || empty($descripcion) || $cantidad === null) {
        echo "<script>Swal.fire({icon: 'error', title: 'Campos incompletos', text: 'Nombre, descripción y cantidad no pueden estar vacíos', showConfirmButton: false, timer: 3000});</script>";
        exit();
    }
    if ($cantidad < 1) {
        echo "<script>Swal.fire({icon: 'error', title: 'Cantidad inválida', text: 'La cantidad debe ser mayor a 0', showConfirmButton: false, timer: 3000});</script>";
        exit();
    }

    // Validaciones de estado, sitio y categoría...
    $id_estado = isset($_POST["id_estado"]) ? (int)$_POST["id_estado"] : null;
    $id_sitio = isset($_POST["id_sitio"]) ? (int)$_POST["id_sitio"] : null;
    $id_categoria = isset($_POST["id_categoria"]) ? (int)$_POST["id_categoria"] : null;

    // Preparar y ejecutar la inserción
    $stmt = $conn->prepare("INSERT INTO activos (nombre, descripcion, cantidad, id_estado, id_sitio, id_categoria) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiss", $nombre, $descripcion, $cantidad, $id_estado, $id_sitio, $id_categoria);

    if ($stmt->execute()) {
        echo "<script>Swal.fire({title: '¡Éxito!', text: 'Activo registrado correctamente', icon: 'success', showConfirmButton: false, timer: 2500});</script>";
    } else {
        echo "<script>Swal.fire({icon: 'error', title: 'Error', text: 'Error al registrar: " . addslashes($conn->error) . "', showConfirmButton: true});</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Activos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/formulario.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-boxes"></i> INVENTARIO
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Ir a
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="dashboard_admin.php"><i class="fas fa-home"></i> &nbsp; Inicio</a></li>
                            <li><a class="dropdown-item" href="inventario.php"><i class="fas fa-boxes"></i> &nbsp; Inventario</a></li>
                            <li><a class="dropdown-item" href="estado_activos.php"><i class="fas fa-chart-line"></i> &nbsp; Estado Activos</a></li>
                            <li><a class="dropdown-item" href="historiales.php"><i class="fas fa-history"></i> &nbsp; Historiales</a></li>
                            <li><a class="dropdown-item" href="reportes.php"><i class="fas fa-file-alt"></i> &nbsp; Reportes</a></li>
                            <li><a class="dropdown-item" href="reporte_graficos.php"><i class="fas fa-chart-pie"></i> &nbsp; Reportes Gráficos</a></li>
                            <li><a class="dropdown-item" href="regresion.php"><i class="fas fa-project-diagram"></i> &nbsp; Regresión</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp; Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <div class="form-container" style="padding: 20px;">
        <form method="POST" id="formularioActivo">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del activo:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <input type="text" name="descripcion" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad:</label>
                <input type="number" name="cantidad" class="form-control" min="1" required>
            </div>

            <div class="mb-3">
                <label for="id_estado" class="form-label">Estado:</label>
                <select name="id_estado" class="form-select" required>
                    <option value="">Seleccione un estado</option>
                    <?php while($e = $estados->fetch_assoc()): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_sitio" class="form-label">Sitio:</label>
                <select name="id_sitio" class="form-select" required>
                    <option value="">Seleccione un sitio</option>
                    <?php while($s = $sitios->fetch_assoc()): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoría:</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">Seleccione una categoría</option>
                    <?php while($c = $categorias->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Activo</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>