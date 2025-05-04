<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Gestión de Activos</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .dashboard {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            padding: 20px;
            margin: 0 10px;
            border-radius: 12px;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .card.azul { background-color: #007bff; }
        .card.blanco { background-color: #6c757d; }
        .card.amarillo {
            background-color: #ffc107;
            color: #000;
        }

        .content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .metrics, .events {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .metrics h3, .events h3 {
            margin-bottom: 15px;
        }

        .metrics ul, .events ul {
            list-style: none;
            padding: 0;
        }

        .metrics li, .events li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .badge {
            background-color: #007bff;
            padding: 5px 10px;
            border-radius: 20px;
            color: #fff;
            font-weight: bold;
        }

        .event {
            display: flex;
            align-items: center;
        }

        .icon {
            font-size: 24px;
            margin-right: 10px;
        }

        .assets-header {
            margin: 30px 0 10px 0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            background-color: #e0e0e0;
            transition: background-color 0.3s;
        }

        .filter-btn.active, .filter-btn:hover {
            background-color: #007bff;
            color: #fff;
        }

        .search-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #searchInput {
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }

        .filter-advanced {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            background-color: #6c757d;
            color: #fff;
            cursor: pointer;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .styled-table th, .styled-table td {
            padding: 12px 15px;
            text-align: left;
        }

        .styled-table thead {
            background-color: #007bff;
            color: #fff;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
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

        <div class="assets-header">
            <div class="filter-buttons">
                <button class="filter-btn active">🔘 Todos</button>
                <button class="filter-btn">📁 Mis datos</button>
            </div>
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="🔍 Filtrar por palabra clave">
            </div>
        </div>

        <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#assetTable tbody tr');

        rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        let found = false;

        cells.forEach(cell => {
            if (cell.textContent.toLowerCase().includes(searchValue)) {
                found = true;
            }
        });

        row.style.display = found ? '' : 'none';
    });
});
</script>

        <table class="styled-table" id="assetTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>ID Estado</th>
                    <th>ID Categoría</th>
                    <th>ID Sitio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conexion = new mysqli("localhost", "root", "", "sistema_activos");
                if ($conexion->connect_error) {
                    die("Error de conexión: " . $conexion->connect_error);
                }

                $consulta = "SELECT * FROM activos";
                $resultado = $conexion->query($consulta);

                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
                        echo "<td>" . $fila['id_estado'] . "</td>";
                        echo "<td>" . $fila['id_categoria'] . "</td>";
                        echo "<td>" . $fila['id_sitio'] . "</td>";
                        echo "<td>" . $fila['cantidad'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron activos.</td></tr>";
                }
                $conexion->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
