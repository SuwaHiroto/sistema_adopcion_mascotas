<?php
// ===== PASO 1: DEFINIR TÍTULO E INCLUIR HEADER =====
$page_title = 'Mascotas Disponibles';
include 'layouts/header_user.php';

// ===== PASO 2: LÓGICA DE LA PÁGINA =====
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/MascotaModel.php';

$db = (new Database())->connect();
$mascota_model = new Mascota($db);
$lista_mascotas = $mascota_model->getDisponibles();
$num_mascotas = $lista_mascotas->rowCount();

// Obtenemos el nombre del usuario desde la sesión para usarlo en el autocompletado
$nombre_usuario_logueado = $_SESSION['user_nombre'] ?? '';
?>

<!-- Estilos específicos de esta página -->
<style>
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://source.unsplash.com/1600x600/?pets,happy') no-repeat center center/cover;
        padding: 4rem 2rem;
        text-align: center;
        color: white;
        margin: -2rem -2rem 2rem -2rem;
    }

    .hero h1 {
        font-size: 3rem;
        margin: 0;
        font-weight: 700;
    }

    .hero p {
        font-size: 1.2rem;
        margin-top: 0.5rem;
    }

    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .gallery-title {
        text-align: center;
        color: var(--primary-color);
        font-size: 2.5rem;
        margin-bottom: 3rem;
    }

    .pet-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2.5rem;
    }

    .pet-card {
        background: var(--card-bg-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .pet-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .pet-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .pet-card-body {
        padding: 1.5rem;
    }

    .pet-card-body h3 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    .pet-card-body .details {
        color: #777;
        margin-bottom: 1rem;
    }

    .pet-card-body .description {
        margin-bottom: 1.5rem;
        min-height: 60px;
    }

    .btn {
        display: block;
        width: 100%;
        text-align: center;
        background: var(--secondary-color);
        color: #fff;
        padding: 0.8rem;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn:hover {
        background: #e0940e;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: #fff;
        margin: auto;
        padding: 2rem;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        position: relative;
        animation: slide-down 0.5s ease-out;
    }

    @keyframes slide-down {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 20px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-btn:hover {
        color: #333;
    }

    .modal-pet-info {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .modal-pet-info img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
    }

    .modal-pet-info h3 {
        margin-top: 10px;
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

    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 5px;
        color: #fff;
    }

    .alert-success {
        background-color: #28a745;
    }
</style>

<div class="hero">
    <h1>Adopta un Amigo</h1>
    <p>Encuentra a tu compañero de vida. Ellos te están esperando.</p>
</div>

<div class="container">
    <?php if (isset($_SESSION['message']) && $_SESSION['message']['type'] === 'success'): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']['text'];
            unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <h2 class="gallery-title">Nuestros Peludos Disponibles</h2>

    <?php if ($num_mascotas > 0): ?>
        <div class="pet-gallery">
            <?php while ($row = $lista_mascotas->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="pet-card">
                    <img src="../../public/uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>">
                    <div class="pet-card-body">
                        <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                        <p class="details"><strong><?php echo htmlspecialchars($row['especie']); ?></strong> | Edad: <?php echo htmlspecialchars($row['edad']); ?> años</p>
                        <p class="description"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        <button class="btn btn-adopt"
                            data-id="<?php echo $row['id']; ?>"
                            data-nombre-mascota="<?php echo htmlspecialchars($row['nombre']); ?>"
                            data-foto-mascota="../../public/uploads/<?php echo htmlspecialchars($row['foto']); ?>"
                            data-nombre-usuario="<?php echo htmlspecialchars($nombre_usuario_logueado); ?>">
                            ¡Adóptame!
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-pets" style="background: #fff; padding: 2rem; border-radius: 10px; text-align: center;">
            <h3>¡Vaya! Parece que todos nuestros amigos han encontrado un hogar.</h3>
            <p>Vuelve pronto para conocer a nuevas mascotas.</p>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================================= -->
<!-- ===== FORMULARIO Y MODAL CORREGIDOS Y VERIFICADOS ===== -->
<!-- ========================================================= -->
<div id="adoptionModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">×</span>
        <div class="modal-pet-info">
            <img id="modalPetImage" src="" alt="Foto de la mascota">
            <h3>Solicitud de adopción para <span id="modalPetName"></span></h3>
        </div>

        <form action="../../controllers/SolicitudController.php" method="POST">
            <!-- El 'name' aquí es CRÍTICO. Debe ser "id_mascota" -->
            <input type="hidden" name="id_mascota" id="modalPetIdInput">

            <h4>Tus Datos</h4>
            <div class="form-group">
                <label for="nombre">Nombre (Ya registrado)</label>
                <input type="text" id="modalUserName" readonly style="background-color: #e9ecef;">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono de Contacto</label>
                <!-- name="telefono" -->
                <input type="tel" name="telefono" placeholder="Ej: 600 123 456" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección Completa</label>
                <!-- name="direccion" -->
                <input type="text" name="direccion" placeholder="Ej: Calle de la Alegría, 123, 1A" required>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 2rem 0;">
            <h4>Sobre el Futuro Hogar</h4>
            <div class="form-group">
                <label for="tipo_vivienda">¿Dónde vivirá la mascota?</label>
                <!-- name="tipo_vivienda" -->
                <select name="tipo_vivienda" required>
                    <option value="" disabled selected>Selecciona una opción...</option>
                    <option value="casa_con_jardin">Casa con jardín</option>
                    <option value="casa_sin_jardin">Casa sin jardín</option>
                    <option value="apartamento">Apartamento / Piso</option>
                    <option value="finca_rural">Finca rural</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="experiencia_previa">¿Tienes experiencia previa con mascotas?</label>
                <!-- name="experiencia_previa" -->
                <textarea name="experiencia_previa" rows="3" placeholder="Ej: Tuve un perro durante 10 años..." required></textarea>
            </div>
            <div class="form-group">
                <label for="motivo_adopcion">¿Por qué te gustaría adoptar a esta mascota?</label>
                <!-- name="motivo_adopcion" -->
                <textarea name="motivo_adopcion" rows="4" placeholder="Cuéntanos un poco sobre ti..." required></textarea>
            </div>
            <button type="submit" class="btn">Confirmar y Enviar Solicitud</button>
        </form>
    </div>
</div>

<!-- JavaScript Actualizado -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('adoptionModal');
        if (modal) {
            const closeModalBtn = modal.querySelector('.close-btn');
            const modalPetImage = document.getElementById('modalPetImage');
            const modalPetName = document.getElementById('modalPetName');
            const modalPetIdInput = document.getElementById('modalPetIdInput');
            const modalUserName = document.getElementById('modalUserName');
            const adoptButtons = document.querySelectorAll('.btn-adopt');

            adoptButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const petId = this.dataset.id;
                    const petName = this.dataset.nombreMascota;
                    const petPhoto = this.dataset.fotoMascota;
                    const userName = this.dataset.nombreUsuario;

                    modalPetImage.src = petPhoto;
                    modalPetName.textContent = petName;
                    modalPetIdInput.value = petId;
                    modalUserName.value = userName;

                    modal.classList.add('active');
                });
            });

            function closeModal() {
                modal.classList.remove('active');
            }

            closeModalBtn.addEventListener('click', closeModal);
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            });
        }
    });
</script>
