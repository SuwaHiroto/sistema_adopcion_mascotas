<?php
// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ===== VERIFICACIÓN DE SEGURIDAD PARA USUARIOS =====
// Si no existe la sesión de usuario, lo expulsamos al login.
// A diferencia del admin, aquí nos vale cualquier usuario logueado.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Por favor, inicie sesión para ver esta página.'];
    // La ruta desde /views/public/ es ../auth/login.php
    header('Location: ../auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Mi Albergue'; ?></title>
    <style>
        /* Estilos generales y del navbar de usuario */
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

        .user-navbar {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: var(--shadow);
        }

        .user-navbar .logo a {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
        }

        .user-navbar nav a {
            color: #f8f9fa;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: 500;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        .user-navbar nav a:hover,
        .user-navbar nav a.active {
            color: white;
            border-bottom-color: var(--secondary-color);
        }

        .user-navbar .user-info a {
            background-color: var(--danger-color);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .user-navbar .user-info a:hover {
            background-color: #c82333;
        }

        .main-container {
            padding: 2rem;
        }
    </style>
</head>

<body>

    <header class="user-navbar">
        <div class="logo">
            <a href="mascotas.php">AdoptaUnAmigo</a>
        </div>
        <nav>
            <a href="/views/public/mascotas.php" class="<?php if (basename($_SERVER['PHP_SELF']) == 'mascotas.php') echo 'active'; ?>">Mascotas</a>
            <a href="/views/public/mis_solicitudes.php" class="<?php if (basename($_SERVER['PHP_SELF']) == 'mis_solicitudes.php') echo 'active'; ?>">Mis Solicitudes</a>
        </nav>
        <div class="user-info">
            <span>Hola, <?php echo htmlspecialchars($_SESSION['user_nombre']); ?></span>
            <a href="../../controllers/LogoutController.php">Cerrar Sesión</a>
        </div>
    </header>

    <main class="main-container">
        <!-- El contenido de cada página se insertará aquí -->