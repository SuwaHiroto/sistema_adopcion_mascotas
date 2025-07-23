<?php

class Solicitud
{
    private $conn;
    private $table = 'solicitudes_adopcion';

    // Propiedades
    public $id;
    public $id_usuario;
    public $id_mascota;
    public $estado;
    public $telefono;
    public $direccion;
    public $tipo_vivienda;
    public $experiencia_previa;
    public $motivo_adopcion;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Crea una nueva solicitud de adopción con todos los detalles.
     */
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (id_usuario, id_mascota, estado, telefono, direccion, tipo_vivienda, experiencia_previa, motivo_adopcion) 
                  VALUES 
                  (:id_usuario, :id_mascota, :estado, :telefono, :direccion, :tipo_vivienda, :experiencia_previa, :motivo_adopcion)';

        $stmt = $this->conn->prepare($query);

        // Limpiamos los datos que vienen del formulario
        $this->cleanData();

        // ================================================================
        // ======> CORRECCIÓN: Asignamos el valor por defecto DESPUÉS de limpiar <======
        // ================================================================
        $this->estado = 'pendiente';

        // Vinculación de parámetros
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':id_mascota', $this->id_mascota);
        $stmt->bindParam(':estado', $this->estado); // Ahora $this->estado SIEMPRE será 'pendiente'
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':tipo_vivienda', $this->tipo_vivienda);
        $stmt->bindParam(':experiencia_previa', $this->experiencia_previa);
        $stmt->bindParam(':motivo_adopcion', $this->motivo_adopcion);

        return $stmt->execute();
    }

    /**
     * Obtiene solicitudes detalladas para el administrador.
     */
    public function getDetalladas($estadoFiltro = null)
    {
        $query = 'SELECT s.id, s.fecha_solicitud, s.estado, s.telefono, s.direccion, s.tipo_vivienda, s.experiencia_previa, s.motivo_adopcion, u.nombre AS nombre_usuario, u.email AS email_usuario, m.nombre AS nombre_mascota, m.id AS id_mascota FROM ' . $this->table . ' s JOIN usuarios u ON s.id_usuario = u.id JOIN mascotas m ON s.id_mascota = m.id';
        if ($estadoFiltro !== null) {
            $query .= ' WHERE s.estado = :estado';
        }
        $query .= ' ORDER BY s.fecha_solicitud DESC';
        $stmt = $this->conn->prepare($query);
        if ($estadoFiltro !== null) {
            $stmt->bindParam(':estado', $estadoFiltro);
        }
        $stmt->execute();
        return $stmt;
    }

    // ================================================================
    // ======> ESTA ES LA FUNCIÓN QUE FALTA Y SOLUCIONA AMBOS PROBLEMAS <======
    // ================================================================
    /**
     * Obtiene todas las solicitudes de un usuario específico.
     * @param int $id_usuario El ID del usuario.
     * @return PDOStatement
     */
    public function getByUserId($id_usuario)
    {
        $query = 'SELECT 
                    s.fecha_solicitud, 
                    s.estado,
                    m.nombre AS nombre_mascota,
                    m.foto AS foto_mascota
                  FROM 
                    ' . $this->table . ' s
                  JOIN 
                    mascotas m ON s.id_mascota = m.id
                  WHERE 
                    s.id_usuario = :id_usuario
                  ORDER BY 
                    s.fecha_solicitud DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Función de limpieza de datos (privada)
    private function cleanData()
    {
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->id_mascota = htmlspecialchars(strip_tags($this->id_mascota));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->tipo_vivienda = htmlspecialchars(strip_tags($this->tipo_vivienda));
        $this->experiencia_previa = htmlspecialchars(strip_tags($this->experiencia_previa));
        $this->motivo_adopcion = htmlspecialchars(strip_tags($this->motivo_adopcion));
    }
}
