<?php
// ===== PASO 1: DEFINIR TÍTULO E INCLUIR HEADER =====
$page_title = 'Mis Solicitudes';
include 'layouts/header_user.php';

// ===== PASO 2: LÓGICA PHP PARA OBTENER DATOS =====
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Solicitud.php';

$db = (new Database())->connect();
$solicitud_model = new Solicitud($db);

// Obtenemos el ID del usuario directamente de la sesión
$id_usuario_logueado = $_SESSION['user_id'];
// Llamamos al método pasándole directamente el ID del usuario logueado
$solicitudes = $solicitud_model->getByUserId($id_usuario_logueado);
?>

<!--
    AQUÍ EMPIEZA LA PARTE VISUAL (HTML)
    Nota: El <html>, <head> y <body> ya están en header_user.php
-->

<!-- Estilos específicos de esta página -->
<style>
    .card {
        background: var(--card-bg-color);
        padding: 2rem;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
    }

    h1 {
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 2rem;
    }

    .solicitud-list {
        list-style: none;
        padding: 0;
    }

    .solicitud-item {
        display: flex;
        align-items: center;
        background: #fff;
        margin-bottom: 1rem;
        padding: 1rem;
        border-radius: 8px;
        border-left: 5px solid;
        transition: box-shadow 0.3s;
    }

    .solicitud-item:hover {
        box-shadow: var(--shadow);
    }

    .solicitud-item img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1.5rem;
    }

    .solicitud-info h3 {
        margin: 0 0 0.5rem 0;
        color: var(--primary-color);
    }

    .solicitud-info p {
        margin: 0;
        color: #666;
    }

    .status-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        color: white;
        font-size: 0.9em;
        font-weight: 600;
    }

    .solicitud-item.estado-pendiente {
        border-color: var(--secondary-color);
    }

    .solicitud-item.estado-aprobada {
        border-color: var(--success-color);
    }

    .solicitud-item.estado-rechazada {
        border-color: var(--danger-color);
    }

    .status-pendiente {
        background-color: var(--secondary-color);
    }

    .status-aprobada {
        background-color: var(--success-color);
    }

    .status-rechazada {
        background-color: var(--danger-color);
    }

    .no-solicitudes {
        text-align: center;
        padding: 2rem;
    }

    .btn-link {
        display: inline-block;
        background: var(--secondary-color);
        color: #fff;
        padding: 0.8rem 1.5rem;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
    }
</style>

<div class="card">
    <h1>Historial de Mis Solicitudes</h1>
    <!-- ===== BLOQUE PARA MOSTRAR NOTIFICACIONES ===== -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
            <?php echo $_SESSION['message']['text'];
            unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <!-- ============================================= -->

    <?php if ($solicitudes->rowCount() > 0): ?>
        <ul class="solicitud-list">
            <?php while ($row = $solicitudes->fetch(PDO::FETCH_ASSOC)): ?>
                <li class="solicitud-item estado-<?php echo htmlspecialchars($row['estado']); ?>">
                    <img src="../../public/uploads/<?php echo htmlspecialchars($row['foto_mascota']); ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre_mascota']); ?>">
                    <div class="solicitud-info">
                        <h3><?php echo htmlspecialchars($row['nombre_mascota']); ?></h3>
                        <p>Fecha de solicitud: <?php echo date('d/m/Y', strtotime($row['fecha_solicitud'])); ?></p>
                    </div>
                    <div style="margin-left: auto;">
                        <span class="status-badge status-<?php echo htmlspecialchars($row['estado']); ?>">
                            <?php echo ucfirst(htmlspecialchars($row['estado'])); ?>
                        </span>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="no-solicitudes">
            <h3>Aún no has realizado ninguna solicitud.</h3>
            <p>¡Anímate a conocer a nuestros amigos!</p>
            <a href="mascotas.php" class="btn-link" style="margin-top: 1rem;">Ver Mascotas Disponibles</a>
        </div>
    <?php endif; ?>
</div>