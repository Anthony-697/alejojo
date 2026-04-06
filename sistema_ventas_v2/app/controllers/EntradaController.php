<?php
/**
 * Controlador de Entradas (Compras)
 * Maneja el registro de compras de productos a proveedores
 */

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Campana.php';
require_once __DIR__ . '/../models/Entrada.php';

class EntradaController {
    private $producto;
    private $campana;
    private $entrada;
    
    public function __construct() {
        $this->producto = new Producto();
        $this->campana = new Campana();
        $this->entrada = new Entrada();
    }
    
    /**
     * Muestra el formulario para registrar una compra
     * URL: /entradas/crear
     */
    public function crear() {
        requireLogin();
        
        // Obtener productos y campañas
        $productos = $this->producto->getByCampana();
        $campanas = $this->campana->all();
        
        // Procesar el formulario cuando se envía
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto_id = (int)($_POST['producto_id'] ?? 0);
            $campana_id = (int)($_POST['campana_id'] ?? 0);
            $cantidad = (float)($_POST['cantidad'] ?? 0);
            $costo = (float)($_POST['costo'] ?? 0);
            
            $errors = [];
            if ($producto_id <= 0) $errors[] = 'Debe seleccionar un producto';
            if ($campana_id <= 0) $errors[] = 'Debe seleccionar una campaña';
            if ($cantidad <= 0) $errors[] = 'La cantidad debe ser mayor a 0';
            if ($costo <= 0) $errors[] = 'El costo debe ser mayor a 0';
            
            if (empty($errors)) {
                $db = Database::getInstance();
                $db->beginTransaction();
                
                try {
                    // Registrar la entrada
                    $this->entrada->registrar($producto_id, $campana_id, $cantidad, $costo);
                    
                    // Actualizar el stock del producto
                    $this->producto->updateStock($producto_id, $cantidad, 'sumar');
                    
                    $db->commit();
                    $_SESSION['success'] = '✅ Compra registrada correctamente';
                    redirect('entradas/crear');
                    
                } catch (Exception $e) {
                    $db->rollback();
                    $_SESSION['error'] = 'Error al registrar la compra: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        // Mostrar el formulario HTML
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Registrar Compra</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
                .navbar{background:#1e293b;padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;}
                .navbar a:hover{background:#334155;}
                .logo{color:#22c55e;font-weight:bold;margin-right:auto;}
                .container{max-width:600px;margin:0 auto;padding:2rem;}
                .card{background:#1e293b;padding:1.5rem;border-radius:12px;margin-bottom:1rem;}
                input,select{width:100%;padding:8px;margin:5px 0 15px 0;border-radius:6px;border:none;background:#0f172a;color:white;}
                button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;}
                .btn{background:#22c55e;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;color:white;display:inline-block;}
                .info{background:#0f172a;padding:1rem;border-radius:8px;margin-bottom:1rem;text-align:center;}
                .alert-success{background:#166534;padding:0.75rem;border-radius:8px;margin-bottom:1rem;border-left:4px solid #22c55e;}
                .alert-error{background:#991b1b;padding:0.75rem;border-radius:8px;margin-bottom:1rem;border-left:4px solid #ef4444;}
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
                <h2>📥 Registrar Compra a Proveedor</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                
                <div class="info">
                    <strong>ℹ️ Información:</strong> Al registrar una compra, el stock del producto se actualizará automáticamente.
                </div>';
        
        // Mostrar mensajes de éxito o error
        if (isset($_SESSION['success'])) {
            echo '<div class="alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['error'])) {
            echo '<div class="alert-error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        
        if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
            echo '<div class="alert-error"><ul>';
            foreach ($_SESSION['errors'] as $error) {
                echo '<li>' . h($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['errors']);
        }
        
        echo '<div class="card">
                    <form method="POST">
                        <label>Campaña *</label>
                        <select name="campana_id" id="campana" required>
                            <option value="">Seleccione una campaña</option>';
        foreach ($campanas as $c) {
            echo '<option value="' . $c['id'] . '">' . h($c['nombre']) . '</option>';
        }
        echo '</select>
                        
                        <label>Producto *</label>
                        <select name="producto_id" id="producto" required>
                            <option value="">Seleccione un producto</option>';
        foreach ($productos as $p) {
            echo '<option value="' . $p['id'] . '" data-campana="' . $p['campana_id'] . '">';
            echo h($p['nombre']) . ' (Stock actual: ' . $p['stock'] . ') - $' . number_format($p['precio_compra'], 2);
            echo '</option>';
        }
        echo '</select>
                        
                        <label>Cantidad *</label>
                        <input type="number" name="cantidad" min="1" required>
                        
                        <label>Costo Unitario *</label>
                        <input type="number" step="0.01" name="costo" required>
                        
                        <div style="margin-top: 1rem;">
                            <button type="submit">💾 Guardar Compra</button>
                            <a href="' . BASE_URL . 'dashboard" class="btn" style="background:#64748b; margin-left:1rem;">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                // Filtrar productos por campaña seleccionada
                function filtrarProductos() {
                    var campana = document.getElementById("campana").value;
                    var selectProducto = document.getElementById("producto");
                    var opciones = selectProducto.querySelectorAll("option");
                    
                    for (var i = 0; i < opciones.length; i++) {
                        var opcion = opciones[i];
                        if (opcion.value && opcion.dataset.campana) {
                            if (campana === "" || opcion.dataset.campana == campana) {
                                opcion.style.display = "block";
                            } else {
                                opcion.style.display = "none";
                            }
                        }
                    }
                    selectProducto.value = "";
                }
                
                document.getElementById("campana").addEventListener("change", filtrarProductos);
            </script>
        </body>
        </html>';
    }
    
    /**
     * Muestra el historial de compras
     * URL: /entradas/historial
     */
    public function historial() {
        requireLogin();
        
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;
        $entradas = $this->entrada->getAll($fecha_inicio, $fecha_fin);
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Historial de Compras</title>
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
                input{padding:6px;border-radius:5px;border:none;background:#0f172a;color:white;}
                @media print{
                    .navbar, .btn, form, .no-print{display:none;}
                }
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
                <h2>📋 Historial de Compras</h2>
                
                <div class="no-print">
                    <a href="' . BASE_URL . 'entradas/crear" class="btn">➕ Nueva Compra</a>
                    
                    <form method="GET" style="display:inline-block; margin-left:1rem;">
                        <label>Desde:</label>
                        <input type="date" name="fecha_inicio" value="' . $fecha_inicio . '">
                        <label>Hasta:</label>
                        <input type="date" name="fecha_fin" value="' . $fecha_fin . '">
                        <button type="submit" class="btn" style="padding:6px 12px;">Filtrar</button>
                        <a href="' . BASE_URL . 'entradas/historial" class="btn" style="background:#64748b; padding:6px 12px;">Limpiar</a>
                    </form>
                    
                    <button onclick="window.print()" class="btn" style="background:#06b6d4; margin-left:1rem;">🖨 Imprimir</button>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Campaña</th>
                                <th>Cantidad</th>
                                <th>Costo Unitario</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($entradas as $e) {
            $total = $e['cantidad'] * $e['costo_unitario'];
            echo '<tr>
                    <td>' . $e['id'] . '</td>
                    <td>' . date('d/m/Y H:i', strtotime($e['fecha'])) . '</td>
                    <td>' . h($e['codigo']) . '</td>
                    <td>' . h($e['producto_nombre']) . '</td>
                    <td>' . h($e['campana_nombre'] ?? 'Sin campaña') . '</td>
                    <td>' . number_format($e['cantidad']) . '</td>
                    <td>$' . number_format($e['costo_unitario'], 2) . '</td>
                    <td>$' . number_format($total, 2) . '</td>
            </tr>';
        }
        
        if (empty($entradas)) {
            echo '<tr><td colspan="8" style="text-align:center;">No hay compras registradas</td></tr>';
        }
        
        echo '</tbody>
                    </table>
                </div>
                
                <div style="margin-top:2rem; text-align:center; font-size:12px; color:#64748b;">
                    Reporte generado el ' . date('d/m/Y H:i:s') . '
                </div>
            </div>
        </body>
        </html>';
    }
}