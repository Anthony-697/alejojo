<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Campana.php';

class ProductoController {
    private $producto;
    private $campana;
    
    public function __construct() {
        $this->producto = new Producto();
        $this->campana = new Campana();
    }
    
    public function index() {
        requireLogin();
        $filtro = $_GET['campana'] ?? '';
        $productos = $this->producto->getByCampana($filtro);
        $campanas = $this->campana->all();
        
        echo '<!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Productos</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>
            *{margin:0;padding:0;box-sizing:border-box;}
            body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
            .navbar{background:#1e293b;padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;}
            .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;}
            .navbar a:hover{background:#334155;}
            .logo{color:#22c55e;font-weight:bold;margin-right:auto;}
            .container{max-width:1400px;margin:0 auto;padding:2rem;}
            table{width:100%;border-collapse:collapse;background:#1e293b;border-radius:12px;overflow:hidden;}
            th,td{padding:10px;text-align:center;border-bottom:1px solid #334155;}
            th{background:#334155;}
            .btn{background:#22c55e;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;color:white;display:inline-block;margin:1rem 0;}
            .btn-editar{background:#3b82f6;padding:4px 8px;border-radius:6px;text-decoration:none;color:white;}
            .btn-eliminar{background:#ef4444;padding:4px 8px;border-radius:6px;text-decoration:none;color:white;}
            select, input{padding:5px;border-radius:5px;}
            .rojo{color:#ef4444;font-weight:bold;}
            .amarillo{color:#facc15;font-weight:bold;}
            .verde{color:#22c55e;font-weight:bold;}
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
                <h2>📦 Productos</h2>
                <div style="display:flex; gap:1rem; margin-bottom:1rem;">
                    <a href="' . BASE_URL . 'productos/crear" class="btn">➕ Nuevo Producto</a>
                    <a href="' . BASE_URL . 'productos/importar" class="btn">📥 Importar CSV</a>
                </div>
                <form method="GET" style="margin-bottom:1rem;">
                    <label>Filtrar por campaña: </label>
                    <select name="campana" onchange="this.form.submit()">
                        <option value="">Todas</option>';
        foreach ($campanas as $c) {
            echo '<option value="' . $c['id'] . '" ' . ($filtro == $c['id'] ? 'selected' : '') . '>' . h($c['nombre']) . '</option>';
        }
        echo '</select></form>
                <table>
                    <thead><tr><th>ID</th><th>Código</th><th>Nombre</th><th>Compra</th><th>Venta</th><th>Stock</th><th>Campaña</th><th>Acciones</th></tr></thead>
                    <tbody>';
        
        foreach ($productos as $p) {
            $stockClass = $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde');
            echo '<tr>
                <td>' . $p['id'] . '</td>
                <td>' . h($p['codigo']) . '</td>
                <td>' . h($p['nombre']) . '</td>
                <td>$' . number_format($p['precio_compra'], 2) . '</td>
                <td>$' . number_format($p['precio_normal'], 2) . '</td>
                <td class="' . $stockClass . '">' . $p['stock'] . '</td>
                <td>' . h($p['campana_nombre'] ?? 'Sin campaña') . '</td>
                <td>
                    <a href="' . BASE_URL . 'productos/editar/' . $p['id'] . '" class="btn-editar">✏ Editar</a>
                    <a href="' . BASE_URL . 'productos/eliminar/' . $p['id'] . '" class="btn-eliminar" onclick="return confirm(\'¿Eliminar?\')">🗑 Eliminar</a>
                 </td>
            </tr>';
        }
        
        if (empty($productos)) {
            echo '<tr><td colspan="8">No hay productos registrados</td></tr>';
        }
        
        echo '</tbody></table></div></body></html>';
    }
    
    public function crear() {
        requireLogin();
        $campanas = $this->campana->all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->producto->create([
                'codigo' => trim($_POST['codigo']),
                'nombre' => trim($_POST['nombre']),
                'precio_compra' => (float)$_POST['precio_compra'],
                'precio_venta' => (float)$_POST['precio'],
                'campana_id' => (int)$_POST['campana_id']
            ]);
            $_SESSION['success'] = 'Producto creado';
            redirect('productos');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Producto</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;} .card{background:#1e293b;padding:2rem;border-radius:12px;max-width:500px;margin:2rem auto;} input,select{width:100%;padding:8px;margin:10px 0;border-radius:6px;border:none;background:#0f172a;color:white;} button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;} a{color:#22c55e;}</style>
        </head>
        <body><div class="card"><h2>➕ Nuevo Producto</h2>
        <form method="POST">
            <label>Código *</label><input type="text" name="codigo" required>
            <label>Nombre *</label><input type="text" name="nombre" required>
            <label>Precio Compra *</label><input type="number" step="0.01" name="precio_compra" required>
            <label>Precio Venta *</label><input type="number" step="0.01" name="precio" required>
            <label>Campaña *</label>
            <select name="campana_id" required>
                <option value="">Seleccione</option>';
        foreach ($campanas as $c) {
            echo '<option value="' . $c['id'] . '">' . h($c['nombre']) . '</option>';
        }
        echo '</select>
            <button type="submit">💾 Guardar</button>
            <a href="' . BASE_URL . 'productos">Cancelar</a>
        </form></div></body></html>';
    }
    
    public function editar($id) {
        requireLogin();
        $producto = $this->producto->find($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->producto->update($id, [
                'codigo' => trim($_POST['codigo']),
                'nombre' => trim($_POST['nombre']),
                'precio_compra' => (float)$_POST['precio_compra'],
                'precio_venta' => (float)$_POST['precio'],
                'stock' => (int)$_POST['stock']
            ]);
            $_SESSION['success'] = 'Producto actualizado';
            redirect('productos');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Producto</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;} .card{background:#1e293b;padding:2rem;border-radius:12px;max-width:500px;margin:2rem auto;} input{width:100%;padding:8px;margin:10px 0;border-radius:6px;border:none;background:#0f172a;color:white;} button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;} a{color:#22c55e;}</style>
        </head>
        <body><div class="card"><h2>✏ Editar Producto</h2>
        <form method="POST">
            <input type="hidden" name="id" value="' . $producto['id'] . '">
            <label>Código *</label><input type="text" name="codigo" value="' . h($producto['codigo']) . '" required>
            <label>Nombre *</label><input type="text" name="nombre" value="' . h($producto['nombre']) . '" required>
            <label>Precio Compra *</label><input type="number" step="0.01" name="precio_compra" value="' . $producto['precio_compra'] . '" required>
            <label>Precio Venta *</label><input type="number" step="0.01" name="precio" value="' . $producto['precio_normal'] . '" required>
            <label>Stock</label><input type="number" name="stock" value="' . $producto['stock'] . '">
            <button type="submit">💾 Actualizar</button>
            <a href="' . BASE_URL . 'productos">Cancelar</a>
        </form></div></body></html>';
    }
    
    public function eliminar($id) {
        requireLogin();
        $this->producto->softDelete($id);
        $_SESSION['success'] = 'Producto eliminado';
        redirect('productos');
    }
    
    public function importar() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            $result = $this->producto->importFromCSV($_FILES['archivo']['tmp_name']);
            if ($result['success']) {
                $_SESSION['success'] = "✅ {$result['importados']} productos importados";
                if ($result['errores'] > 0) {
                    $_SESSION['error'] = "⚠️ {$result['errores']} filas ignoradas";
                }
            } else {
                $_SESSION['error'] = "❌ Error: {$result['error']}";
            }
            redirect('productos');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Importar Productos</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;} .card{background:#1e293b;padding:2rem;border-radius:12px;max-width:500px;margin:2rem auto;} input,select{width:100%;padding:8px;margin:10px 0;border-radius:6px;border:none;background:#0f172a;color:white;} button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;} a{color:#22c55e;}</style>
        </head>
        <body>
        <div class="card">
            <h2>📥 Importar Productos (CSV)</h2>
            <p>Formato: codigo,nombre,precio_compra,precio_venta,stock,campana_id</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="archivo" accept=".csv" required>
                <button type="submit">📤 Subir Archivo</button>
                <a href="' . BASE_URL . 'productos">Cancelar</a>
            </form>
        </div>
        </body></html>';
    }
}