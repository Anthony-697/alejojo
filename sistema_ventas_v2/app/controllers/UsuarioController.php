<?php
/**
 * Controlador de Usuarios
 * Permite gestionar los usuarios del sistema
 */

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuario;
    
    public function __construct() {
        $this->usuario = new Usuario();
    }
    
    /**
     * Muestra la lista de usuarios (solo admin)
     */
    public function index() {
        requireLogin();
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permiso para ver esta página';
            redirect('dashboard');
        }
        
        $usuarios = $this->usuario->all();
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Usuarios</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
                .navbar{background:linear-gradient(135deg,#1e293b,#0f172a);padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;transition:all 0.3s;}
                .navbar a:hover{background:#334155;transform:translateY(-2px);}
                .logo{color:#22c55e;font-weight:bold;font-size:1.2rem;margin-right:auto;}
                .container{max-width:1200px;margin:0 auto;padding:2rem;}
                .btn{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border-radius:10px;text-decoration:none;color:white;display:inline-flex;align-items:center;gap:8px;margin-bottom:1rem;}
                table{width:100%;border-collapse:collapse;background:#1e293b;border-radius:16px;overflow:hidden;}
                th,td{padding:12px;text-align:center;border-bottom:1px solid #334155;}
                th{background:#334155;}
                .btn-editar{background:#3b82f6;padding:6px 12px;border-radius:8px;text-decoration:none;color:white;}
                .btn-eliminar{background:#ef4444;padding:6px 12px;border-radius:8px;text-decoration:none;color:white;}
                .btn-reset{background:#f59e0b;padding:6px 12px;border-radius:8px;text-decoration:none;color:white;}
                .activo{color:#22c55e;font-weight:bold;}
                .inactivo{color:#ef4444;font-weight:bold;}
                .admin{color:#facc15;font-weight:bold;}
                .vendedor{color:#22c55e;font-weight:bold;}
                .debe-cambiar{color:#f59e0b;font-weight:bold;}
            </style>
        </head>
        <body>
<div class="navbar">
    <div class="logo">🛍️ ALEJOJO V2</div>
    <a href="' . BASE_URL . 'dashboard">🏠 Inicio</a>
    <a href="' . BASE_URL . 'productos">📦 Productos</a>
    <a href="' . BASE_URL . 'clientes">👤 Clientes</a>
    <a href="' . BASE_URL . 'campanas">📢 Campañas</a>
    <a href="' . BASE_URL . 'ventas/crear">🛒 Nueva Venta</a>
    <a href="' . BASE_URL . 'ventas">📊 Historial</a>
    <a href="' . BASE_URL . 'reportes/deudores">💳 Deudores</a>
    <a href="' . BASE_URL . 'reportes/stock">📦 Stock</a>
    <a href="' . BASE_URL . 'entradas/crear">📥 Compras</a>
    <a href="' . BASE_URL . 'usuarios">👥 Usuarios</a>
    <a href="' . BASE_URL . 'promociones">🎯 Promociones</a>

    <div style="margin-left:auto;">
        👤 ' . h($_SESSION['usuario_nombre']) . '
        <a href="' . BASE_URL . 'usuarios/cambiar_password" style="background:#f59e0b; margin-left:10px;">🔒 Cambiar Contraseña</a>
        <a href="' . BASE_URL . 'auth/logout" style="background:#ef4444; margin-left:10px;">🚪 Salir</a>
    </div>
</div>
            
            <div class="container">
                <h2>👥 Gestión de Usuarios</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <a href="' . BASE_URL . 'usuarios/crear" class="btn">➕ Nuevo Usuario</a>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Ult. Login</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($usuarios as $u) {
            $rolClass = $u['rol'] == 'admin' ? 'admin' : 'vendedor';
            $estadoClass = $u['activo'] ? 'activo' : 'inactivo';
            $estadoTexto = $u['activo'] ? 'Activo' : 'Inactivo';
            $debeCambiar = $u['debe_cambiar_password'] ? '<span class="debe-cambiar">⚠️ Pendiente</span>' : '✅';
            $ultimoLogin = $u['ultimo_login'] ? date('d/m/Y H:i', strtotime($u['ultimo_login'])) : 'Nunca';
            
            echo '<tr>
                    <td>' . $u['id'] . '</td>
                    <td>' . h($u['username']) . '</td>
                    <td>' . h($u['nombres']) . '</td>
                    <td>' . h($u['apellidos']) . '</td>
                    <td class="' . $rolClass . '">' . strtoupper($u['rol']) . '</td>
                    <td class="' . $estadoClass . '">' . $estadoTexto . '<br>' . $debeCambiar . '</td>
                    <td>' . $ultimoLogin . '</td>
                    <td class="acciones">
                        <a href="' . BASE_URL . 'usuarios/editar/' . $u['id'] . '" class="btn-editar">✏️ Editar</a>
                        <a href="' . BASE_URL . 'usuarios/reset_password/' . $u['id'] . '" class="btn-reset" onclick="return confirm(\'¿Resetear contraseña?\')">🔄 Reset</a>
                        <a href="' . BASE_URL . 'usuarios/eliminar/' . $u['id'] . '" class="btn-eliminar" onclick="return confirm(\'¿Eliminar este usuario?\')">🗑️ Eliminar</a>
                    </td>
                </tr>';
        }
        
        echo '</tbody>
                    </table>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function crear() {
        requireLogin();
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $mensaje_password = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $password = $_POST['password'] ?? '';
            $es_temporal = isset($_POST['es_temporal']) ? 1 : 0;
            $rol = $_POST['rol'] ?? 'vendedor';
            
            $errors = [];
            if (empty($nombres)) $errors[] = 'Los nombres son requeridos';
            if (empty($apellidos)) $errors[] = 'Los apellidos son requeridos';
            if (empty($password)) $errors[] = 'La contraseña es requerida';
            if (strlen($password) < 4) $errors[] = 'La contraseña debe tener al menos 4 caracteres';
            
            // 🔥 CORRECCIÓN: Tomar solo el primer nombre y primer apellido
            $primerNombre = explode(' ', trim($nombres))[0];
            $primerApellido = explode(' ', trim($apellidos))[0];
            $usernameBase = strtolower($primerNombre) . '.' . strtolower($primerApellido);
            
            // Verificar si ya existe y agregar número si es necesario
            $username = $usernameBase;
            $contador = 1;
            while ($this->usuario->findByUsername($username)) {
                $username = $usernameBase . $contador;
                $contador++;
            }
            
            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (username, password, nombres, apellidos, nombre, rol, activo, debe_cambiar_password) 
                        VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
                $db = Database::getInstance();
                $db->query($sql, [$username, $hash, $nombres, $apellidos, $nombres . ' ' . $apellidos, $rol, $es_temporal]);
                
                $mensaje_password = "✅ Usuario creado correctamente<br>
                                     📛 Usuario: <strong>" . $username . "</strong><br>
                                     🔒 Contraseña: <strong>" . htmlspecialchars($password) . "</strong><br>
                                     " . ($es_temporal ? "⚠️ El usuario deberá cambiar su contraseña al primer inicio de sesión" : "✅ Contraseña permanente, el usuario puede usarla siempre");
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nuevo Usuario</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;}
                .card{background:#1e293b;padding:2rem;border-radius:16px;max-width:550px;margin:2rem auto;}
                input,select{width:100%;padding:10px;margin:10px 0;border-radius:10px;border:none;background:#0f172a;color:white;}
                button{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border:none;border-radius:10px;color:white;cursor:pointer;}
                a{color:#22c55e;text-decoration:none;}
                .alert-error{background:#991b1b;padding:1rem;border-radius:10px;margin-bottom:1rem;}
                .alert-success{background:#166534;padding:1rem;border-radius:10px;margin-bottom:1rem;border-left:4px solid #22c55e;}
                .info-box{background:#0f172a;padding:1rem;border-radius:10px;margin:1rem 0;border-left:4px solid #22c55e;}
                .checkbox{width:auto;margin-right:10px;}
            </style>
        </head>
        <body>
            <div class="card">
                <h2>➕ Nuevo Usuario</h2>';
        
        if (isset($mensaje_password)) {
            echo '<div class="alert-success">' . $mensaje_password . '</div>';
        }
        
        if (isset($_SESSION['errors'])) {
            echo '<div class="alert-error"><ul>';
            foreach ($_SESSION['errors'] as $e) {
                echo '<li>' . h($e) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['errors']);
        }
        
        echo '<div class="info-box">
                    <strong>ℹ️ Información:</strong><br>
                    • El <strong>usuario</strong> se genera automáticamente como: <strong>primerNombre.primerApellido</strong><br>
                    • Ejemplo: Nombres "Ana María", Apellidos "Pérez Gómez" → usuario: <strong>ana.perez</strong><br>
                    • Si marcas <strong>"Contraseña temporal"</strong>, el usuario deberá cambiarla al primer login
                </div>
                
                <form method="POST">
                    <label>📛 Nombres *</label>
                    <input type="text" name="nombres" required autofocus placeholder="Ej: Ana María">

                    
                    <label>📛 Apellidos *</label>
                    <input type="text" name="apellidos" required placeholder="Ej: Pérez Gómez">

                    
                    <label>🔒 Contraseña *</label>
                    <input type="text" name="password" required placeholder="Escribe la contraseña que quieras">
                    
                    <label>
                        <input type="checkbox" name="es_temporal" class="checkbox"> ⚠️ Contraseña temporal (el usuario deberá cambiarla al primer login)
                    </label>
                    
                    <label>🎭 Rol *</label>
                    <select name="rol">
                        <option value="vendedor">👤 Vendedor</option>
                        <option value="admin">👑 Administrador</option>
                    </select>
                    
                    <button type="submit">💾 Crear Usuario</button>
                    <a href="' . BASE_URL . 'usuarios">Cancelar</a>
                </form>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Edita un usuario existente
     */
    public function editar($id) {
        requireLogin();
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $usuario = $this->usuario->find($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            redirect('usuarios');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $rol = $_POST['rol'] ?? 'vendedor';
            $activo = isset($_POST['activo']) ? 1 : 0;
            $password = $_POST['password'] ?? '';
            $es_temporal = isset($_POST['es_temporal']) ? 1 : 0;
            
            $sql = "UPDATE usuarios SET nombres = ?, apellidos = ?, nombre = ?, rol = ?, activo = ?, debe_cambiar_password = ? WHERE id = ?";
            $nombreCompleto = $nombres . ' ' . $apellidos;
            $params = [$nombres, $apellidos, $nombreCompleto, $rol, $activo, $es_temporal, $id];
            
            // Si se ingresó nueva contraseña, actualizarla
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombres = ?, apellidos = ?, nombre = ?, rol = ?, activo = ?, debe_cambiar_password = ?, password = ? WHERE id = ?";
                $params = [$nombres, $apellidos, $nombreCompleto, $rol, $activo, $es_temporal, $hash, $id];
            }
            
            $db = Database::getInstance();
            $db->query($sql, $params);
            $_SESSION['success'] = '✅ Usuario actualizado';
            redirect('usuarios');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Editar Usuario</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;}
                .card{background:#1e293b;padding:2rem;border-radius:16px;max-width:500px;margin:2rem auto;}
                input,select{width:100%;padding:10px;margin:10px 0;border-radius:10px;border:none;background:#0f172a;color:white;}
                button{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border:none;border-radius:10px;color:white;cursor:pointer;}
                a{color:#22c55e;text-decoration:none;}
                .checkbox{width:auto;margin-right:10px;}
            </style>
        </head>
        <body>
            <div class="card">
                <h2>✏️ Editar Usuario: ' . h($usuario['username']) . '</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <form method="POST">
                    <label>📛 Nombres *</label>
                    <input type="text" name="nombres" value="' . h($usuario['nombres']) . '" required>
                    
                    <label>📛 Apellidos *</label>
                    <input type="text" name="apellidos" value="' . h($usuario['apellidos']) . '" required>
                    
                    <label>🔒 Nueva Contraseña (dejar vacío para no cambiar)</label>
                    <input type="text" name="password" placeholder="Escribe nueva contraseña si quieres cambiarla">
                    
                    <label>
                        <input type="checkbox" name="es_temporal" class="checkbox" ' . ($usuario['debe_cambiar_password'] ? 'checked' : '') . '> ⚠️ Contraseña temporal (debe cambiarla al login)
                    </label>
                    
                    <label>🎭 Rol *</label>
                    <select name="rol">
                        <option value="vendedor" ' . ($usuario['rol'] == 'vendedor' ? 'selected' : '') . '>Vendedor</option>
                        <option value="admin" ' . ($usuario['rol'] == 'admin' ? 'selected' : '') . '>Administrador</option>
                    </select>
                    
                    <label>
                        <input type="checkbox" name="activo" class="checkbox" ' . ($usuario['activo'] ? 'checked' : '') . '> Usuario Activo
                    </label>
                    
                    <button type="submit">💾 Actualizar</button>
                    <a href="' . BASE_URL . 'usuarios">Cancelar</a>
                </form>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Resetear contraseña de un usuario
     */
    public function reset_password($id) {
        requireLogin();
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $usuario = $this->usuario->find($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            redirect('usuarios');
        }
        
        // Generar contraseña temporal simple
        $nuevaPassword = 'temp' . rand(1000, 9999);
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ?, debe_cambiar_password = 1 WHERE id = ?";
        $db = Database::getInstance();
        $db->query($sql, [$hash, $id]);
        
        $_SESSION['success'] = '✅ Contraseña reestablecida<br>🔒 Nueva contraseña temporal: <strong>' . $nuevaPassword . '</strong><br>⚠️ El usuario deberá cambiarla al iniciar sesión';
        redirect('usuarios');
    }
    
    /**
     * Cambiar la propia contraseña (desde el perfil)
     */
    public function cambiar_password() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password_actual = $_POST['password_actual'] ?? '';
            $password_nueva = $_POST['password_nueva'] ?? '';
            $password_confirmar = $_POST['password_confirmar'] ?? '';
            
            $usuario = $this->usuario->find($_SESSION['usuario_id']);
            
            if (!password_verify($password_actual, $usuario['password'])) {
                $_SESSION['error'] = 'Contraseña actual incorrecta';
            } elseif (strlen($password_nueva) < 4) {
                $_SESSION['error'] = 'La nueva contraseña debe tener al menos 4 caracteres';
            } elseif ($password_nueva !== $password_confirmar) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
            } else {
                $this->usuario->actualizarPassword($_SESSION['usuario_id'], $password_nueva);
                $_SESSION['success'] = '✅ Contraseña cambiada correctamente';
                redirect('dashboard');
            }
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cambiar Contraseña</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;}
                .card{background:#1e293b;padding:2rem;border-radius:16px;max-width:500px;margin:2rem auto;}
                input{width:100%;padding:10px;margin:10px 0;border-radius:10px;border:none;background:#0f172a;color:white;}
                button{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border:none;border-radius:10px;color:white;cursor:pointer;}
                a{color:#22c55e;text-decoration:none;}
            </style>
        </head>
        <body>
            <div class="card">
                <h2>🔒 Cambiar Contraseña</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <form method="POST">
                    <label>🔐 Contraseña actual</label>
                    <input type="password" name="password_actual" required>
                    
                    <label>🆕 Nueva contraseña</label>
                    <input type="text" name="password_nueva" required>
                    
                    <label>✅ Confirmar nueva contraseña</label>
                    <input type="text" name="password_confirmar" required>
                    
                    <button type="submit">💾 Cambiar Contraseña</button>
                    <a href="' . BASE_URL . 'dashboard">Cancelar</a>
                </form>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Elimina un usuario
     */
    public function eliminar($id) {
        requireLogin();
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        if ($id == $_SESSION['usuario_id']) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario';
            redirect('usuarios');
        }
        
        $this->usuario->delete($id);
        $_SESSION['success'] = '✅ Usuario eliminado';
        redirect('usuarios');
    }
}
?>