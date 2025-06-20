<?php
session_start();

// Desactiva almacenamiento en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verifica si hay una sesión activa
if (!isset($_SESSION['user_id'])) {
    // Redirige a login si no hay sesión
    header("Location: login.php");
    exit();
}
?>
