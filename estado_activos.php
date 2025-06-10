<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Actualizar Estado</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .navbar img {
            height: 50px;
        }

        .navbar h1 {
            font-size: 1.5rem;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .container {
            flex-grow: 1;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Estilos para los paneles de acción */
        .action-panels {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .panel {
            flex: 1;
            min-width: 500px;
            background-color: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 215, 0, 0.3);
        }

        .panel h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #001e3c;
            border-bottom: 2px solid #FFD700;
            padding-bottom: 10px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #001e3c;
            color: white;
            border: 2px solid #FFD700;
        }

        .btn-primary:hover {
            background-color: #003366;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Estilos para el modal de confirmación */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }

        .modal-content {
            background-color: #fff;
            color: #333;
            margin: 10% auto;
            padding: 25px;
            border-radius: 12px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            border: 2px solid #FFD700;
        }

        .modal-title {
            margin-top: 0;
            color: #001e3c;
            border-bottom: 2px solid #FFD700;
            padding-bottom: 10px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 10px;
        }

        .success-message {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .error-message {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }

        footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        a.hover {
            color: #FFD700;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .panel {
                min-width: 100%;
            }
            
            .navbar h1 {
                font-size: 1.1rem;
            }

            .navbar img {
                height: 40px;
            }

            .container {
                padding: 20px 10px;
            }

            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="estado_activos.php">
                <i class='fas fa-book-open' style='font-size:24px'></i>
                <span class="d-none d-sm-inline">ACTUALIZAR ESTADO DE ACTIVOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                      <li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <i class='fas fa-home' ></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <i class="fas fa-exchange-alt me-1"></i> Estado
                        </a>
                    </li>-->
                    <li class="nav-item">
                        <a class="nav-link active" href="historiales.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                            </svg> Historiales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                            </svg> Registrar activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                            <i class='fas fa-chart-pie'></i> Reportes graficos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reportes.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger logout-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i><b> Cerrar Sesión</b>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="action-panels">
            <div class="panel">
                <h3><i class="fas fa-edit"></i>Actualizar</h3>
                <?php
                // Conexión a la base de datos
                include 'conexion.php';
                if ($conn->connect_error) {
                    echo '<div class="error-message">Error de conexión a la base de datos.</div>';
                    exit;
                }
                // Obtener activos
                $activos = [];
                $result = $conn->query("SELECT a.id, a.nombre, c.nombre AS categoria FROM activos a INNER JOIN categorias c ON a.id_categoria = c.id");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $activos[] = $row;
                    }
                }
                // Obtener sitios
                $sitios = [];
                $result2 = $conn->query("SELECT id, nombre FROM sitios");
                if ($result2) {
                    while ($row = $result2->fetch_assoc()) {
                        $sitios[] = $row;
                    }
                }
                // Obtener estados
                $estados = [];
                $result3 = $conn->query("SELECT id, nombre FROM estado_activos");
                if ($result3) {
                    while ($row = $result3->fetch_assoc()) {
                        $estados[] = $row;
                    }
                }
                ?>
                <form id="updateAssetForm" method="post" action="actualizar_estado.php">
                    <div class="form-group">
                        <label for="activo_id"><i class="fas fa-laptop"></i> SELECCIONAR ACTIVO:</label>
                        <select class="form-control" id="activo_id" name="activo_id" required>
                            <option value="">-- Seleccionar activo --</option>
                            <?php foreach ($activos as $a): ?>
                                <option value="<?= htmlspecialchars($a['id']) ?>">
                                    <?= htmlspecialchars($a['nombre']) ?> (<?= htmlspecialchars($a['categoria']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nuevo_estado"><i class="fas fa-info-circle"></i> NUEVO ESTADO:</label>
                        <select class="form-control" id="nuevo_estado" name="nuevo_estado" required>
                            <option value="">-- Seleccionar estado --</option>
                            <?php foreach ($estados as $e): ?>
                                <option value="<?= htmlspecialchars($e['id']) ?>">
                                    <?= htmlspecialchars($e['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sitio_destino"><i class="fas fa-map-marker-alt"></i> SITIO DESTINO:</label>
                        <select class="form-control" id="sitio_destino" name="sitio_destino" required>
                            <option value="">-- Seleccionar sitio --</option>
                            <?php foreach ($sitios as $s): ?>
                                <option value="<?= htmlspecialchars($s['id']) ?>">
                                    <?= htmlspecialchars($s['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="observaciones"><i class="fas fa-comment"></i> OBSERVACIONES:</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Ingrese observaciones sobre el cambio de estado..."></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Estado
                        </button>
                    </div>
                </form>
                <div id="messageContainer"></div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title"><i class="fas fa-question-circle"></i> Confirmar Actualización</h3>
            <p>¿Está seguro que desea actualizar el estado del activo seleccionado?</p>
            <div class="modal-buttons">
                <button id="cancelBtn" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button id="confirmBtn" class="btn btn-primary">
                    <i class="fas fa-check"></i> Confirmar
                </button>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Luz a las Naciones</p>
    </footer>

    <script>
        // Mostrar modal de confirmación al enviar el formulario
        document.getElementById('updateAssetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('confirmModal').style.display = 'block';
        });
        // Cancelar modal
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').style.display = 'none';
        });
        // Confirmar y enviar formulario
        document.getElementById('confirmBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').style.display = 'none';
            document.getElementById('updateAssetForm').submit();
        });
        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>