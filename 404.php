<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Error 404 - Página no encontrada</title>
  <link rel="icon" type="image/svg+xml" href="img/gear-fill.svg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      color: #fff;
      background: linear-gradient(rgba(0, 0, 80, 0.85), rgba(0, 0, 60, 0.9)),
                  url('https://miro.medium.com/v2/resize:fit:1400/1*cRjevzZSKByeCrwjFmBrIg.jpeg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      animation: fadeIn 1s ease-in;
    }

    .error-container {
      max-width: 700px;
      padding: 30px;
      background-color: rgba(2, 44, 87, 0.85);
      border: 2px solid #FFD700;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
      animation: slideUp 0.7s ease-out forwards;
    }

    .error-container h1 {
      font-size: 4.5rem;
      color: #FFD700;
      margin-bottom: 20px;
    }

    .error-container h2 {
      font-size: 2rem;
      margin-bottom: 15px;
    }

    .error-container p {
      font-size: 1.1rem;
      margin-bottom: 30px;
      color: rgba(255, 255, 255, 0.85);
    }

    .btn-regresar {
      padding: 12px 30px;
      background-color: #FFD700;
      color: #001933;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    .btn-regresar:hover {
      background-color: #ffc400;
      transform: translateY(-3px);
    }

    footer {
      position: absolute;
      bottom: 10px;
      width: 100%;
      text-align: center;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.85rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

  <div class="error-container">
    <h1><i class="fas fa-exclamation-triangle"></i> 404</h1>
    <h2>Página no encontrada</h2>
    <p>Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
    <a href="dashboard_admin.php" class="btn-regresar"><i class="fas fa-home"></i> Ir al inicio</a>
  </div>

  <footer>&copy; <?= date('Y') ?> Sistema de Inventario. Todos los derechos reservados.</footer>

</body>
</html>
