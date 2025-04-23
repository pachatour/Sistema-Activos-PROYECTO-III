<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Activos</title>
    <link rel="stylesheet" href="Carita.css">
    <script>
        function actualizarDatos() {
            fetch('datos_activos.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total').innerText = data.total;
                    document.getElementById('disponibles').innerText = data.disponibles;
                    document.getElementById('enUso').innerText = data.enUso;
                });
        }

        setInterval(actualizarDatos, 3000); // Actualiza cada 3s
        window.onload = actualizarDatos;
    </script>
</head>
<body>
    <div class="dashboard">
        <h1>Gestión de Activos</h1>
        <div class="card"><h2>Total</h2><p id="total">Cargando...</p></div>
        <div class="card"><h2>Disponibles</h2><p id="disponibles">Cargando...</p></div>
        <div class="card"><h2>En uso</h2><p id="enUso">Cargando...</p></div>
    </div>
</body>
</html>
