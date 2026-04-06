<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $cliente;
    
    public function __construct() {
        $this->cliente = new Cliente();
    }
    
    public function index() {
        requireLogin();
        $clientes = $this->cliente->all('nombre ASC');
        
        echo '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clientes</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family: "Segoe UI", sans-serif; background:#0f172a; color:#f1f5f9; }
            .navbar { background:#1e293b; padding:1rem 2rem; display:flex; gap:0.5rem; flex-wrap:wrap; }
            .navbar a { color:#e2e8f0; text-decoration:none; padding:0.5rem 1rem; border-radius:8px; }
            .navbar a:hover { background:#334155; }
            .logo { color:#22c55e; font-weight:bold; margin-right:auto; }
            .container { max-width:1200px; margin:0 auto; padding:2rem; }
            table { width:100%; border-collapse:collapse; background:#1e293b; border-radius:12px; overflow:hidden; }
            th, td { padding:12px; text-align:center; border-bottom:1px solid #334155; }
            th { background:#334155; }
            .btn { background:#22c55e; padding:0.5rem 1rem; border-radius:8px; text-decoration:none; color:white; display:inline-block; margin:1rem 0; }
            .btn-editar { background:#3b82f6; padding:4px 8px; border-radius:6px; text-decoration:none; color:white; }
            .btn-eliminar { background:#ef4444; padding:4px 8px; border-radius:6px; text-decoration:none; color:white; }
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
                <h2>👤 Clientes</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <a href="' . BASE_URL . 'clientes/crear" class="btn">➕ Nuevo Cliente</a>
                <table>
                    <thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr></thead>
                    <tbody>';
        foreach ($clientes as $c) {
            echo '<tr>
                <td>' . $c['id'] . '</td>
                <td>' . h($c['nombre']) . '</td>
                <td>' . h($c['telefono']) . '</td>
                <td>' . h($c['email']) . '</td>
                <td>
                    <a href="' . BASE_URL . 'clientes/editar/' . $c['id'] . '" class="btn-editar">✏ Editar</a>
                    <a href="' . BASE_URL . 'clientes/eliminar/' . $c['id'] . '" class="btn-eliminar" onclick="return confirm(\'¿Eliminar?\')">🗑 Eliminar</a>
                </td>
            </tr>';
        }
        if (empty($clientes)) {
            echo '<tr><td colspan="5">No hay clientes registrados</td></tr>';
        }
        echo '</tbody></table></div></body></html>';
    }
    
    public function crear() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            
            $db = Database::getInstance();
            $db->query("INSERT INTO clientes (nombre, telefono, email, direccion) VALUES (:nombre, :telefono, :email, :direccion)", [
                ':nombre' => $nombre, ':telefono' => $telefono, ':email' => $email, ':direccion' => $direccion
            ]);
            $_SESSION['success'] = 'Cliente creado';
            redirect('clientes');
        }
        
        echo '<!DOCTYPE html><html>
        <head><meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Cliente</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;} .card{background:#1e293b;padding:2rem;border-radius:12px;max-width:500px;margin:2rem auto;} input,textarea{width:100%;padding:8px;margin:10px 0;border-radius:6px;border:none;background:#0f172a;color:white;} button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;} a{color:#22c55e;}</style></head>
        <body><div class="card"><h2>➕ Nuevo Cliente</h2>
        <form method="POST">
            <label>Nombre *</label><input type="text" name="nombre" required>
            <label>Teléfono</label><input type="text" name="telefono">
            <label>Email</label><input type="email" name="email">
            <label>Dirección</label><textarea name="direccion" rows="3"></textarea>
            <button type="submit">💾 Guardar</button>
            <a href="' . BASE_URL . 'clientes">Cancelar</a>
        </form></div></body></html>';
    }
    
    public function editar($id) {
        requireLogin();
        $cliente = $this->cliente->find($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            
            $db = Database::getInstance();
            $db->query("UPDATE clientes SET nombre=:nombre, telefono=:telefono, email=:email, direccion=:direccion WHERE id=:id", [
                ':nombre' => $nombre, ':telefono' => $telefono, ':email' => $email, ':direccion' => $direccion, ':id' => $id
            ]);
            $_SESSION['success'] = 'Cliente actualizado';
            redirect('clientes');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Cliente</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;} .card{background:#1e293b;padding:2rem;border-radius:12px;max-width:500px;margin:2rem auto;} input,textarea{width:100%;padding:8px;margin:10px 0;border-radius:6px;border:none;background:#0f172a;color:white;} button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;} a{color:#22c55e;}</style></head>
        <body><div class="card"><h2>✏ Editar Cliente</h2>
        <form method="POST">
            <input type="hidden" name="id" value="' . $cliente['id'] . '">
            <label>Nombre *</label><input type="text" name="nombre" value="' . h($cliente['nombre']) . '" required>
            <label>Teléfono</label><input type="text" name="telefono" value="' . h($cliente['telefono']) . '">
            <label>Email</label><input type="email" name="email" value="' . h($cliente['email']) . '">
            <label>Dirección</label><textarea name="direccion" rows="3">' . h($cliente['direccion']) . '</textarea>
            <button type="submit">💾 Actualizar</button>
            <a href="' . BASE_URL . 'clientes">Cancelar</a>
        </form></div></body></html>';
    }
    
    public function eliminar($id) {
        requireLogin();
        $this->cliente->delete($id);
        $_SESSION['success'] = 'Cliente eliminado';
        redirect('clientes');
    }
}