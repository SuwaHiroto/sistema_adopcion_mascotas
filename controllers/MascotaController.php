<?php
// Iniciar sesión para futuras validaciones de rol de admin
session_start();

// Requerir la configuración de la base de datos y el modelo
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Mascota.php';

// Validar que el usuario sea administrador (descomentar cuando tengas el login)
/*
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    die("Acceso denegado. No tienes permisos de administrador.");
}
*/

$db = (new Database())->connect();
$mascota = new Mascota($db);

// --- LÓGICA PARA MANEJAR LAS ACCIONES (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Función para manejar la subida de la foto
    function manejarSubidaFoto($file_input) {
        if (isset($file_input) && $file_input['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../public/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            // Crear un nombre de archivo único
            $nombre_archivo = uniqid('mascota_', true) . '.' . strtolower(pathinfo($file_input['name'], PATHINFO_EXTENSION));
            $upload_file = $upload_dir . $nombre_archivo;

            // Mover el archivo
            if (move_uploaded_file($file_input['tmp_name'], $upload_file)) {
                return $nombre_archivo; // Devolver solo el nombre del archivo
            }
        }
        return null; // Devolver null si falla la subida
    }

    // ---- ACCIÓN: CREAR ----
    if ($action === 'create') {
        $mascota->nombre = $_POST['nombre'];
        $mascota->especie = $_POST['especie'];
        $mascota->raza = $_POST['raza'];
        $mascota->edad = $_POST['edad'];
        $mascota->descripcion = $_POST['descripcion'];
        $mascota->estado = $_POST['estado'];
        
        $nombre_foto = manejarSubidaFoto($_FILES['foto']);
        $mascota->foto = $nombre_foto ?? 'default.jpg'; // Usar foto por defecto si no se sube una

        if ($mascota->create()) {
            $_SESSION['message'] = 'Mascota agregada exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al agregar la mascota.';
        }
    }

    // ---- ACCIÓN: ACTUALIZAR ----
    if ($action === 'update') {
        $mascota->id = $_POST['id'];
        $mascota->nombre = $_POST['nombre'];
        $mascota->especie = $_POST['especie'];
        $mascota->raza = $_POST['raza'];
        $mascota->edad = $_POST['edad'];
        $mascota->descripcion = $_POST['descripcion'];
        $mascota->estado = $_POST['estado'];

        // Manejar la foto: si se sube una nueva, se reemplaza. Si no, se mantiene la antigua.
        $nombre_foto_nueva = manejarSubidaFoto($_FILES['foto']);
        if ($nombre_foto_nueva) {
            $mascota->foto = $nombre_foto_nueva;
            // Opcional: Borrar foto antigua del servidor
            // if ($_POST['foto_actual']) unlink(__DIR__ . '/../public/uploads/' . $_POST['foto_actual']);
        } else {
            $mascota->foto = $_POST['foto_actual']; // Mantener la foto existente
        }

        if ($mascota->update()) {
            $_SESSION['message'] = 'Mascota actualizada exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al actualizar la mascota.';
        }
    }

    // Redirigir siempre a la página de gestión para ver los cambios
    header('Location: ../views/admin/gestionar_mascotas.php');
    exit();
}

// --- LÓGICA PARA MANEJAR LA ACCIÓN DE ELIMINAR (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $mascota->id = $_GET['id'];
    
    // Opcional pero recomendado: obtener el nombre de la foto para borrarla del servidor
    $mascota->getById($mascota->id);
    $foto_a_borrar = $mascota->foto;

    if ($mascota->delete()) {
        // Borrar el archivo de la foto del servidor
        if ($foto_a_borrar && $foto_a_borrar !== 'default.jpg' && file_exists(__DIR__ . '/../public/uploads/' . $foto_a_borrar)) {
            unlink(__DIR__ . '/../public/uploads/' . $foto_a_borrar);
        }
        $_SESSION['message'] = 'Mascota eliminada exitosamente.';
    } else {
        $_SESSION['error'] = 'Error al eliminar la mascota.';
    }
    
    header('Location: ../views/admin/gestionar_mascotas.php');
    exit();
}
?>