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

        // Delegamos el cálculo a un método privado para no inflar la complejidad de talleres()
        $metricas = $this->procesarMetricasTalleres($talleres, $periodo);

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
        
        // Delegamos el resumen académico
        $resumen = $this->calcularResumenAcademico($historial);

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

    private function procesarMetricasTalleres($talleres, $periodo) {
        $total = 0; $esteCuatri = 0;
        $ini = $periodo['fecha_inicio'] ?? null;
        $fin = $periodo['fecha_fin'] ?? null;

        foreach ($talleres as $t) {
            $horas = (int)($t['horas_acumuladas'] ?? 0);
            $total += $horas;
            if ($ini && $fin && !empty($t['fecha_inscripcion'])) {
                $fecha = substr($t['fecha_inscripcion'], 0, 10);
                if ($fecha >= $ini && $fecha <= $fin) $esteCuatri += $horas;
            }
        }
        return ["horasTotalesAcumuladas" => $total, "horasEsteCuatri" => $esteCuatri, "restantes" => max(0, 150 - $total)];
    }

    private function calcularResumenAcademico($historial) {
        $suma = 0; $conteo = 0; $creditos = 0;
        foreach ($historial as $m) {
            if (is_numeric($m['calificacion'])) {
                $suma += $m['calificacion'];
                $conteo++;
            }
            if ($m['estatus'] === 'Aprobada') $creditos += ($m['creditos'] ?? 0);
        }
        return ["promedio" => ($conteo > 0) ? round($suma / $conteo, 2) : 0, "creditos" => $creditos];
    }
}
