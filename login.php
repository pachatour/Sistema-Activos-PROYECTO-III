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
        if ($password === $user['constrasena'])  {
            // Guardar sesión antes de mostrar el mensaje
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre_usuario'];
            $_SESSION['user_type'] = $user['id_tipo_usuario'];
            $_SESSION['user_permissions'] = $user['parametro_edit_config'];

            // Mostrar mensaje interactivo de bienvenida antes de redirigir
            echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <title>Bienvenido</title>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
                <style>
                    body {
                        background: linear-gradient(rgba(0, 0, 80, 0.85), rgba(0, 0, 60, 0.9)), url('https://miro.medium.com/v2/resize:fit:1400/1*cRjevzZSKByeCrwjFmBrIg.jpeg') no-repeat center center fixed;
                        background-size: cover;
                        min-height: 100vh;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        color: #FFD700;
                        font-family: Arial, sans-serif;
                    }
                    .welcome-box {
                        background: rgba(0,30,60,0.92);
                        border-radius: 18px;
                        padding: 50px 40px;
                        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
                        text-align: center;
                        border: 2px solid #FFD700;
                        animation: popin 1s cubic-bezier(.68,-0.55,.27,1.55);
                    }
                    @keyframes popin {
                        0% { transform: scale(0.7); opacity: 0; }
                        80% { transform: scale(1.1); }
                        100% { transform: scale(1); opacity: 1; }
                    }
                    .welcome-box i {
                        font-size: 3rem;
                        margin-bottom: 18px;
                        color: #FFD700;
                        animation: bounce 1.2s infinite alternate;
                    }
                    @keyframes bounce {
                        to { transform: translateY(-10px);}
                    }
                    .welcome-box h1 {
                        font-size: 2.2rem;
                        margin-bottom: 10px;
                        color: #FFD700;
                    }
                    .welcome-box p {
                        color: #fff;
                        font-size: 1.1rem;
                        margin-bottom: 0;
                    }
                </style>
            </head>
            <body>
                <div class='welcome-box'>
                    <i class='fas fa-smile-beam'></i>
                    <h1>¡Bienvenido, " . htmlspecialchars($user['nombre_usuario']) . "!</h1>
                    <p>Redirigiendo a tu panel...</p>
                </div>
                <script>
                    setTimeout(function() {";
                        switch ($user['id_tipo_usuario']) {
                            case 1: echo "window.location.href = 'dashboard_admin.html';"; break;
                            case 2: echo "window.location.href = 'manager_dashboard.php';"; break;
                            case 3: echo "window.location.href = 'formulario.php';"; break;
                            case 4: echo "window.location.href = 'biblio_dashboard.php';"; break;
                            default: echo "window.location.href = 'login.php';";
                        }
            echo "}, 1700);
                </script>
            </body>
            </html>";
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
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0, 0, 80, 0.85), rgba(0, 0, 60, 0.9)),
                        url('https://miro.medium.com/v2/resize:fit:1400/1*cRjevzZSKByeCrwjFmBrIg.jpeg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 20px;
        }

        .login-container {
            background-color: rgba(0, 30, 60, 0.85);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.15);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            height: 70px;
            margin-bottom: 15px;
        }

        .login-header h2 {
            font-size: 1.8rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: bold;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="password"] {
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        input:focus {
            border-color: white;
            outline: none;
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }

        .login-button {
            background-color: #FFD700;
            color: #003366;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            background-color: #ffe033;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .login-button:active {
            transform: translateY(1px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #FFD700;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #ffe033;
        }

        @media (min-width: 768px) {
            .login-header h2 {
                font-size: 2rem;
            }

            .login-header img {
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="https://cdn-icons-png.flaticon.com/512/1251/1251570.png" alt="Inicio de sesión">
            <h2>INICIAR SESIÓN</h2>
        </div>
        <?php if (isset($error_message)): ?>
            <div style="background:#dc3545;color:#fff;padding:12px 18px;border-radius:6px;margin-bottom:18px;text-align:center;font-weight:bold;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username"><i class="fas fa-user"></i> Usuario:</label>
            <input type="text" id="username" name="username" required placeholder="Ingrese su usuario">
            
            <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">

            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt"></i> ACCEDER
            </button>
        </form>
        <a href="index.html" class="back-link"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
    </div>
</body>
</html>
