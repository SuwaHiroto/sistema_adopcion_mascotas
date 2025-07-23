<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Albergue de Mascotas</title>
    <style>
        /* Reutilizamos estilos para mantener la consistencia */
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
        }

        .card {
            background: var(--card-bg-color);
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: .5rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: .8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn {
            display: block;
            width: 100%;
            background: var(--primary-color);
            color: #fff;
            padding: 0.9rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1rem;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            color: #fff;
            text-align: center;
        }

        .alert-danger {
            background-color: var(--danger-color);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="card">
            <h1>Crear una Cuenta</h1>

            <!-- Mostrar mensajes de error/éxito -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
                    <?php echo $_SESSION['message']['text'];
                    unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/AuthController.php" method="POST">
                <!-- Campo oculto para identificar la acción -->
                <input type="hidden" name="accion" value="registro">

                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirmar_contrasena">Confirmar Contraseña</label>
                    <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required>
                </div>
                <button type="submit" class="btn">Registrarse</button>
            </form>

            <div class="login-link">
                <p>¿Ya tienes una cuenta? <a href="/views/auth/login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>

</body>

</html>