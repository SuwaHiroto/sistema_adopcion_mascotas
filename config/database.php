<?php
class Database {
    // --- MODIFICA ESTOS DATOS CON LOS TUYOS ---
    private $host = 'dpg-d20e2ni4d50c73b1p230-a.oregon-postgres.render.com';          // Host de PostgreSQL
    private $db_name = 'adopcion_hmvb';     // Nombre de tu base de datos
    private $username = 'adopcion_hmvb_user';       // Tu usuario de PostgreSQL
    private  $password = '0PqYnDVvG6034tX6NVJwFbDGFtp0zEk4';  // Tu contraseña de PostgreSQL
    private $port = '5432';               // Puerto (usualmente 5432)
    // -----------------------------------------

    private $conn;

    public function connect() {
        if ($this->conn) {
            return $this->conn;
        }

        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die('Error de Conexión: ' . $e->getMessage());
        }

        return $this->conn;
    }
}
?>  
