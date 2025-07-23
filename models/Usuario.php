<?php
class Usuario
{
    private $conn;
    private $table = 'usuarios';

    public $id;
    public $nombre;
    public $email;
    public $contrasena; // <-- Añadimos esta propiedad para el registro
    public $rol;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Comprueba si un email ya existe en la base de datos.
     * Esencial para evitar registros duplicados.
     * @return bool true si el email existe, false si no.
     */
    public function emailExists()
    {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        // Si rowCount() es mayor que 0, significa que encontró un usuario.
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * REGISTRA un nuevo usuario en la base de datos.
     * Esta función ahora hashea la contraseña de forma segura.
     * @return bool true si el registro fue exitoso, false si falló.
     */
    public function register()
    {
        $query = 'INSERT INTO ' . $this->table . ' (nombre, email, contrasena, rol) VALUES (:nombre, :email, :contrasena, :rol)';
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->rol = 'usuario'; // Todos los registros nuevos son 'usuario'

        // ===== SEGURIDAD: Hashear la contraseña =====
        // Nunca guardes contraseñas en texto plano.
        $this->contrasena = password_hash($this->contrasena, PASSWORD_BCRYPT);

        // Vincular parámetros
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contrasena', $this->contrasena);
        $stmt->bindParam(':rol', $this->rol);

        return $stmt->execute();
    }

    // --- Las otras funciones como findByEmail() pueden permanecer aquí ---
    public function findByEmail($email)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Valida las credenciales de un usuario para el inicio de sesión.
     * @return array|false Retorna los datos del usuario si las credenciales son correctas, de lo contrario retorna false.
     */
    public function login()
    {
        // 1. Buscar al usuario por email
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            // Se encontró al usuario, ahora obtenemos sus datos
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Verificar la contraseña hasheada
            // La contraseña proporcionada en el formulario ($this->contrasena) se compara con la de la BD ($usuario['contrasena'])
            if (password_verify($this->contrasena, $usuario['contrasena'])) {
                // La contraseña es correcta, devolvemos los datos del usuario
                return $usuario;
            }
        }

        // Si el usuario no se encuentra o la contraseña es incorrecta, retornamos false
        return false;
    }
}
