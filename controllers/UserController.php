<?php
class UserController {
    private $model;

    public function __construct() {
        require_once 'models/UserModel.php';
        $this->model = new UserModel();
    }

    public function perfil() {
        $this->verificarSesion();
        $perfil = $this->model->getPerfilAlumno($_SESSION['matricula']);
        View::render("users/perfilView", ["perfil" => $perfil]);
    }

    public function talleres() {
        $this->verificarSesion();
        $matricula = $_SESSION['matricula'];
        $talleres = $this->model->getTalleresAlumno($matricula);
        $periodo = $this->model->getPeriodoActual();

        $metricas = $this->calcularMetricasTalleres($talleres, $periodo);

        View::render("users/talleresView", array_merge([
            "perfil" => $this->model->getPerfilAlumno($matricula),
            "talleres" => $talleres,
            "periodoActual" => $periodo,
            "TOTAL_REQUERIDO" => 150
        ], $metricas));
    }

    public function historial() {
        $this->verificarSesion();
        $matricula = $_SESSION['matricula'];
        $historial = $this->model->getHistorialAlumno($matricula);
        
        $resumen = $this->obtenerResumenAcademico($historial);

        View::render("users/historialView", [
            "perfil" => $this->model->getPerfilAlumno($matricula),
            "historial" => $historial,
            "promedio" => $resumen['promedio'],
            "totalCreditos" => $resumen['creditos']
        ]);
    }

    private function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['matricula'])) {
            header('Location: /UniCham/index.php?controller=login&method=auth');
            exit();
        }
    }

    private function calcularMetricasTalleres($talleres, $periodo) {
        $total = 0; $esteCuatri = 0;
        $fInicio = $periodo['fecha_inicio'] ?? null;
        $fFin = $periodo['fecha_fin'] ?? null;

        foreach ($talleres as $t) {
            $h = (int)($t['horas_acumuladas'] ?? 0);
            $total += $h;
            $fechaI = substr($t['fecha_inscripcion'] ?? '', 0, 10);
            if ($fInicio && $fFin && $fechaI >= $fInicio && $fechaI <= $fFin) {
                $esteCuatri += $h;
            }
        }
        return ["horasTotalesAcumuladas" => $total, "horasEsteCuatri" => $esteCuatri, "restantes" => max(0, 150 - $total)];
    }

    private function obtenerResumenAcademico($historial) {
        $suma = 0; $conteo = 0; $creditos = 0;
        foreach ($historial as $m) {
            if (is_numeric($m['calificacion'])) {
                $suma += $m['calificacion'];
                $conteo++;
            }
            if (($m['estatus'] ?? '') === 'Aprobada') {
                $creditos += ($m['creditos'] ?? 0);
            }
        }
        return ["promedio" => ($conteo > 0) ? round($suma / $conteo, 2) : 0, "creditos" => $creditos];
    }
}
