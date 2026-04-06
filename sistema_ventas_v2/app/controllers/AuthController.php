<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuario;
    
    public function __construct() {
        $this->usuario = new Usuario();
    }
    
    public function login() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $user = $this->usuario->findByUsername($username);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_username'] = $user['username'];
                $_SESSION['rol'] = $user['rol'];
                
                // Actualizar último login
                $this->usuario->actualizarUltimoLogin($user['id']);
                
                // Verificar si debe cambiar contraseña
                if ($user['debe_cambiar_password']) {
                    redirect('usuarios/cambiar_password');
                } else {
                    redirect('dashboard');
                }
            } else {
                $error = 'Usuario o contraseña incorrectos';
            }
        }
        
        $title = 'Iniciar Sesión';
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function logout() {
        session_destroy();
        redirect('auth/login');
    }
}
?>