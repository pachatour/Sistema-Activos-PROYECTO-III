<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT u.*, t.parametro_edit_config 
            FROM usuarios u
            JOIN tipo_usuarios t ON u.id_tipo_usuario = t.id
            WHERE u.nombre_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['constrasena'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre_usuario'];
            $_SESSION['user_type'] = $user['id_tipo_usuario'];
            $_SESSION['user_permissions'] = $user['parametro_edit_config'];

            switch ($user['id_tipo_usuario']) {
                case 1: header("Location: admin_dashboard.php"); break;
                case 2: header("Location: manager_dashboard.php"); break;
                case 3: header("Location: biblio_dashboard.php"); break;
                case 4: header("Location: registro_activos.php"); break;
                default: header("Location: login.php");
            }
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Neumorphism Estilo Amarillo-Azul</title>
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
            padding: 15px 30px;
            background-color: var(--base);
            box-shadow: inset 4px 4px 8px var(--sombra-oscura),
                        inset -4px -4px 8px var(--sombra-clara);
        }

        .logo {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--azul);
        }

        .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
        }

        .login-form {
            background: var(--base);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 8px 8px 16px var(--sombra-oscura),
                        -8px -8px 16px var(--sombra-clara);
            width: 350px;
            text-align: center;
        }

        .login-form h2 {
            margin-bottom: 25px;
            color: var(--azul);
        }

        label {
            display: block;
            text-align: left;
            margin-top: 20px;
            margin-bottom: 5px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: var(--base);
            box-shadow: inset 3px 3px 6px var(--sombra-oscura),
                        inset -3px -3px 6px var(--sombra-clara);
            outline: none;
            font-size: 1rem;
            color: var(--text);
        }

        button {
            margin-top: 30px;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            background: linear-gradient(145deg, var(--amarillo), #ffe600);
            color: var(--text);
            box-shadow: 6px 6px 12px var(--sombra-oscura),
                        -6px -6px 12px var(--sombra-clara);
            transition: all 0.3s ease;
        }

        button:hover {
            background: var(--azul);
            color: #fff;
        }

        .error-message {
            background-color: #ffe6e6;
            color: #b30000;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 4px 4px 8px var(--sombra-oscura),
                        -4px -4px 8px var(--sombra-clara);
        }

        @media (max-width: 500px) {
            .login-form {
                width: 90%;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="logo">Inicio de sesión</div>
        <nav class="nav-links">
            <a href="index.html">Inicio</a>
            <a href="#">Ayuda</a>
        </nav>
    </header>

    <div class="login-container">
        <div class="login-form">
            <h2>INICIAR SESIÓN</h2>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Iniciar</button>
            </form>
        </div>
    </div>
</body>
</html>
