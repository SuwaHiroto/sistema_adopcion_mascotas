<?php
class Mascota
{
    private $conn;
    private $table = 'mascotas';

    public $id;
    public $nombre;
    public $especie;
    public $raza;
    public $edad;
    public $descripcion;
    public $foto;
    public $estado;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY id DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // ================================================================
    // ======> FUNCIÓN QUE FALTABA Y QUE SOLUCIONA EL ERROR <======
    // ================================================================
    /**
     * LEER todas las mascotas DISPONIBLES para el público
     */
    public function getDisponibles()
    {
        // La consulta solo selecciona mascotas con estado 'disponible'
        $query = 'SELECT * FROM ' . $this->table . " WHERE estado = 'disponible' ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    // ================================================================

    public function getById($id)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->especie = $row['especie'];
            $this->raza = $row['raza'];
            $this->edad = $row['edad'];
            $this->descripcion = $row['descripcion'];
            $this->foto = $row['foto'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' (nombre, especie, raza, edad, descripcion, foto, estado) VALUES (:nombre, :especie, :raza, :edad, :descripcion, :foto, :estado)';
        $stmt = $this->conn->prepare($query);
        $this->cleanData();

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':especie', $this->especie);
        $stmt->bindParam(':raza', $this->raza);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':estado', $this->estado);

        return $stmt->execute();
    }

    public function update()
    {
        $query = 'UPDATE ' . $this->table . ' SET nombre = :nombre, especie = :especie, raza = :raza, edad = :edad, descripcion = :descripcion, foto = :foto, estado = :estado WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->cleanData();

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':especie', $this->especie);
        $stmt->bindParam(':raza', $this->raza);
        $stmt->bindParam(':edad', $this->edad);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':estado', $this->estado);

        return $stmt->execute();
    }

public function delete()
{
    // Iniciar una transacción para asegurar que todo o nada se ejecute
    $this->conn->beginTransaction();

    try {
        // 1. Primero, eliminar los registros en la tabla dependiente (log_adopciones)
        // Asegúrate que la columna de la mascota en log_adopciones se llame 'fk_mascota'
        $query_log = 'DELETE FROM log_adopciones WHERE fk_mascota = :id';
        $stmt_log = $this->conn->prepare($query_log);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt_log->bindParam(':id', $this->id);
        $stmt_log->execute();

        // 2. Ahora, eliminar la mascota de la tabla principal
        $query_mascota = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt_mascota = $this->conn->prepare($query_mascota);
        $stmt_mascota->bindParam(':id', $this->id);
        $stmt_mascota->execute();

        // Si ambas consultas fueron exitosas, confirma los cambios
        $this->conn->commit();
        return true;

    } catch (PDOException $e) {
        // Si algo falló, revierte todos los cambios para no dejar datos inconsistentes
        $this->conn->rollBack();
        // Opcional: puedes registrar el error en un log para depuración
        // error_log($e->getMessage());
        return false;
    }
}

    private function cleanData()
    {
        $this->id = htmlspecialchars(strip_tags($this->id ?? ''));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->especie = htmlspecialchars(strip_tags($this->especie));
        $this->raza = htmlspecialchars(strip_tags($this->raza));
        $this->edad = htmlspecialchars(strip_tags($this->edad));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->foto = htmlspecialchars(strip_tags($this->foto));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
    }
}
