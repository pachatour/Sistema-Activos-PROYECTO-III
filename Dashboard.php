<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Gestión de Activos</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <div class="cards">
            <div class="card azul">
                <h5>Activos Totales</h5>
                <h2>128</h2>
            </div>
            <div class="card blanco">
                <h5>Usuarios Registrados</h5>
                <h2>37</h2>
            </div>
            <div class="card amarillo">
                <h5>Reportes Generados</h5>
                <h2>12</h2>
            </div>
        </div>

        <div class="content">
            <div class="metrics">
                <h3>Activos por Estado</h3>
                <ul>
                    <li><span>Operativo</span><span class="badge">89</span></li>
                    <li><span>En reparación</span><span class="badge">25</span></li>
                    <li><span>De baja</span><span class="badge">14</span></li>
                </ul>
            </div>

            <div class="events">
                <h3>Últimos Reportes</h3>
                <ul>
                    <li>
                        <div class="event">
                            <div class="icon">📝</div>
                            <div class="info">
                                <strong>Proyector AULA 3</strong><br>
                                <small>2025-04-09 10:30</small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="event">
                            <div class="icon">📝</div>
                            <div class="info">
                                <strong>Impresora Oficina</strong><br>
                                <small>2025-04-08 14:10</small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="event">
                            <div class="icon">📝</div>
                            <div class="info">
                                <strong>Router Principal</strong><br>
                                <small>2025-04-08 09:55</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
