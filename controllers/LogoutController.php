<?php
// Iniciar la sesión para poder acceder a ella
session_start();

// 1. Desarmar todas las variables de sesión
$_SESSION = array();

// 2. Si se está usando cookies de sesión, borrarlas también
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

// 3. Finalmente, destruir la sesión
session_destroy();

// 4. Redirigir a la página de login con un mensaje
// No podemos usar la sesión para el mensaje porque ya la destruimos,
// así que lo pasamos por la URL.
header('Location: ../views/auth/login.php?status=logout_success');
exit();
