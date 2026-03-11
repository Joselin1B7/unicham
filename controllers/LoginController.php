<?php
require_once __DIR__ . '/../utils/View.php';
require_once __DIR__ . '/../models/AlumnoModel.php';

class LoginController {
    
    public function auth() {
        View::render("Inicio/authView", ["user" => 0]);
    }

    public function validador() {
        header('Content-Type: application/json');
        $mat = $_POST['matricula'] ?? null;
        $pwd = $_POST['password'] ?? null;

        if (!$mat || !$pwd) {
            return $this->enviarJson(-1, 'Faltan datos.');
        }

        $model = new AlumnoModel();
        $user = $model->getAlumnoByMatricula($mat);

        if (!$user || !password_verify($pwd, $user['password_hash'])) {
            return $this->enviarJson(0, 'Credenciales incorrectas.');
        }

        $this->iniciarSesion($user);
        return $this->enviarJson(1, 'OK', ['redirect_url' => '/UniCham/index.php?controller=user&method=perfil']);
    }

    private function iniciarSesion($data) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_id'] = $data['id_usuario'];
        $_SESSION['matricula'] = $data['matricula'];
    }

    private function enviarJson($status, $msg, $extra = []) {
        echo json_encode(array_merge(['status' => $status, 'message' => $msg], $extra));
        exit();
    }
}
