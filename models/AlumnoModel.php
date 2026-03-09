<?php

require_once __DIR__ . '/../db/Conexiondb.php';
require_once __DIR__ . '/../entitys/AlumnoEntity.php';

class AlumnoModel extends Conexiondb
{
    /**
     * Obtiene datos del alumno y usuario ligado por matrícula.
     * Versión optimizada para SonarQube (Complejidad < 5).
     */
    public function getAlumnoByMatricula(string $matricula)
    {
        $db = parent::conectardb();
        if (!$db) return false;

        parent::setNames();

        $sql = "SELECT 
                    A.id_alumno, A.matricula, A.nombre, A.apellido_paterno, A.apellido_materno,
                    A.fecha_ingreso, A.telefono, A.id_carrera, A.id_grupo, A.id_usuario,
                    U.nombre_usuario, U.email, U.password AS password_hash,
                    U.id_rol, U.id_estado, U.foto_perfil
                FROM alumnos A
                INNER JOIN usuarios U ON A.id_usuario = U.id_usuario
                WHERE A.matricula = :matricula
                LIMIT 1";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $data ?: false; // Retorna los datos o false si no hay nada

        } catch (PDOException $e) {
            error_log("Error getAlumnoByMatricula(): " . $e->getMessage());
            return false;
        } finally {
            // Usamos finally para asegurar que siempre se cierre la conexión
            parent::desconectardb();
        }
    }

    // Métodos vacíos movidos al final para mantener orden
    public function auth() {}
    public function validador() {}
    public function forget() {}
    public function resetPassword() {}
}
