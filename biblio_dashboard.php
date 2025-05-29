<?php
// Si necesitas sesión, puedes descomentar la siguiente línea:
// session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Bibliotecaria</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

        .main-header {
            width: 100%;
            background-color: rgba(0, 30, 60, 0.95);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #FFD700;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo i {
            color: #FFD700;
        }
        .nav-links a {
            margin-left: 25px;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
            color: #003366;
            background: #FFD700;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.07);
        }
        .nav-links a:hover {
            background: #3498db;
            color: #fff;
        }
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 40px 20px;
            flex-grow: 1;
        }
        .dashboard-card {
            width: 220px;
            height: 120px;
            background-color: rgba(0, 30, 60, 0.9);
            border-radius: 12px;
            border: 2px solid #FFD700;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
            transition: transform 0.3s, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            cursor: pointer;
        }
        .dashboard-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #FFD700;
        }
        .dashboard-card:hover {
            background-color: #00264d;
            transform: translateY(-5px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.5);
        }
        .dashboard-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.3rem;
            color: #FFD700;
        }
        .dashboard-card p {
            color: #fff;
            font-size: 1rem;
        }
        @media (max-width: 600px) {
            .dashboard-card {
                width: 100%;
                height: auto;
                padding: 20px 0;
            }
            .main-header {
                flex-direction: column;
                gap: 10px;
                padding: 18px 10px;
            }
            .logo {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="logo"><i class="fas fa-book"></i> BIBLIOTECA</div>
        <nav class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
            <a href="perfil.php"><i class="fas fa-user"></i> Perfil</a>
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
                <p>Crea un estudiante para registrar su prestamo.</p>
            </a>
            <a href="reportes.php" class="dashboard-card">
                <i class="fas fa-file-alt"></i>
                <h3>Reportes</h3>
                <p>Visualiza reportes.</p>
            </a>
            <a href="crud_libros.php" class="dashboard-card">
                <i class="fas fa-user-cog"></i>
                <h3>Ver libros</h3>
                <p>Ver libros disponibles en la biblioteca.</p>
            </a>
        </div>
    </main>
</body>
</html>