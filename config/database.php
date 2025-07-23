<?php
class Database {
    // --- MODIFICA ESTOS DATOS CON LOS TUYOS ---
    private $host = 'localhost';          // Host de PostgreSQL
    private $db_name = 'adopcion';     // Nombre de tu base de datos
    private $username = 'postgres';       // Tu usuario de PostgreSQL
    private  $password = '1231';  // Tu contraseña de PostgreSQL
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