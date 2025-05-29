<?php
require_once 'conexion.php';

// Obtener todos los libros
$libros = $conn->query("SELECT a.*, c.nombre as categoria, e.nombre as estado, s.nombre as sitio
    FROM activos a
    JOIN categorias c ON a.id_categoria = c.id
    JOIN estado_activos e ON a.id_estado = e.id
    JOIN sitios s ON a.id_sitio = s.id
    WHERE c.nombre = 'Libro'
    ORDER BY a.id DESC");

// Editar libro
$editando = false;
$libro_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $id_edit = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM activos WHERE id=$id_edit");
    $libro_edit = $res->fetch_assoc();
}

// Actualizar libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $codigoBarras = $_POST['codigoBarras'];
    $descripcion = $_POST['descripcion'];
    $cantidad = intval($_POST['cantidad']);
    $conn->query("UPDATE activos SET nombre='$nombre', codigoBarras='$codigoBarras', descripcion='$descripcion', cantidad=$cantidad WHERE id=$id");
    header("Location: crud_libros.php");
    exit();
}

// Eliminar libro
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM activos WHERE id=$id");
    header("Location: crud_libros.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Libros</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial', sans-serif; }
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
            max-width: 1000px;
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
        .crud-form input, .crud-form textarea {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #FFD700;
            background: rgba(255,255,255,0.1);
            color: #fff;
            min-width: 160px;
        }
        .crud-form textarea {
            min-width: 300px;
            min-height: 40px;
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
            .container { padding: 10px 2px; }
            th, td { padding: 8px 6px; }
            .crud-title { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1><i class="fas fa-book"></i> CRUD Libros</h1>
        <a href="biblio_dashboard.php" style="color:#FFD700;text-decoration:none;font-weight:bold;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
    <div class="container">
        <?php if ($editando && $libro_edit): ?>
        <div class="crud-title">Editar Libro</div>
        <form class="crud-form" method="post" action="crud_libros.php">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" value="<?= $libro_edit['id'] ?>">
            <input type="text" name="nombre" placeholder="Nombre" required value="<?= htmlspecialchars($libro_edit['nombre']) ?>">
            <input type="text" name="codigoBarras" placeholder="Código de Barras" required value="<?= htmlspecialchars($libro_edit['codigoBarras']) ?>">
            <input type="number" name="cantidad" placeholder="Cantidad" min="1" required value="<?= htmlspecialchars($libro_edit['cantidad']) ?>">
            <textarea name="descripcion" placeholder="Descripción"><?= htmlspecialchars($libro_edit['descripcion']) ?></textarea>
            <button type="submit">Actualizar</button>
            <a href="crud_libros.php" style="color:#FFD700;font-weight:bold;text-decoration:none;margin-left:10px;">Cancelar</a>
        </form>
        <?php endif; ?>
        <div class="crud-title" style="margin-top:30px;font-size:1.3rem;">Lista de Libros</div>
        <div style="text-align:right; margin-bottom:15px;">
            <input type="text" id="buscadorLibros" placeholder="Buscar libro..." style="padding:8px; border-radius:6px; border:1px solid #FFD700; width:220px; color:#003366;">
        </div>
        <table id="tablaLibros">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $libros->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['codigoBarras']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                    <td><?= htmlspecialchars($row['sitio']) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                    <td class="crud-actions">
                        <a href="crud_libros.php?editar=<?= $row['id'] ?>" class="edit" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="crud_libros.php?eliminar=<?= $row['id'] ?>" class="delete" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este libro?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
    // Buscador inteligente para la tabla de libros
    document.getElementById('buscadorLibros').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll('#tablaLibros tbody tr');
        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            fila.style.display = textoFila.includes(filtro) ? '' : 'none';
        });
    });
    </script>
</body>
</html>
