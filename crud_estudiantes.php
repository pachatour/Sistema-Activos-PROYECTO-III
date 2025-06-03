<?php
require_once 'conexion.php';

// Acción POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $tipo = $_POST['tipo'] ?? 'estudiante';
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $identificacion = $conn->real_escape_string($_POST['identificacion'] ?? '');
    $nivel = $conn->real_escape_string($_POST['nivel'] ?? '');
    $curso = $conn->real_escape_string($_POST['curso'] ?? '');
    $paralelo = $conn->real_escape_string($_POST['paralelo'] ?? '');

    if ($_POST['accion'] === 'agregar') {
        $query = "INSERT INTO usuarios_biblioteca (nombre, apellido, identificacion, nivel, curso, paralelo, tipo)
                  VALUES ('$nombre', '$apellido', '$identificacion', '$nivel', '$curso', '$paralelo', '$tipo')";
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
                    tipo='$tipo'
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
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
    <div class="container-fluid">
        <!-- Logo y título -->
        <a class="navbar-brand" href="crud_estudiantes.php">
           <i class='fas fa-user-friends' style='font-size:24px'></i>
            <span class="d-none d-sm-inline">USUARIOS DE BIBLIOTECA</span>
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
                <li class="nav-item">
                    <a class="nav-link active" href="prestamos.php">
                        <i class="fas fa-exchange-alt me-1"></i> Préstamos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="crud_libros.php">
                        <i class="fas fa-book me-1"></i> Libros
                    </a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link active" href="crud_estudiantes.php">
                        <i class="fas fa-users me-1"></i> Crear Usuarios
                    </a>
                </li>-->
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
</body>
</html>
