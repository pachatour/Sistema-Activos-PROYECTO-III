<?php
require_once 'conexion.php';

// Agregar estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $nivel = $_POST['nivel'];
    $curso = $_POST['curso'];
    $paralelo = $_POST['paralelo'];
    $conn->query("INSERT INTO estudiantes (nombre, apellido, nivel, curso, paralelo) VALUES ('$nombre', '$apellido', '$nivel', '$curso', '$paralelo')");
    header("Location: crud_estudiantes.php");
    exit();
}

// Editar estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id_estudiante']);
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $nivel = $_POST['nivel'];
    $curso = $_POST['curso'];
    $paralelo = $_POST['paralelo'];
    $conn->query("UPDATE estudiantes SET nombre='$nombre', apellido='$apellido', nivel='$nivel', curso='$curso', paralelo='$paralelo' WHERE id_estudiante=$id");
    header("Location: crud_estudiantes.php");
    exit();
}

// Eliminar estudiante
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM estudiantes WHERE id_estudiante=$id");
    header("Location: crud_estudiantes.php");
    exit();
}

// Obtener estudiantes
$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY id_estudiante DESC");

// Para editar
$editando = false;
$estudiante_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM estudiantes WHERE id_estudiante=$id_edit");
    $estudiante_edit = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Estudiantes</title>
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
        .navbar h1 {
            font-size: 1.5rem;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }
        .container {
            max-width: 900px;
            margin: 40px auto 0 auto;
            background: rgba(0, 30, 60, 0.93);
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
            padding: 32px 24px;
        }
        .crud-title {
            color: #FFD700;
            margin-bottom: 20px;
            text-align: center;
            font-size: 2rem;
        }
        .crud-form {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 30px;
            justify-content: center;
        }
        .crud-form input, .crud-form select {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #FFD700;
            background: rgba(255,255,255,0.1);
            color: #fff;
            min-width: 160px;
        }
        .crud-form button {
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
        .crud-form button:hover {
            background-color: #ffe033;
            color: #003366;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255,255,255,0.97);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            color: #2c3e50;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        thead {
            background-color: #FFD700;
            color: #003366;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #ffe033;
        }
        .crud-actions a, .crud-actions button {
            margin-right: 8px;
            color: #3498db;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .crud-actions a.delete {
            color: #dc3545;
        }
        .crud-actions a.edit {
            color: #17a2b8;
        }
        @media (max-width: 700px) {
            .container {
                padding: 10px 2px;
            }
            th, td {
                padding: 8px 6px;
            }
            .crud-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1><i class="fas fa-users"></i> CRUD Estudiantes</h1>
        <a href="biblio_dashboard.php" style="color:#FFD700;text-decoration:none;font-weight:bold;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
    <div class="container">
        <div class="crud-title"><?= $editando ? 'Editar Estudiante' : 'Agregar Estudiante' ?></div>
        <form class="crud-form" method="post" action="crud_estudiantes.php">
            <?php if ($editando): ?>
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_estudiante" value="<?= $estudiante_edit['id_estudiante'] ?>">
            <?php else: ?>
                <input type="hidden" name="accion" value="agregar">
            <?php endif; ?>
            <input type="text" name="nombre" placeholder="Nombre" required value="<?= $editando ? htmlspecialchars($estudiante_edit['nombre']) : '' ?>">
            <input type="text" name="apellido" placeholder="Apellido" required value="<?= $editando ? htmlspecialchars($estudiante_edit['apellido']) : '' ?>">
            <select name="nivel" required>
                <option value="">Nivel</option>
                <option value="primario" <?= $editando && $estudiante_edit['nivel']=='primario' ? 'selected' : '' ?>>Primario</option>
                <option value="secundario" <?= $editando && $estudiante_edit['nivel']=='secundario' ? 'selected' : '' ?>>Secundario</option>
            </select>
            <select name="curso" required>
                <option value="">Curso</option>
                <option value="primero" <?= $editando && $estudiante_edit['curso']=='primero' ? 'selected' : '' ?>>Primero</option>
                <option value="segundo" <?= $editando && $estudiante_edit['curso']=='segundo' ? 'selected' : '' ?>>Segundo</option>
                <option value="tercero" <?= $editando && $estudiante_edit['curso']=='tercero' ? 'selected' : '' ?>>Tercero</option>
                <option value="cuarto" <?= $editando && $estudiante_edit['curso']=='cuarto' ? 'selected' : '' ?>>Cuarto</option>
                <option value="quinto" <?= $editando && $estudiante_edit['curso']=='quinto' ? 'selected' : '' ?>>Quinto</option>
                <option value="sexto" <?= $editando && $estudiante_edit['curso']=='sexto' ? 'selected' : '' ?>>Sexto</option>
            </select>
            <select name="paralelo" required>
                <option value="">Paralelo</option>
                <option value="A" <?= $editando && $estudiante_edit['paralelo']=='A' ? 'selected' : '' ?>>A</option>
                <option value="B" <?= $editando && $estudiante_edit['paralelo']=='B' ? 'selected' : '' ?>>B</option>
            </select>
            <button type="submit"><?= $editando ? 'Actualizar' : 'Agregar' ?></button>
            <?php if ($editando): ?>
                <a href="crud_estudiantes.php" style="color:#FFD700;font-weight:bold;text-decoration:none;margin-left:10px;">Cancelar</a>
            <?php endif; ?>
        </form>
        <div class="crud-title" style="margin-top:30px;font-size:1.3rem;">Lista de Estudiantes</div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nivel</th>
                    <th>Curso</th>
                    <th>Paralelo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $estudiantes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['apellido']) ?></td>
                    <td><?= ucfirst($row['nivel']) ?></td>
                    <td><?= ucfirst($row['curso']) ?></td>
                    <td><?= $row['paralelo'] ?></td>
                    <td class="crud-actions">
                        <a href="crud_estudiantes.php?editar=<?= $row['id_estudiante'] ?>" class="edit" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="crud_estudiantes.php?eliminar=<?= $row['id_estudiante'] ?>" class="delete" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este estudiante?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
