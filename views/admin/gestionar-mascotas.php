<?php
// Iniciar sesión para poder usar variables de sesión para los mensajes
session_start();

// Incluir dependencias
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/MascotaModel.php';

// --- LÓGICA DEL CONTROLADOR ---
// Toda esta sección se ejecuta ANTES de enviar cualquier contenido HTML al navegador.

$db = (new Database())->connect();
$mascota = new Mascota($db);
$upload_dir = __DIR__ . '/../../public/uploads/';

// Función para manejar la subida de la foto
function manejarSubidaFoto($file_input, $upload_dir)
{
    if (isset($file_input) && $file_input['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $nombre_archivo = uniqid('mascota_', true) . '.' . strtolower(pathinfo($file_input['name'], PATHINFO_EXTENSION));
        if (move_uploaded_file($file_input['tmp_name'], $upload_dir . $nombre_archivo)) {
            return $nombre_archivo;
        }
    }
    return null;
}

// Manejar peticiones POST (Crear y Actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $mascota->nombre = $_POST['nombre'];
    $mascota->especie = $_POST['especie'];
    $mascota->raza = $_POST['raza'];
    $mascota->edad = $_POST['edad'];
    $mascota->descripcion = $_POST['descripcion'];
    $mascota->estado = $_POST['estado'];

    if ($action === 'create') {
        $mascota->foto = manejarSubidaFoto($_FILES['foto'], $upload_dir) ?? 'default.png';
        if ($mascota->create()) {
            $_SESSION['message'] = ['text' => 'Mascota agregada exitosamente.', 'type' => 'success'];
        } else {
            $_SESSION['message'] = ['text' => 'Error al agregar la mascota.', 'type' => 'danger'];
        }
    } elseif ($action === 'update') {
        $mascota->id = $_POST['id'];
        $nombre_foto_nueva = manejarSubidaFoto($_FILES['foto'], $upload_dir);
        if ($nombre_foto_nueva) {
            $mascota->foto = $nombre_foto_nueva;
            if ($_POST['foto_actual'] && $_POST['foto_actual'] !== 'default.png' && file_exists($upload_dir . $_POST['foto_actual'])) {
                unlink($upload_dir . $_POST['foto_actual']);
            }
        } else {
            $mascota->foto = $_POST['foto_actual'];
        }
        if ($mascota->update()) {
            $_SESSION['message'] = ['text' => 'Mascota actualizada exitosamente.', 'type' => 'success'];
        } else {
            $_SESSION['message'] = ['text' => 'Error al actualizar la mascota.', 'type' => 'danger'];
        }
    }
    // Redirigir para evitar reenvío de formulario y detener la ejecución del script
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Manejar peticiones GET (Eliminar)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if ($mascota->getById($_GET['id'])) {
        if ($mascota->delete()) {
            if ($mascota->foto && $mascota->foto !== 'default.png' && file_exists($upload_dir . $mascota->foto)) {
                unlink($upload_dir . $mascota->foto);
            }
            $_SESSION['message'] = ['text' => 'Mascota eliminada exitosamente.', 'type' => 'success'];
        } else {
            $_SESSION['message'] = ['text' => 'Error al eliminar la mascota.', 'type' => 'danger'];
        }
    } else {
        $_SESSION['message'] = ['text' => 'Mascota no encontrada.', 'type' => 'danger'];
    }
    // Redirigir para limpiar la URL y detener la ejecución del script
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Preparar datos para la vista (solo si no hubo redirección)
$edit_mode = false;
$mascota_a_editar = new Mascota($db); // Objeto limpio para el formulario
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    if ($mascota_a_editar->getById($_GET['id'])) {
        $edit_mode = true;
    }
}
$lista_mascotas = $mascota->getAll();

// --- FIN LÓGICA DEL CONTROLADOR ---

// *** CAMBIO REALIZADO AQUÍ ***
// El header se incluye DESPUÉS de toda la lógica de redirección, justo antes de empezar a renderizar el HTML.
include 'layouts/header_admin.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mascotas</title>
    <style>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--card-bg-color);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: .5rem;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: .7rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: var(--primary-color);
            color: #fff;
        }

        table th,
        table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            color: #fff;
        }

        .alert-success {
            background-color: var(--success-color);
        }

        .alert-danger {
            background-color: var(--danger-color);
        }

        .status-disponible {
            color: var(--success-color);
            font-weight: bold;
        }

        .status-adoptado {
            color: var(--secondary-color);
            font-weight: bold;
        }

        h2,
        h3 {
            color: var(--primary-color);
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>
            <center>🐾 Gestión de Mascotas 🐾</center>
        </h2>

        <!-- Mensajes de notificación -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
                <?php echo $_SESSION['message']['text'];
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para Agregar o Editar -->
        <div class="card">
            <h3><?php echo $edit_mode ? '✏️ Editar Mascota' : '➕ Agregar Nueva Mascota'; ?></h3>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $mascota_a_editar->id; ?>">
                    <input type="hidden" name="foto_actual" value="<?php echo $mascota_a_editar->foto; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo $edit_mode ? htmlspecialchars($mascota_a_editar->nombre) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="especie">Especie</label>
                    <input type="text" name="especie" value="<?php echo $edit_mode ? htmlspecialchars($mascota_a_editar->especie) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="raza">Raza</label>
                    <input type="text" name="raza" value="<?php echo $edit_mode ? htmlspecialchars($mascota_a_editar->raza) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="edad">Edad (años)</label>
                    <input type="number" name="edad" value="<?php echo $edit_mode ? htmlspecialchars($mascota_a_editar->edad) : '0'; ?>">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" rows="3"><?php echo $edit_mode ? htmlspecialchars($mascota_a_editar->descripcion) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="foto">Foto</label>
                    <input type="file" name="foto" accept="image/*">
                    <?php if ($edit_mode && $mascota_a_editar->foto): ?>
                        <p style="margin-top: 10px;">Foto actual: <img src="../../public/uploads/<?php echo htmlspecialchars($mascota_a_editar->foto); ?>" width="50" alt="Foto"></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado">
                        <option value="disponible" <?php echo ($edit_mode && $mascota_a_editar->estado == 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="adoptado" <?php echo ($edit_mode && $mascota_a_editar->estado == 'adoptado') ? 'selected' : ''; ?>>Adoptado</option>
                    </select>
                </div>

                <button type="submit" class="btn"><?php echo $edit_mode ? 'Actualizar Mascota' : 'Agregar Mascota'; ?></button>
                <?php if ($edit_mode): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancelar Edición</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabla con la Lista de Mascotas -->
        <div class="card">
            <h3>Listado de Mascotas</h3>
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Especie</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $lista_mascotas->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><img src="../../public/uploads/<?php echo htmlspecialchars($row['foto']); ?>?v=<?php echo time(); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>" width="60" style="border-radius: 5px;"></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['especie']); ?></td>
                            <td><span class="status-<?php echo htmlspecialchars($row['estado']); ?>"><?php echo ucfirst(htmlspecialchars($row['estado'])); ?></span></td>
                            <td>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm">Editar</a>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar a esta mascota? Esta acción no se puede deshacer.');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
