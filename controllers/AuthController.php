<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['accion'])) {
    die("Acceso no permitido.");
}

$db = (new Database())->connect();
$usuario_model = new Usuario($db);
$accion = $_POST['accion'];

// --- LÓGICA DE REGISTRO (Ya la teníamos) ---
if ($accion === 'registro') {
    // ... (toda la lógica de registro que ya hicimos permanece aquí, sin cambios)
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($email) || empty($contrasena)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Todos los campos son obligatorios.'];
        header('Location: ../views/auth/registro.php');
        exit();
    }
    if ($contrasena !== $confirmar_contrasena) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Las contraseñas no coinciden.'];
        header('Location: ../views/auth/registro.php');
        exit();
    }
    if (strlen($contrasena) < 6) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'La contraseña debe tener al menos 6 caracteres.'];
        header('Location: ../views/auth/registro.php');
        exit();
    }

    $usuario_model->email = $email;
    if ($usuario_model->emailExists()) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'El correo electrónico ya está registrado. Por favor, intenta iniciar sesión.'];
        header('Location: ../views/auth/registro.php');
        exit();
    }

    $usuario_model->nombre = $nombre;
    $usuario_model->contrasena = $contrasena;

    if ($usuario_model->register()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => '¡Registro exitoso! Ahora puedes iniciar sesión.'];
        header('Location: ../views/auth/login.php');
        exit();
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hubo un error al crear tu cuenta.'];
        header('Location: ../views/auth/registro.php');
        exit();
    }
}

// ===============================================
// ======> NUEVO BLOQUE: LÓGICA DE LOGIN <======
// ===============================================
if ($accion === 'login') {
    // 1. Recoger datos del formulario de login
    $email = $_POST['email'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($email) || empty($contrasena)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Email y contraseña son obligatorios.'];
        header('Location: ../views/auth/login.php');
        exit();
    }

    // 2. Asignar datos al modelo y llamar a la función de login
    $usuario_model->email = $email;
    $usuario_model->contrasena = $contrasena;

    $usuario_logueado = $usuario_model->login();

    if ($usuario_logueado) {
        // ¡Éxito! Las credenciales son correctas.
        // 3. Crear la sesión para el usuario
        $_SESSION['user_id'] = $usuario_logueado['id'];
        $_SESSION['user_nombre'] = $usuario_logueado['nombre'];
        $_SESSION['user_rol'] = $usuario_logueado['rol'];

        // 4. Redirigir según el rol
        if ($usuario_logueado['rol'] === 'admin') {
            header('Location: ../views/admin/gestionar_solicitudes.php');
        } else {
            // Redirigir al usuario normal a la galería de mascotas
            header('Location: ../views/public/mascotas.php');
        }
        exit();
    } else {
        // Error: Credenciales incorrectas
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'El correo electrónico o la contraseña son incorrectos.'];
        header('Location: ../views/auth/login.php');
        exit();
    }
}
