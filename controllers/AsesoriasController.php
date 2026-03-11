<?php
require_once('models/AsesoriasModel.php');

class AsesoriasController {
    private $model;

    public function __construct() {
        $this->model = new AsesoriasModel();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $matricula = $_SESSION['matricula'] ?? '20250001';
        
        $alumnoInfo = $this->model->getAlumnoHeaderData($matricula);
        $asesorias = $this->model->getAsesorias();
        
        $tabla = $this->construirTablaHorario($asesorias);
        $horas = $this->obtenerHorasOrdenadas($tabla);

        $periodo = "Agosto 2025 - Diciembre 2025";
        require('views/users/asesoriasView.php');
    }

    private function construirTablaHorario($asesorias) {
        $map = ['Lunes'=>1,'Martes'=>2,'Miércoles'=>3,'Miercoles'=>3,'Jueves'=>4,'Viernes'=>5];
        $tabla = [];
        foreach ($asesorias as $row) {
            $dia = $map[$row['dia_semana']] ?? null;
            if (!$dia) continue;
            $hora = substr($row['hora_inicio'],0,5)." - ".substr($row['hora_fin'],0,5);
            $tabla[$hora][$dia] = ['materia'=>$row['materia'],'profesor'=>$row['profesor'],'lugar'=>$row['lugar']];
        }
        return $tabla;
    }

    private function obtenerHorasOrdenadas($tabla) {
        $horas = array_keys($tabla);
        usort($horas, fn($a, $b) => strcmp(explode(' - ', $a)[0], explode(' - ', $b)[0]));
        return $horas;
    }
}
