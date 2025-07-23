<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// ========= PASO 1: VALIDACIÓN ESTRICTA DE ENTRADA =========
// Si la petición no es POST, o falta alguno de los dos datos, detenemos todo.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_solicitud']) || empty($_POST['accion'])) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Petición inválida o datos incompletos.'];
    header('Location: ../views/admin/gestionar_solicitudes.php');
    exit();
}

// ========= PASO 2: ASIGNAR VARIABLES Y DETERMINAR EL ESTADO =========
$id_solicitud = $_POST['id_solicitud'];
$accion = trim(strtolower($_POST['accion'])); // Limpiamos y convertimos a minúsculas para evitar errores
$estado_nuevo = null; // Inicializamos como NULL para seguridad

// Comprobamos el valor de $accion y asignamos el estado correspondiente
if ($accion === 'aprobar') {
    $estado_nuevo = 'aprobada';
} elseif ($accion === 'rechazar') {
    $estado_nuevo = 'rechazada';
}

// ========= PASO 3: VALIDAR QUE TENGAMOS UN ESTADO VÁLIDO =========
// Si después de los 'if', $estado_nuevo sigue siendo null, algo falló.
if ($estado_nuevo === null) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Acción desconocida. Se recibió "' . htmlspecialchars($accion) . '".'];
    header('Location: ../views/admin/gestionar_solicitudes.php');
    exit();
}

// ========= PASO 4: EJECUTAR LA LÓGICA DE LA BASE DE DATOS =========
try {
    $db = (new Database())->connect();

    // Llamada al procedimiento almacenado
    $stmt = $db->prepare("CALL procesar_solicitud_adopcion(:p_id_solicitud, :p_nuevo_estado)");
    $stmt->bindParam(':p_id_solicitud', $id_solicitud, PDO::PARAM_INT);
    $stmt->bindParam(':p_nuevo_estado', $estado_nuevo, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION['message'] = [
        'type' => 'success',
        'text' => "La solicitud #{$id_solicitud} ha sido marcada como '{$estado_nuevo}' exitosamente."
    ];
} catch (PDOException $e) {
    // Capturamos cualquier error de la base de datos
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Error al procesar en la BD: ' . $e->getMessage()
    ];
}

// ========= PASO 5: REDIRIGIR AL USUARIO =========
header('Location: ../views/admin/gestionar_solicitudes.php');
exit();
