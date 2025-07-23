<?php
// Ya no necesitamos mostrar errores aquí en producción, pero es bueno dejarlo comentado.
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

session_start();

// Verificación de seguridad: solo usuarios logueados pueden enviar solicitudes
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Debes iniciar sesión para poder adoptar.'];
    header('Location: ../views/auth/login.php');
    exit();
}

// Requerir dependencias
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Solicitud.php';

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido.");
}

// Conexión a la BD y creación de una instancia del Modelo
$db = (new Database())->connect();
$solicitud_model = new Solicitud($db);

try {
    // Tomar el ID del usuario de la SESIÓN
    $id_usuario = $_SESSION['user_id'];

    // Tomar los datos del FORMULARIO
    $id_mascota = $_POST['id_mascota'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $tipo_vivienda = $_POST['tipo_vivienda'];
    $experiencia_previa = $_POST['experiencia_previa'];
    $motivo_adopcion = $_POST['motivo_adopcion'];

    // Asignar todos los datos al objeto del Modelo
    $solicitud_model->id_usuario = $id_usuario;
    $solicitud_model->id_mascota = $id_mascota;
    $solicitud_model->telefono = $telefono;
    $solicitud_model->direccion = $direccion;
    $solicitud_model->tipo_vivienda = $tipo_vivienda;
    $solicitud_model->experiencia_previa = $experiencia_previa;
    $solicitud_model->motivo_adopcion = $motivo_adopcion;

    // Llamar a la función 'create' del Modelo para guardar en la BD
    if ($solicitud_model->create()) {
        // ================================================================
        // ======> COMPORTAMIENTO FINAL: GUARDAR MENSAJE Y REDIRIGIR <======
        // ================================================================
        // Guardamos el mensaje de éxito en la sesión.
        $_SESSION['message'] = [
            'type' => 'success', // 'success' corresponderá a un estilo verde
            'text' => '¡Tu solicitud ha sido enviada con éxito! Ya puedes verla en tu historial.'
        ];
        // Redirigimos al usuario a la página donde verá sus solicitudes.
        header('Location: ../views/public/mis_solicitudes.php');
        exit();
        // ================================================================
    } else {
        // Si el método create() devuelve false por alguna razón desconocida
        throw new Exception("Hubo un error al registrar tu solicitud.");
    }
} catch (Exception $e) { // Usamos Exception para capturar tanto errores de BD como los nuestros
    // Guardamos el mensaje de error en la sesión
    $_SESSION['message'] = [
        'type' => 'danger', // 'danger' corresponderá a un estilo rojo
        'text' => 'Error: ' . $e->getMessage()
    ];
    // Lo mandamos de vuelta a la galería de mascotas para que lo intente de nuevo
    header('Location: ../views/public/mascotas.php');
    exit();
}
