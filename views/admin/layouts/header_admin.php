<?php
// Iniciar la sesión en cada página de admin
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ======================================================================
// ======> VERIFICACIÓN DE SEGURIDAD MÁS IMPORTANTE <======
// ======================================================================
// Si no existe la sesión de usuario O el rol no es 'admin',
// lo redirigimos a la página de login.
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    // Guardamos un mensaje de error para mostrar en el login
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Acceso denegado. Por favor, inicie sesión como administrador.'];
    header('Location: ../../auth/login.php'); // Ojo con la ruta de redirección
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- El título será dinámico dependiendo de la página -->
    <title><?php echo $page_title ?? 'Panel de Administrador'; ?></title>
    <style>
        /* Estilos generales y del navbar */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f5a623;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --background-color: #f7f9fc;
            --text-color: #333;
            --card-bg-color: #ffffff;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
        }

        .admin-navbar {
            background-color: #343a40;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            
        }

        .admin-navbar .logo {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .admin-navbar .logo a {
            color: white;
            text-decoration: none;
        }

        .admin-navbar nav a {
            color: #f8f9fa;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        .admin-navbar nav a:hover,
        .admin-navbar nav a.active {
            color: var(--primary-color);
        }

        .admin-navbar .user-info a {
            background-color: var(--danger-color);
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .main-container {
            padding: 2rem;
        }
    </style>
</head>

<body>

    <header class="admin-navbar">
        <div class="logo">
            <a href="gestionar_solicitudes.php">Admin Panel</a>
        </div>
        <nav>
            <!-- El enlace activo tendrá la clase 'active' -->
            <a href="/views/admin/gestionar-mascotas.php" class="<?php if (basename($_SERVER['PHP_SELF']) == 'gestionar_mascotas.php') echo 'active'; ?>">Mascotas</a>
            <a href="/views/admin/gestionar_solicitudes.php" class="<?php if (basename($_SERVER['PHP_SELF']) == 'gestionar_solicitudes.php') echo 'active'; ?>">Solicitudes</a>
        </nav>
        <div class="user-info">
            <span>Hola, <?php echo htmlspecialchars($_SESSION['user_nombre']); ?></span>
            <a href="../../controllers/LogoutController.php">Cerrar Sesión</a>
        </div>
    </header>

    <main class="main-container">
        <!-- El contenido de cada página se insertará aquí -->```
    </main> <!-- Cierra el main-container -->

</body>

</html>