<?php
// Simulación (puedes luego usar conexión.php para conectar a BD)
$data = [
    "total" => rand(80, 100),
    "disponibles" => rand(40, 60),
    "enUso" => rand(20, 40),
];

header('Content-Type: application/json');
echo json_encode($data);
?>
