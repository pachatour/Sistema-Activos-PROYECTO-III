<?php
require_once 'conexion.php';
include 'verificar_sesion.php';
// Acción POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $tipo = $_POST['tipo'] ?? 'estudiante';
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $identificacion = $conn->real_escape_string($_POST['identificacion'] ?? '');
    $nivel = $conn->real_escape_string($_POST['nivel'] ?? '');
    $curso = $conn->real_escape_string($_POST['curso'] ?? '');
    $paralelo = $conn->real_escape_string($_POST['paralelo'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $telefono = $conn->real_escape_string($_POST['telefono'] ?? '');

    if ($_POST['accion'] === 'agregar') {
        $query = "INSERT INTO usuarios_biblioteca (nombre, apellido, identificacion, nivel, curso, paralelo, tipo, email, telefono)
                  VALUES ('$nombre', '$apellido', '$identificacion', '$nivel', '$curso', '$paralelo', '$tipo', '$email', '$telefono')";
        $conn->query($query);
    } elseif ($_POST['accion'] === 'editar') {
        $id = intval($_POST['id']);
        $query = "UPDATE usuarios_biblioteca SET 
                    nombre='$nombre',
                    apellido='$apellido',
                    identificacion='$identificacion',
                    nivel='$nivel',
                    curso='$curso',
                    paralelo='$paralelo',
                    tipo='$tipo',
                    email='$email',
                    telefono='$telefono'
                  WHERE id=$id";
        $conn->query($query);
    }

    header("Location: crud_estudiantes.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM usuarios_biblioteca WHERE id=$id");
    header("Location: crud_estudiantes.php");
    exit();
}

// Consultar usuarios
$usuarios = $conn->query("SELECT * FROM usuarios_biblioteca ORDER BY id DESC");

// Editar
$editando = false;
$usuario_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM usuarios_biblioteca WHERE id=$id_edit");
    $usuario_edit = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Biblioteca</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <span class="d-none d-sm-inline">ADMINISTRACIÓN DE USUARIOS</span>
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
                        <li><a class="dropdown-item" href="prestamos.php"><i class='fas fa-cubes'></i> &nbsp; Prestar </a></li>
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
<div class="container">
    <h2 class="text-center mb-4">
        <?= $editando ? 'Editar Usuario' : 'Crear Usuario de Biblioteca' ?>
    </h2>

    <form method="post" class="row g-3">
        <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'agregar' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $usuario_edit['id'] ?>">
        <?php endif; ?>

        <div class="col-md-6">
            <label class="form-label">Tipo de Usuario</label>
            <select name="tipo" id="tipo" class="form-select" required onchange="mostrarCamposPorTipo()">
                <option value="estudiante" <?= $editando && $usuario_edit['tipo'] === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
                <option value="docente" <?= (!$editando && !isset($usuario_edit['tipo'])) || ($editando && $usuario_edit['tipo'] === 'docente') ? 'selected' : '' ?>>Docente</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Identificación</label>
            <input type="text" name="identificacion" class="form-control" required value="<?= $editando ? $usuario_edit['identificacion'] : '' ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required value="<?= $editando ? $usuario_edit['nombre'] : '' ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" required value="<?= $editando ? $usuario_edit['apellido'] : '' ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" value="<?= $editando ? ($usuario_edit['email'] ?? '') : '' ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= $editando ? ($usuario_edit['telefono'] ?? '') : '' ?>">
        </div>

        <div id="estudiante-campos" class="<?= $editando && $usuario_edit['tipo'] !== 'estudiante' ? 'hidden' : '' ?>">
            <div class="col-md-4">
                <label class="form-label">Nivel</label>
                <select name="nivel" class="form-select">
                    <option value="">Seleccionar</option>
                    <option value="primario" <?= $editando && $usuario_edit['nivel'] === 'primario' ? 'selected' : '' ?>>Primario</option>
                    <option value="secundario" <?= $editando && $usuario_edit['nivel'] === 'secundario' ? 'selected' : '' ?>>Secundario</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Curso</label>
                <select name="curso" class="form-select">
                    <option value="">Seleccionar</option>
                    <option value="primero" <?= $editando && $usuario_edit['curso'] === 'primero' ? 'selected' : '' ?>>Primero</option>
                    <option value="segundo" <?= $editando && $usuario_edit['curso'] === 'segundo' ? 'selected' : '' ?>>Segundo</option>
                    <option value="tercero" <?= $editando && $usuario_edit['curso'] === 'tercero' ? 'selected' : '' ?>>Tercero</option>
                    <option value="cuarto" <?= $editando && $usuario_edit['curso'] === 'cuarto' ? 'selected' : '' ?>>Cuarto</option>
                    <option value="quinto" <?= $editando && $usuario_edit['curso'] === 'quinto' ? 'selected' : '' ?>>Quinto</option>
                    <option value="sexto" <?= $editando && $usuario_edit['curso'] === 'sexto' ? 'selected' : '' ?>>Sexto</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Paralelo</label>
                <select name="paralelo" class="form-select">
                    <option value="">Seleccionar</option>
                    <option value="A" <?= $editando && $usuario_edit['paralelo'] === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $editando && $usuario_edit['paralelo'] === 'B' ? 'selected' : '' ?>>B</option>
                </select>
            </div>
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-gold px-5"><?= $editando ? 'Actualizar' : 'Registrar' ?></button>
            <?php if ($editando): ?>
                <a href="crud_estudiantes.php" class="btn btn-secondary ms-2">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>

    <h3 class="mt-5 mb-3" style="color: #FFD700;">Listado de Usuarios</h3>
    <div class="table-responsive">
        <table class="table table-hover table-striped bg-white text-dark">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Identificación</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Curso</th>
                    <th>Paralelo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= $u['identificacion'] ?></td>
                    <td><?= $u['nombre'] . ' ' . $u['apellido'] ?></td>
                    <td><?= ucfirst($u['tipo']) ?></td>
                    <td><?= $u['email'] ?? '-' ?></td>
                    <td><?= $u['telefono'] ?? '-' ?></td>
                    <td><?= $u['curso'] ?? '-' ?></td>
                    <td><?= $u['paralelo'] ?? '-' ?></td>
                    <td>
                        <a href="?editar=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Deseas eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function mostrarCamposPorTipo() {
        const tipo = document.getElementById("tipo").value;
        const camposEstudiante = document.getElementById("estudiante-campos");
        camposEstudiante.classList.toggle("hidden", tipo !== "estudiante");
    }

    window.onload = mostrarCamposPorTipo;
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
