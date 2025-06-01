<?php
session_start();

// 3. Registrar el logout para auditoría (opcional)
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $fecha = date('Y-m-d H:i:s');
}

// 4. Destruir completamente la sesión
$_SESSION = array(); // Vaciar el array de sesión

// Eliminar la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// 5. Redirección con mensaje
header("Location: login.php?logout=success");
exit();
?>