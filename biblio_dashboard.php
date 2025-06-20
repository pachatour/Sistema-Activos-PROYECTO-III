<?php
//session_start();
include 'conexion.php';
include 'verificar_sesion.php';
// DEBUG: Mostrar el tipo de usuario (eliminar en producción)
if (isset($_SESSION['user_type'])) {
    echo "<!-- user_type: " . htmlspecialchars($_SESSION['user_type']) . " -->";
}

// Verifica si el usuario está logueado y tiene el tipo de usuario Administrador (id=1)
$esAdministrador = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 1;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Bibliotecaria</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="css/biblio_dash.css">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
    <header class="main-header">
        <div class="logo"><i class="fas fa-book"></i> BIBLIOTECA</div>
        <nav class="nav-links">
            <?php if ($esAdministrador): ?>
                <a href="dashboard_admin.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </header>
    <main>
        <div class="dashboard-container">
            <a href="prestamos.php" class="dashboard-card">
                <i class="fas fa-plus-square"></i>
                <h3>Registrar Préstamo</h3>
                <p>Registra los préstamos de los libros.</p>
            </a>
            <a href="crud_estudiantes.php" class="dashboard-card">
                <i class="fas fa-search"></i>
                <h3>Crear Estudiante</h3>
                <p>Crea un estudiante para registrar su préstamo.</p>
            </a>
            <a href="reporte_libros.php" class="dashboard-card">
                <i class="fas fa-file-alt"></i>
                <h3>Reportes</h3>
                <p>Visualiza reportes.</p>
            </a>
            <a href="crud_libros.php" class="dashboard-card">
                <i class="fas fa-user-cog"></i>
                <h3>Ver Libros</h3>
                <p>Ver libros disponibles en la biblioteca.</p>
            </a>
            <a href="dashboard_prestamos.php" class="dashboard-card">
                <i class='fas fa-eye'></i>
                <h3>Préstamos Activos</h3>
                <p>Ver los préstamos realizados.</p>
            </a>
        </div>
    </main>
</body>
</html>