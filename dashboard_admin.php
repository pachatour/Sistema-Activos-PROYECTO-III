<?php
require 'conexion.php';
include 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Dashboard administrador</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">

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
    }

    .navbar {
      background-color: rgba(0, 30, 60, 0.95);
      box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    }

    .navbar-brand {
      font-size: 1.8rem;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .menu-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 60px;
      padding: 50px 20px;
      flex-grow: 1;
      animation: fadeIn 1s ease-in;
    }

    .menu-item {
      width: 220px;
      height: 130px;
      background-color: rgba(2, 44, 87, 0.95);
      border-radius: 14px;
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
      transition: transform 0.4s ease, background-color 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
      opacity: 0;
      animation: slideUp 0.6s ease forwards;
    }

    .menu-item:nth-child(1) { animation-delay: 0.1s; }
    .menu-item:nth-child(2) { animation-delay: 0.2s; }
    .menu-item:nth-child(3) { animation-delay: 0.3s; }
    .menu-item:nth-child(4) { animation-delay: 0.4s; }
    .menu-item:nth-child(5) { animation-delay: 0.5s; }
    .menu-item:nth-child(6) { animation-delay: 0.6s; }
    .menu-item:nth-child(7) { animation-delay: 0.7s; }
    .menu-item:nth-child(8) { animation-delay: 0.8s; }

    .menu-item i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #FFD700;
    }

    .menu-item:hover {
      background-color: #00264d;
      transform: translateY(-8px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
    }

    footer {
      text-align: center;
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      padding: 15px 10px;
      background-color: rgba(0, 0, 40, 0.8);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
      bottom: 0;
      width: 100%;
    }

    @media (max-width: 600px) {
      .menu-item {
        width: 100%;
        padding: 20px 0;
      }

      .navbar-brand {
        font-size: 1.1rem;
      }
    }

    /* Animaciones */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

      <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard_admin.php">
        <i class="fas fa-hand-holding"></i> INICIO DASHBOARD
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span><i class="fas fa-bars"></i></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
              <i class="fas fa-sign-out-alt me-1"></i><b>Cerrar Sesión</b>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="menu-container">
    <a href="inventario.php" class="menu-item"><i class="fas fa-boxes"></i>Inventario</a>
    <a href="formulario.php" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#FFD700" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
      </svg>Registrar</a>
    <a href="estado_activos.php" class="menu-item"><i class="fas fa-pen"></i>Editar Estado</a>
    <a href="historiales.php" class="menu-item"><i class="fas fa-history"></i>Historiales</a>
    <a href="reportes.php" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#FFD700" class="bi bi-clipboard-data" viewBox="0 0 16 16">
      <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0z"/>
      <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
      <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
    </svg>Reportes</a>
    <a href="reporte_graficos.php" class="menu-item"><i class="fas fa-chart-pie"></i>Reportes gráficos</a>
    <a href="regresion.php" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#FFD700" class="bi bi-file-earmark-spreadsheet-fill" viewBox="0 0 16 16">
      <path d="M6 12v-2h3v2z"/>
      <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M3 9h10v1h-3v2h3v1h-3v2H9v-2H6v2H5v-2H3v-1h2v-2H3z"/>
      </svg>Predicción con Regresión</a>
    <a href="biblio_dashboard.php" class="menu-item"><i class="fas fa-book"></i>Biblioteca</a>
  </div>

  <footer>
    <p>© 2025 Unidad Educativa Luz a las Naciones</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
