<?php
include 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Actualizar Estado | Sistema de Activos</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="stylesheet" href="css/estado_activos.css">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-boxes"></i> 
                <span class="d-none d-sm-inline">ESTADO ACTIVOS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-arrow-alt-circle-down"></i> Ir a
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="dashboard_admin.php"><i class="fas fa-home"></i> &nbsp; Inicio</a></li>
                            <li><a class="dropdown-item" href="formulario.php"><i class="fas fa-plus-circle"></i> &nbsp Formulario</a></li>
                            <li><a class="dropdown-item" href="historiales.php"><i class="fas fa-history"></i> &nbsp Historiales</a></li>
                            <li><a class="dropdown-item" href="reportes.php"><i class="fas fa-file-alt"></i> &nbsp Reportes</a></li>
                            <li><a class="dropdown-item" href="reporte_graficos.php"><i class="fas fa-chart-pie"></i> &nbsp Reportes Gráficos</a></li>
                            <li><a class="dropdown-item" href="regresion.php"><i class="fas fa-project-diagram"></i> &nbsp Regresión</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="update-panel">
            <div class="panel-header">
                <i class="fas fa-edit"></i>
                <h3>Actualizar Estado de Activo</h3>
            </div>
            
            <?php
            // Conexión a la base de datos
            include 'conexion.php';
            if ($conn->connect_error) {
                echo '<div class="alert-message alert-error">
                        <i class="fas fa-exclamation-circle"></i> Error de conexión a la base de datos.
                      </div>';
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
            
            <div class="form-container">
                <form id="updateAssetForm" method="post" action="actualizar_estado.php">
                    <div class="row g-4">
                        <!-- Activo -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="activo_id" class="form-label">
                                    <i class="fas fa-laptop"></i> Activo
                                </label>
                                <select class="form-select" id="activo_id" name="activo_id" required>
                                    <option value="" selected disabled>Seleccionar activo</option>
                                    <?php foreach ($activos as $a): ?>
                                        <option value="<?= htmlspecialchars($a['id']) ?>">
                                            <?= htmlspecialchars($a['nombre']) ?> (<?= htmlspecialchars($a['categoria']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nuevo_estado" class="form-label">
                                    <i class="fas fa-info-circle"></i> Estado
                                </label>
                                <select class="form-select" id="nuevo_estado" name="nuevo_estado" required>
                                    <option value="" selected disabled>Seleccionar estado</option>
                                    <?php foreach ($estados as $e): ?>
                                        <option value="<?= htmlspecialchars($e['id']) ?>">
                                            <?= htmlspecialchars($e['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Sitio -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sitio_destino" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación
                                </label>
                                <select class="form-select" id="sitio_destino" name="sitio_destino" required>
                                    <option value="" selected disabled>Seleccionar sitio</option>
                                    <?php foreach ($sitios as $s): ?>
                                        <option value="<?= htmlspecialchars($s['id']) ?>">
                                            <?= htmlspecialchars($s['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Observaciones -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">
                                    <i class="fas fa-comment-dots"></i> Observaciones
                                </label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Detalles sobre el cambio de estado..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Botón de envío -->
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-gold">
                                <i class="fas fa-save"></i> Actualizar Estado
                            </button>
                        </div>
                    </div>
                </form>
                
                <div id="messageContainer"></div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-question-circle"></i> Confirmar</h3>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea actualizar el estado del activo seleccionado?</p>
            </div>
            <div class="modal-footer">
                <button id="cancelBtn" class="btn-modal btn-modal-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button id="confirmBtn" class="btn-modal btn-modal-primary">
                    <i class="fas fa-check"></i> Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Luz a las Naciones. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mostrar modal de confirmación
        document.getElementById('updateAssetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('confirmModal').classList.add('active');
        });
        
        // Cancelar modal
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.remove('active');
        });
        
        // Confirmar y enviar formulario
        document.getElementById('confirmBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.remove('active');
            document.getElementById('updateAssetForm').submit();
        });
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
        
        // Efecto de carga suave
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.body.style.opacity = '1';
            }, 100);
        });
        
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease';
    </script>
</body>
</html>