<?php
$conexion = new mysqli("localhost", "root", "", "sistema_activos");

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
    
    // Validación de id_estado (1-4)
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
    
    $codigoBarras = !empty($_POST["codigoBarras"]) ? $_POST["codigoBarras"] : null;

    // Preparar y ejecutar
    $stmt = $conexion->prepare("INSERT INTO activos (nombre, descripcion, id_estado, id_sitio, id_categoria, codigoBarras) VALUES (?, ?, ?, ?, ?, ?)");
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
                    text: 'Error al registrar: " . addslashes($conexion->error) . "',
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
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
  <style>
    :root {
      --background: #e0e5ec;
      --shadow-light: #ffffff;
      --shadow-dark: #a3b1c6;
      --primary: #3498db;
      --error: #e74c3c;
      --success: #2ecc71;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--background);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      background-color: var(--background);
      padding: 40px;
      border-radius: 30px;
      box-shadow: -10px -10px 20px var(--shadow-light),
                   10px 10px 20px var(--shadow-dark);
      width: 100%;
      max-width: 500px;
      transition: all 0.3s ease;
      animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
      font-size: 26px;
      font-weight: 600;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 8px;
      color: #2c3e50;
      font-weight: 500;
      margin-top: 20px;
      font-size: 14px;
    }

    input, select {
      padding: 12px 15px;
      border: none;
      border-radius: 15px;
      background: var(--background);
      box-shadow: inset -5px -5px 10px var(--shadow-light),
                  inset 5px 5px 10px var(--shadow-dark);
      font-size: 14px;
      color: #333;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      box-shadow: inset -2px -2px 5px var(--shadow-light),
                  inset 2px 2px 5px var(--shadow-dark),
                  0 0 0 2px rgba(52, 152, 219, 0.3);
    }

    input:invalid {
      box-shadow: inset -5px -5px 10px var(--shadow-light),
                  inset 5px 5px 10px var(--shadow-dark),
                  0 0 0 2px rgba(231, 76, 60, 0.3);
    }

    button {
      margin-top: 30px;
      padding: 14px;
      background: var(--background);
      border: none;
      border-radius: 20px;
      font-weight: bold;
      font-size: 15px;
      color: var(--primary);
      box-shadow: -6px -6px 12px var(--shadow-light),
                   6px 6px 12px var(--shadow-dark);
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    button:hover {
      background: var(--primary);
      color: #fff;
      transform: translateY(-2px);
    }

    button:active {
      transform: translateY(1px);
    }

    button::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 5px;
      height: 5px;
      background: rgba(255, 255, 255, 0.5);
      opacity: 0;
      border-radius: 100%;
      transform: scale(1, 1) translate(-50%);
      transform-origin: 50% 50%;
    }

    button:focus:not(:active)::after {
      animation: ripple 0.6s ease-out;
    }

    @keyframes ripple {
      0% {
        transform: scale(0, 0);
        opacity: 0.5;
      }
      100% {
        transform: scale(20, 20);
        opacity: 0;
      }
    }

    .error {
      color: var(--error);
      font-size: 12px;
      margin-top: 5px;
      display: none;
    }

    .validacion-error {
      color: var(--error);
      font-size: 12px;
      margin-top: 5px;
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Registro de Activo Fijo</h1>
    
    <form method="POST" id="formularioActivo" onsubmit="return validarFormulario()">
    <label>Nombre del activo:</label>
      <input type="text" name="nombre" required>

      <label>Descripción:</label>
      <input type="text" name="descripcion" required>

      <label>Estado:</label>
      <select name="id_estado" required>
        <option value="">Seleccione un estado</option>
        <option value="1">Disponible</option>
        <option value="2">En uso</option>
        <option value="3">Mantenimiento</option>
        <option value="4">Baja</option>
      </select>

      <label>Sitio:</label>
      <select name="id_sitio" required>
        <option value="">Seleccione un sitio</option>
        <option value="1">Oficina Central</option>
        <option value="2">Sucursal Norte</option>
        <option value="3">Sucursal Sur</option>
      </select>

      <label>Categoría:</label>
      <select name="id_categoria" required>
        <option value="">Seleccione una categoría</option>
        <option value="1">Computadora</option>
        <option value="2">Impresora</option>
        <option value="3">Mueble</option>
        <option value="4">Vehículo</option>
        <option value="5">Herramienta</option>
        <option value="6">Celular</option>
        <option value="7">Router</option>
        <option value="8">Tablet</option>
        <option value="9">Otro</option>
      </select>

      <label>Código de Barras (opcional):</label>
      <input type="text" name="codigoBarras">

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