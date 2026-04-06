<?php
require_once 'config/database.php';

$db = Database::getInstance();

echo "<h1>🔧 Reparación de Login</h1>";

// Verificar conexión
echo "<h2>1. Verificando conexión...</h2>";
try {
    $db->query("SELECT 1");
    echo "✅ Conexión a la base de datos OK<br>";
} catch (Exception $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

// Verificar si la tabla usuarios existe
echo "<h2>2. Verificando tabla usuarios...</h2>";
$tabla = $db->fetchOne("SHOW TABLES LIKE 'usuarios'");
if (!$tabla) {
    echo "❌ Tabla 'usuarios' no existe. Creándola...<br>";
    $db->query("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            rol ENUM('admin', 'vendedor') DEFAULT 'vendedor',
            activo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_username (username)
        ) ENGINE=InnoDB
    ");
    echo "✅ Tabla 'usuarios' creada<br>";
} else {
    echo "✅ Tabla 'usuarios' existe<br>";
}

// Verificar usuarios existentes
echo "<h2>3. Usuarios en la base de datos:</h2>";
$usuarios = $db->fetchAll("SELECT id, username, nombre, rol FROM usuarios");
if (empty($usuarios)) {
    echo "No hay usuarios registrados.<br>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Nombre</th><th>Rol</th></tr>";
    foreach ($usuarios as $u) {
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['username']}</td>";
        echo "<td>{$u['nombre']}</td>";
        echo "<td>{$u['rol']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Crear/Actualizar usuario admin
echo "<h2>4. Creando/Actualizando usuario admin...</h2>";

// Hash de la contraseña 'admin123'
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

// Verificar si ya existe admin
$admin = $db->fetchOne("SELECT * FROM usuarios WHERE username = 'admin'");

if ($admin) {
    echo "Usuario 'admin' ya existe. Actualizando contraseña...<br>";
    $db->query("UPDATE usuarios SET password = :pass WHERE username = 'admin'", [':pass' => $hash]);
    echo "✅ Contraseña actualizada a 'admin123'<br>";
} else {
    echo "Creando usuario 'admin'...<br>";
    $db->query("
        INSERT INTO usuarios (username, password, nombre, rol, activo) 
        VALUES ('admin', :pass, 'Administrador', 'admin', 1)
    ", [':pass' => $hash]);
    echo "✅ Usuario 'admin' creado con contraseña 'admin123'<br>";
}

// Verificar que funciona
echo "<h2>5. Verificando login...</h2>";
$test = $db->fetchOne("SELECT * FROM usuarios WHERE username = 'admin'");
if ($test) {
    echo "✅ Usuario encontrado en BD<br>";
    if (password_verify('admin123', $test['password'])) {
        echo "✅ Contraseña 'admin123' VERIFICADA correctamente<br>";
    } else {
        echo "❌ Contraseña NO coincide. Reemplazando...<br>";
        $db->query("UPDATE usuarios SET password = :pass WHERE username = 'admin'", [':pass' => $hash]);
        echo "✅ Contraseña reestablecida a 'admin123'<br>";
    }
}

echo "<hr>";
echo "<h2>✅ Listo! Ahora puedes iniciar sesión:</h2>";
echo "<p><strong>Usuario:</strong> admin</p>";
echo "<p><strong>Contraseña:</strong> admin123</p>";
echo "<a href='" . BASE_URL . "auth/login'>🔐 Ir al Login</a>";