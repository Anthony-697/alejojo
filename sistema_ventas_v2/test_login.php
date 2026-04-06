<?php
require_once 'config/database.php';

$db = Database::getInstance();

echo "<h1>Prueba de Login Manual</h1>";

// Crear usuario si no existe
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$db->query("DELETE FROM usuarios WHERE username = 'admin'");
$db->query("INSERT INTO usuarios (username, password, nombre, rol, activo) VALUES ('admin', :pass, 'Administrador', 'admin', 1)", [':pass' => $hash]);

echo "Usuario creado con contraseña 'admin123'<br>";

// Probar login
$username = 'admin';
$password = 'admin123';

$user = $db->fetchOne("SELECT * FROM usuarios WHERE username = :username AND activo = 1", [':username' => $username]);

echo "<h2>Resultado de la prueba:</h2>";
if ($user) {
    echo "✅ Usuario encontrado: " . $user['username'] . "<br>";
    echo "Hash almacenado: " . $user['password'] . "<br>";
    
    if (password_verify($password, $user['password'])) {
        echo "✅✅✅ CONTRASEÑA CORRECTA! ✅✅✅<br>";
        
        // Iniciar sesión manualmente
        session_start();
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['usuario_username'] = $user['username'];
        $_SESSION['rol'] = $user['rol'];
        
        echo "<br><a href='" . BASE_URL . "dashboard'>🔐 Ir al Dashboard (ya logueado)</a>";
    } else {
        echo "❌ Contraseña incorrecta<br>";
    }
} else {
    echo "❌ Usuario no encontrado<br>";
}