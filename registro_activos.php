<?php
session_start();
include 'conexion.php';
include 'procesar_activo.php'; // Incluye el archivo con la función obtenerOpciones

// Obtener opciones para los select
$estados_activos = obtenerOpciones($conexion, 'estado_activos', 'id', 'nombre');
$sitios = obtenerOpciones($conexion, 'sitios', 'id', 'nombre');
$categorias = obtenerOpciones($conexion, 'categorias', 'id', 'nombre');

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Activos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .mensaje-exito {
            color: green;
            margin-bottom: 20px;
        }
        .mensaje-error {
            color: red;
            margin-bottom: 20px;
        }
        .codigo-barras {
            font-family: monospace;
            font-size: 24px;
            text-align: center;
            margin-top: 20px;
            letter-spacing: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Registrar Nuevo Activo</h1>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success mensaje-exito">
                <?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?>
            </div>
            
            <?php if (isset($_SESSION['codigo_barras'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Código de Barras Generado</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="codigo-barras">*<?php echo htmlspecialchars($_SESSION['codigo_barras']); ?>*</div>
                        <p class="mt-2"><?php echo htmlspecialchars($_SESSION['codigo_barras']); ?></p>
                        <button class="btn btn-secondary mt-2" onclick="window.print()">Imprimir</button>
                    </div>
                </div>
                <?php unset($_SESSION['codigo_barras']); ?>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger mensaje-error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="procesar_activo.php" method="POST" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre del Activo *</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required maxlength="100">
                    <div class="invalid-feedback">
                        Por favor ingrese el nombre del activo.
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="descripcion" class="form-label">Descripción *</label>
                    <textarea class="form-control" name="descripcion" id="descripcion" required maxlength="255" rows="2"></textarea>
                    <div class="invalid-feedback">
                        Por favor ingrese una descripción.
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="id_estado" class="form-label">Estado *</label>
                    <select class="form-select" name="id_estado" id="id_estado" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($estados as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Seleccione un estado.
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="id_sitio" class="form-label">Ubicación *</label>
                    <select class="form-select" name="id_sitio" id="id_sitio" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($sitios as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Seleccione una ubicación.
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="id_categoria" class="form-label">Categoría *</label>
                    <select class="form-select" name="id_categoria" id="id_categoria" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($categorias as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Seleccione una categoría.
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Guardar Activo</button>
                <a href="listar_activos.php" class="btn btn-secondary">Ver Lista de Activos</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        (function () {
            'use strict'
            
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>