<?php
// Incluimos la clase de conexión para heredar sus métodos seguros
require_once __DIR__ . '/../db/Conexiondb.php';

class TeacherModel extends Conexiondb {
    private $db;

    public function __construct() {
        /**
         * LLamamos al método conectardb() de la clase padre.
         * Esto soluciona el issue de seguridad al no exponer 
         * 'root' y contraseñas vacías en este archivo.
         */
        $this->db = parent::conectardb();
        parent::setNames();
    }

    /**
     * Ejemplo de método para obtener datos del profesor
     * Usando sentencias preparadas para evitar Inyección SQL
     */
    public function getPerfilDocente($id_usuario) {
        $sql = "SELECT * FROM profesores WHERE id_usuario = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener las materias asignadas al profesor
     */
    public function getMateriasAsignadas($id_profesor) {
        $sql = "SELECT m.nombre_materia, c.nombre_carrera 
                FROM materias m
                JOIN carreras c ON m.id_carrera = c.id_carrera
                WHERE m.id_profesor = :id_prof";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_prof' => $id_profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
