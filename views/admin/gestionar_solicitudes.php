<?php
// ===== PASO 1: DEFINIR EL TÍTULO DE LA PÁGINA =====
// Esta variable será usada por el header.
$page_title = 'Gestión de Solicitudes';

// ===== PASO 2: INCLUIR EL HEADER =====
// Esto se encarga de iniciar la sesión, la seguridad y de mostrar el navbar.
// Ya no necesitas el session_start() al principio de este archivo.
include 'layouts/header_admin.php';


// ===== PASO 3: TU LÓGICA PHP (SIN CAMBIOS) =====
// Esta parte de tu código ya es correcta.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Solicitud.php';

$db = (new Database())->connect();
$solicitud_model = new Solicitud($db);

$filtro_actual = $_GET['filtro'] ?? 'pendiente';
$filtros_permitidos = ['pendiente', 'aprobada', 'rechazada'];
if (!in_array($filtro_actual, $filtros_permitidos)) {
    $filtro_actual = 'pendiente';
}
$solicitudes = $solicitud_model->getDetalladas($filtro_actual);
?>

<!--
    AVISO: Hemos quitado todo el <!DOCTYPE>, <html>, <head> y <body>
    porque ya están incluidos en header_admin.php.
    También hemos movido los estilos a una sección separada para mayor claridad.
-->

<!-- ===== PASO 4: ESTILOS CSS ESPECÍFICOS DE ESTA PÁGINA ===== -->
<style>
    .filter-nav {
        margin-bottom: 2rem;
        text-align: center;
    }

    .filter-nav a {
        text-decoration: none;
        padding: 0.6rem 1.2rem;
        margin: 0 5px;
        border-radius: 5px;
        background-color: #e9ecef;
        color: var(--primary-color);
        font-weight: 600;
        transition: all 0.3s;
    }

    .filter-nav a.active,
    .filter-nav a:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .status-badge {
        padding: 0.2rem 0.6rem;
        border-radius: 10px;
        color: white;
        font-size: 0.8em;
        font-weight: 600;
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

    .details-row {
        background-color: #f0f8ff;
    }

    .details-content {
        padding: 1.5rem;
    }

    .details-content strong {
        color: var(--primary-color);
    }

    .card {
        background: var(--card-bg-color);
        padding: 2rem;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table thead {
        background-color: var(--primary-color);
    }

    table th,
    table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
    }

    h2 {
        color: var(--primary-color);
        text-align: center;
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

    .btn-success {
        background-color: var(--success-color);
    }

    .btn-secondary {
        background-color: #6c757d;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.8em;
    }
</style>

<!-- ===== PASO 5: TU CONTENIDO HTML PRINCIPAL (SIN CAMBIOS) ===== -->
<div class="container">
    <h2><?php echo $page_title; ?></h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
            <?php echo $_SESSION['message']['text'];
            unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="filter-nav">
        <a href="?filtro=pendiente" class="<?php if ($filtro_actual === 'pendiente') echo 'active'; ?>">Pendientes</a>
        <a href="?filtro=aprobada" class="<?php if ($filtro_actual === 'aprobada') echo 'active'; ?>">Aprobadas</a>
        <a href="?filtro=rechazada" class="<?php if ($filtro_actual === 'rechazada') echo 'active'; ?>">Rechazadas</a>
    </div>

    <div class="card">
        <table>
            <!-- Tu tabla completa, sin cambios -->
            <thead>
                <tr>
                    <th>Mascota</th>
                    <th>Solicitante</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($solicitudes->rowCount() > 0): ?>
                    <?php while ($row = $solicitudes->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre_mascota']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_usuario']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($row['estado']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['estado'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['estado'] === 'pendiente'): ?>
                                    <button class="btn btn-sm btn-secondary btn-details" data-target="details-<?php echo $row['id']; ?>">Detalles</button>
                                    <form action="../../controllers/AdminController.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_solicitud" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="accion" value="aprobar">
                                        <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                                    </form>
                                    <form action="../../controllers/AdminController.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_solicitud" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="accion" value="rechazar">
                                        <button type="submit" class="btn btn-sm btn-danger">Rechazar</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary btn-details" data-target="details-<?php echo $row['id']; ?>">Ver Detalles</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="details-row" id="details-<?php echo $row['id']; ?>" style="display: none;">
                            <td colspan="4">
                                <div class="details-content">
                                    <p><strong>Fecha Solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($row['fecha_solicitud'])); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email_usuario']); ?></p>
                                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($row['telefono']); ?></p>
                                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($row['direccion']); ?></p>
                                    <p><strong>Tipo de Vivienda:</strong> <?php echo htmlspecialchars($row['tipo_vivienda']); ?></p>
                                    <p><strong>Experiencia Previa:</strong><br><?php echo nl2br(htmlspecialchars($row['experiencia_previa'])); ?></p>
                                    <p><strong>Motivo de Adopción:</strong><br><?php echo nl2br(htmlspecialchars($row['motivo_adopcion'])); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay solicitudes con el estado "<?php echo $filtro_actual; ?>".</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== PASO 6: TU JAVASCRIPT (SIN CAMBIOS) ===== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.btn-details');
        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const detailsRow = document.getElementById(targetId);
                if (detailsRow.style.display === 'none') {
                    detailsRow.style.display = 'table-row';
                    this.textContent = 'Ocultar';
                } else {
                    detailsRow.style.display = 'none';
                    this.textContent = 'Detalles';
                }
            });
        });
    });
</script>