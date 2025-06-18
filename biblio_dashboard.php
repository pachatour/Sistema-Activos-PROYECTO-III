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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
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
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
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

        .nav-links a {
            margin-left: 25px;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
            color: #003366;
            background: #FFD700;
            transition: background 0.3s, color 0.3s, transform 0.3s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.1);
        }

        .nav-links a:hover {
            background: #3498db;
            color: #fff;
            transform: scale(1.05);
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: auto;
            gap: 30px;
            justify-items: center;
            align-items: center;
            padding: 40px 10px;
            min-height: 60vh;
        }

        .dashboard-card {
            width: 320px;
            height: 200px;
            background-color: rgba(0, 30, 60, 0.95);
            border-radius: 18px;
            border: 3px solid #FFD700;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
            text-align: center;
            transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }

        .dashboard-card i {
            font-size: 4rem;
            margin-bottom: 12px;
            color: #FFD700;
        }

        .dashboard-card h3 {
            margin: 0 0 8px 0;
            font-size: 1.5rem;
            color: #FFD700;
        }

        .dashboard-card p {
            color: #fff;
            font-size: 1rem;
        }

        .dashboard-card:hover {
            background-color: #00264d;
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
        }

        @media (max-width: 900px) {
            .dashboard-container {
                grid-template-columns: 1fr;
                grid-template-rows: repeat(4, 1fr);
                gap: 20px;
                padding: 30px 5px;
            }
            .dashboard-card {
                width: 95vw;
                height: 180px;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="logo"><i class="fas fa-book"></i> BIBLIOTECA</div>
        <nav class="nav-links">
            <a href="logout.php"><i class="fas fa-home"></i> Salir</a>
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