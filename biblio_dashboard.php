<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Biblioteca</title>
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="stylesara.css">
</head>
<body>
    <header class="main-header">
        <div class="navbar-content">
            <div class="logo"><i class="fas fa-book"></i> <span style="font-weight:900;">Panel de Biblioteca</span></div>
            <button class="nav-toggle" aria-label="Abrir menú" onclick="toggleNav()"><i class="fas fa-bars"></i></button>
            <nav class="nav-links" id="navLinks">
                 <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="dashboard-container">
            <a href="prestamos.php" class="dashboard-card">
                <i class="fas fa-plus-square"></i>
                <h3>Registrar Préstamo</h3>
                <p>Registra los préstamos de los libros.</p>
            </a>
            <a href="crud_estudiantes.php" class="dashboard-card">
                <i class="fas fa-user-graduate"></i>
                <h3>Crear Usuario</h3>
                <p>Crea un usuario de biblioteca para registrar su prestamo.</p>
            </a>
            <a href="reporte_libros.php" class="dashboard-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Reportes</h3>
                <p>Visualiza reportes.</p>
            </a>
            <a href="crud_libros.php" class="dashboard-card">
                <i class="fas fa-book-open"></i>
                <h3>Ver libros</h3>
                <p>Ver libros disponibles en la biblioteca.</p>
            </a>
        </div>
        <footer>
            <span style="font-weight:600;">Sistema Biblioteca</span> &copy; <?= date('Y') ?> | <span style="color:#fff;">Panel Bibliotecaria</span>
        </footer>
    </main>
    <script>
        function toggleNav() {
            var nav = document.getElementById('navLinks');
            nav.classList.toggle('show');
        }
    </script>
</body>
</html>