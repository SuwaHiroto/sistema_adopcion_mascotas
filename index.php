<?php
/**
 * Punto de entrada principal y simple.
 * Su única responsabilidad es redirigir a la página de inicio de sesión.
 */

// Construir la URL completa a la página de login.
// Esto es más robusto que usar una ruta relativa.
$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$login_page = "http://$host$uri/views/auth/login.php";

// Redirigir al usuario.
header("Location: $login_page");
exit;

?>