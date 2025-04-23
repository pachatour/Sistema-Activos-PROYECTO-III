<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <style>
        :root {
            --base: #e0e5ec;
            --text: #2c3e50;
            --amarillo: #f1c40f;
            --azul: #3498db;
            --sombra-oscura: #a3b1c6;
            --sombra-clara: #ffffff;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--base);
            color: var(--text);
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: var(--base);
            box-shadow: inset 4px 4px 8px var(--sombra-oscura),
                        inset -4px -4px 8px var(--sombra-clara);
            border-bottom: 2px solid var(--azul);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--amarillo);
            text-shadow: 1px 1px 2px var(--sombra-oscura);
        }

        .nav-links a {
            margin-left: 25px;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 12px;
            color: var(--text);
            background-color: var(--base);
            box-shadow: 6px 6px 12px var(--sombra-oscura),
                        -6px -6px 12px var(--sombra-clara);
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background-color: var(--azul);
            color: #fff;
        }

        @media (max-width: 600px) {
            .main-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .nav-links {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .nav-links a {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Cambia el título según el rol -->
    <header class="main-header">
        <div class="logo">Blibliotecaria</div> <!-- O "Administrador", "Biblioteca" -->
        <nav class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="reportes.php">Reportes</a>
            <a href="perfil.php">Perfil</a>
        </nav>
    </header>

</body>
</html>