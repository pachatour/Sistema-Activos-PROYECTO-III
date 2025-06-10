<?php
include 'conexion.php';

$estados = $conn->query("SELECT id, nombre FROM estado_activos ORDER BY id");
$sitios = $conn->query("SELECT id, nombre FROM sitios ORDER BY id");
$categorias = $conn->query("SELECT id, nombre FROM categorias ORDER BY id");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar campos no vacíos (incluyendo espacios)
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    
    if (empty($nombre) || empty($descripcion)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Campos incompletos',
                    text: 'Nombre y descripción no pueden estar vacíos',
                    showConfirmButton: false,
                    timer: 3000
                });
              </script>";
        exit();
    }
    
    // Validación de id_estado
    $id_estado = isset($_POST["id_estado"]) ? (int)$_POST["id_estado"] : null;
    if ($id_estado === null || $id_estado < 1 || $id_estado > 4) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El ID Estado debe ser entre 1 y 4',
                    showConfirmButton: false,
                    timer: 3000
                });
              </script>";
        exit();
    }
    
    // Validación de id_sitio (1-3)
    $id_sitio = isset($_POST["id_sitio"]) ? (int)$_POST["id_sitio"] : null;
    if ($id_sitio === null || $id_sitio < 1 || $id_sitio > 3) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El ID Sitio debe ser entre 1 y 3',
                    showConfirmButton: false,
                    timer: 3000
                });
              </script>";
        exit();
    }
    
    // Validación de id_categoria (1-9)
    $id_categoria = isset($_POST["id_categoria"]) ? (int)$_POST["id_categoria"] : null;
    if ($id_categoria === null || $id_categoria < 1 || $id_categoria > 9) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El ID Categoría debe ser entre 1 y 9',
                    showConfirmButton: false,
                    timer: 3000
                });
              </script>";
        exit();
    }
    
    // Quitar código de barras del formulario, pero enviar vacío
    $codigoBarras = ""; // Siempre vacío

    // Preparar y ejecutar
    $stmt = $conn->prepare("INSERT INTO activos (nombre, descripcion, id_estado, id_sitio, id_categoria, codigoBarras) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiss", $nombre, $descripcion, $id_estado, $id_sitio, $id_categoria, $codigoBarras);

    if ($stmt->execute()) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Efecto de confeti mejorado
                    const duration = 3 * 1000;
                    const end = Date.now() + duration;
                    const colors = ['#3498db', '#2ecc71', '#e74c3c', '#f1c40f', '#9b59b6'];
                    
                    (function frame() {
                        confetti({
                            particleCount: 4,
                            angle: 60,
                            spread: 70,
                            origin: { x: 0 },
                            colors: colors,
                            shapes: ['circle', 'square']
                        });
                        confetti({
                            particleCount: 4,
                            angle: 120,
                            spread: 70,
                            origin: { x: 1 },
                            colors: colors,
                            shapes: ['circle', 'square']
                        });
                        
                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    }());
                    
                    // Notificación de éxito estilo anterior
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Activo registrado correctamente',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2500,
                        willOpen: () => {
                            // Efecto de vibración sutil
                            document.querySelector('.swal2-popup').animate([
                                { transform: 'scale(0.8)', opacity: 0 },
                                { transform: 'scale(1.05)', opacity: 1, offset: 0.5 },
                                { transform: 'scale(1)', opacity: 1 }
                            ], {
                                duration: 500,
                                easing: 'cubic-bezier(0.36, 0.07, 0.19, 0.97)'
                            });
                        }
                    }).then(() => {
                        // Limpiar el formulario después de mostrar la alerta
                        document.querySelector('form').reset();
                    });
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al registrar: " . addslashes($conn->error) . "',
                    showConfirmButton: true
                });
              </script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Activos</title>
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Confetti JS -->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    .navbar h1 {
      font-size: 1.5rem;
      color: white;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
    }

    .form-container {
      background-color: rgba(0, 30, 60, 0.92);
      border-radius: 18px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.4);
      max-width: 900px; /* Aumenta el ancho máximo */
      margin: 40px auto;
      padding: 40px 32px 32px 32px;
      border: 2px solid #FFD700;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px);}
      to { opacity: 1; transform: translateY(0);}
    }

    h1 {
      text-align: center;
      color: #FFD700;
      margin-bottom: 30px;
      font-size: 1.6rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    label {
      color: #FFD700;
      font-weight: 500;
      margin-bottom: 5px;
      font-size: 1rem;
      margin-top: 0;
    }

    input, select {
      padding: 12px 15px;
      border: none;
      border-radius: 12px;
      background: rgba(255,255,255,0.08);
      color: #fff;
      font-size: 1rem;
      transition: background 0.3s, box-shadow 0.3s;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    input:focus, select:focus {
      outline: none;
      background: rgba(255,255,255,0.18);
      box-shadow: 0 0 0 2px #FFD700;
    }

    button {
      margin-top: 18px;
      padding: 14px;
      background: #FFD700;
      border: none;
      border-radius: 20px;
      font-weight: bold;
      font-size: 1.1rem;
      color: #00264d;
      box-shadow: 0 2px 8px rgba(0,0,0,0.18);
      cursor: pointer;
      transition: background 0.3s, color 0.3s, transform 0.2s;
    }

    button:hover {
      background: #fffbe6;
      color: #00264d;
      transform: translateY(-2px) scale(1.03);
    }

    .error, .validacion-error {
      color: #e74c3c;
      font-size: 0.95em;
      margin-top: 2px;
      margin-bottom: -10px;
      display: none;
    }

    @media (max-width: 600px) {
      .form-container {
        padding: 20px 8px 18px 8px;
        max-width: 128vw;
      }
      h1 {
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
      <div class="container-fluid">
          <a class="navbar-brand" href="crud_libros.php">
              <i class='fas fa-book-open' style='font-size:24px'></i>
              <span class="d-none d-sm-inline">REGISTROS DE ACTIVOS</span>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <i class="fas fa-bars"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
             <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                        <a class="nav-link active" href="dashboard_admin.html">
                            <i class='fas fa-home' ></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="inventario.php">
                            <i class="fa-brands fa-wpforms"></i> Inventario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="estado_activos.php">
                            <i class="fas fa-exchange-alt me-1"></i> Estado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="historiales.php">
                            <i class="fas fa-users me-1"></i> Historiales
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link active" href="formulario.php">
                            <i class="fas fa-chart-bar me-1"></i> Registrar activos
                        </a>
                    </li>-->
                    <li class="nav-item">
                        <a class="nav-link active" href="reporte_graficos.php">
                            <i class="fas fa-chart-bar me-1"></i> Reportes graficos
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


  <div class="form-container">
    <form method="POST" id="formularioActivo" onsubmit="return validarFormulario()">
      <label>Nombre del activo:</label>
      <input type="text" name="nombre" required>

      <label>Descripción:</label>
      <input type="text" name="descripcion" required>

      <label>Estado:</label>
      <select name="id_estado" required>
        <option value="">Seleccione un estado</option>
        <?php while($e = $estados->fetch_assoc()): ?>
          <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Sitio:</label>
      <select name="id_sitio" required>
        <option value="">Seleccione un sitio</option>
        <?php while($s = $sitios->fetch_assoc()): ?>
          <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Categoría:</label>
      <select name="id_categoria" required>
        <option value="">Seleccione una categoría</option>
        <?php while($c = $categorias->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
        <?php endwhile; ?>
      </select>

      <!-- Código de Barras eliminado del formulario -->

      <button type="submit">Registrar Activo</button>
    </form>
  </div>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    // Validación en tiempo real
    function validarCampo(input) {
      const errorElement = document.getElementById(`error${input.name.charAt(0).toUpperCase() + input.name.slice(1)}`);
      if (input.value.trim() === '') {
        errorElement.style.display = 'block';
        input.setCustomValidity('Este campo no puede estar vacío');
      } else {
        errorElement.style.display = 'none';
        input.setCustomValidity('');
      }
    }

    // Validación al enviar el formulario
    function validarFormulario() {
      const nombre = document.querySelector('input[name="nombre"]');
      const descripcion = document.querySelector('input[name="descripcion"]');
      const idEstado = document.querySelector('input[name="id_estado"]');
      const idSitio = document.querySelector('input[name="id_sitio"]');
      const idCategoria = document.querySelector('input[name="id_categoria"]');
      
      let valido = true;

      // Validar nombre y descripción (no solo espacios)
      if (nombre.value.trim() === '') {
        document.getElementById('errorNombre').style.display = 'block';
        nombre.setCustomValidity('Este campo no puede estar vacío');
        valido = false;
      }
      
      if (descripcion.value.trim() === '') {
        document.getElementById('errorDescripcion').style.display = 'block';
        descripcion.setCustomValidity('Este campo no puede estar vacío');
        valido = false;
      }

      // Validar ID Estado (1-4)
      if (idEstado.value < 1 || idEstado.value > 4) {
        document.getElementById('errorEstado').textContent = 'El ID Estado debe ser entre 1 y 4';
        valido = false;
      } else {
        document.getElementById('errorEstado').textContent = '';
      }
      
      // Validar ID Sitio (1-3)
      if (idSitio.value < 1 || idSitio.value > 3) {
        document.getElementById('errorSitio').textContent = 'El ID Sitio debe ser entre 1 y 3';
        valido = false;
      } else {
        document.getElementById('errorSitio').textContent = '';
      }
      
      // Validar ID Categoría (1-9)
      if (idCategoria.value < 1 || idCategoria.value > 9) {
        document.getElementById('errorCategoria').textContent = 'El ID Categoría debe ser entre 1 y 9';
        valido = false;
      } else {
        document.getElementById('errorCategoria').textContent = '';
      }
      
      return valido;
    }
  </script>
  <script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formularioActivo');
  const nombreInput = form.querySelector('input[name="nombre"]');
  const descripcionInput = form.querySelector('input[name="descripcion"]');
  const idEstadoInput = form.querySelector('input[name="id_estado"]');
  const idSitioInput = form.querySelector('input[name="id_sitio"]');
  const idCategoriaInput = form.querySelector('input[name="id_categoria"]');
  const campos = form.querySelectorAll('input, select, textarea');

  // Validación individual en tiempo real
  campos.forEach(input => {
    input.addEventListener('input', function () {
      validarCampo(this);
    });
  });

  function validarCampo(input) {
    const nombreCampo = input.name.charAt(0).toUpperCase() + input.name.slice(1);
    const errorElement = document.getElementById(`error${nombreCampo}`);
    
    if (input.value.trim() === '') {
      mostrarError(input, 'Este campo no puede estar vacío');
      if (errorElement) errorElement.style.display = 'block';
    } else {
      limpiarError(input);
      if (errorElement) errorElement.style.display = 'none';
    }
  }

  // Validación de nombre único
  nombreInput.addEventListener('blur', function () {
    const nombre = this.value.trim();
    if (nombre !== '') {
      fetch('validar_activo.php?nombre=' + encodeURIComponent(nombre))
        .then(response => response.json())
        .then(data => {
          if (data.existe) {
            mostrarError(nombreInput, 'Ya existe un activo con este nombre');
          } else {
            limpiarError(nombreInput);
          }
        });
    }
  });

  // Validación final al enviar
  form.addEventListener('submit', function (e) {
    let valido = true;

    campos.forEach(campo => {
      if (campo.hasAttribute('required') && campo.value.trim() === '') {
        mostrarError(campo, 'Este campo es obligatorio');
        valido = false;
      }
    });

    // Validar rangos
    if (idEstadoInput.value < 1 || idEstadoInput.value > 4) {
      mostrarError(idEstadoInput, 'El ID Estado debe ser entre 1 y 4');
      valido = false;
    }

    if (idSitioInput.value < 1 || idSitioInput.value > 3) {
      mostrarError(idSitioInput, 'El ID Sitio debe ser entre 1 y 3');
      valido = false;
    }

    if (idCategoriaInput.value < 1 || idCategoriaInput.value > 9) {
      mostrarError(idCategoriaInput, 'El ID Categoría debe ser entre 1 y 9');
      valido = false;
    }

    if (!valido) e.preventDefault();
  });

  function mostrarError(input, mensaje) {
    let error = input.nextElementSibling;
    if (!error || !error.classList.contains('error')) {
      error = document.createElement('div');
      error.className = 'error';
      input.parentNode.insertBefore(error, input.nextSibling);
    }
    error.textContent = mensaje;
    input.classList.add('invalido');
    input.setCustomValidity(mensaje);
  }

  function limpiarError(input) {
    let error = input.nextElementSibling;
    if (error && error.classList.contains('error')) {
      error.remove();
    }
    input.classList.remove('invalido');
    input.setCustomValidity('');
  }
});
</script>
</body>
</html>