<?php
require_once __DIR__ . '/../models/Campana.php';

class CampanaController {
    private $campana;
    
    public function __construct() {
        $this->campana = new Campana();
    }
    
    public function index() {
        requireLogin();
        $campanas = $this->campana->getWithStatus();
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Campañas</title>
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
                .btn-ver { background:#06b6d4; padding:4px 8px; border-radius:6px; text-decoration:none; color:white; }
                .activa { color:#22c55e; font-weight:bold; }
                .pendiente { color:#facc15; font-weight:bold; }
                .finalizada { color:#ef4444; font-weight:bold; }
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
                <h2>📢 Campañas</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <a href="' . BASE_URL . 'campanas/crear" class="btn">➕ Nueva Campaña</a>
                <table>
                    <thead><tr><th>ID</th><th>Nombre</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>';
        
        foreach ($campanas as $c) {
            $estadoClass = '';
            if ($c['estado_texto'] == 'activa') $estadoClass = 'activa';
            elseif ($c['estado_texto'] == 'pendiente') $estadoClass = 'pendiente';
            else $estadoClass = 'finalizada';
            
            echo '<tr>
                <td>' . $c['id'] . '</td>
                <td>' . h($c['nombre']) . '</td>
                <td>' . date('d/m/Y', strtotime($c['fecha_inicio'])) . '</td>
                <td>' . date('d/m/Y', strtotime($c['fecha_fin'])) . '</td>
                <td class="' . $estadoClass . '">' . ucfirst($c['estado_texto']) . '</td>
                <td>
                    <a href="' . BASE_URL . 'reportes/stock?campana=' . $c['id'] . '" class="btn-ver">📦 Ver Stock</a>
                </td>
            </tr>';
        }
        
        if (empty($campanas)) {
            echo '<tr><td colspan="6">No hay campañas registradas</td></tr>';
        }
        
        echo '</tbody>
                </table>
            </div>
        </body>
        </html>';
    }
    
    public function crear() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            
            $this->campana->create([
                'nombre' => $nombre,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);
            
            $_SESSION['success'] = 'Campaña creada correctamente';
            redirect('campanas');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nueva Campaña</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                body { background:#0f172a; color:#f1f5f9; font-family:sans-serif; }
                .card { background:#1e293b; padding:2rem; border-radius:12px; max-width:500px; margin:2rem auto; }
                input, select { width:100%; padding:8px; margin:10px 0; border-radius:6px; border:none; background:#0f172a; color:white; }
                button { background:#22c55e; padding:10px 20px; border:none; border-radius:8px; color:white; cursor:pointer; }
                a { color:#22c55e; }
            </style>
        </head>
        <body>
            <div class="card">
                <h2>➕ Nueva Campaña</h2>
                <form method="POST">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" required autofocus>
                    
                    <label>Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" required>
                    
                    <label>Fecha Fin *</label>
                    <input type="date" name="fecha_fin" required>
                    
                    <button type="submit">💾 Guardar Campaña</button>
                    <a href="' . BASE_URL . 'campanas">Cancelar</a>
                </form>
            </div>
        </body>
        </html>';
    }
}