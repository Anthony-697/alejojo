<?php
/**
 * Controlador de Reportes
 * Genera reportes del sistema:
 * - Deudores (clientes con saldo pendiente)
 * - Stock (productos agrupados por campaña)
 */

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Campana.php';

class ReporteController {
    private $cliente;
    private $producto;
    private $campana;
    
    public function __construct() {
        $this->cliente = new Cliente();
        $this->producto = new Producto();
        $this->campana = new Campana();
    }
    
    /**
     * Muestra el reporte de clientes deudores
     * URL: /reportes/deudores
     */
    public function deudores() {
        requireLogin();
        
        $db = Database::getInstance();
        
        // Consulta para obtener clientes con deuda
        $sql = "SELECT 
                    c.id,
                    c.nombre,
                    c.telefono,
                    c.email,
                    SUM(v.total) as total_comprado,
                    SUM(IFNULL(p.pago_inicial + p.pago_final, 0)) as total_pagado,
                    SUM(v.total - IFNULL(p.pago_inicial + p.pago_final, 0)) as deuda
                FROM clientes c
                JOIN ventas v ON c.id = v.cliente_id
                LEFT JOIN pagos p ON v.id = p.venta_id
                GROUP BY c.id
                HAVING deuda > 0
                ORDER BY deuda DESC";
        
        $deudores = $db->fetchAll($sql);
        
        // Mostrar la vista
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Clientes Deudores</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
                .navbar{background:#1e293b;padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;}
                .navbar a:hover{background:#334155;}
                .logo{color:#22c55e;font-weight:bold;margin-right:auto;}
                .container{max-width:1200px;margin:0 auto;padding:2rem;}
                table{width:100%;border-collapse:collapse;background:#1e293b;border-radius:12px;overflow:hidden;}
                th,td{padding:12px;text-align:center;border-bottom:1px solid #334155;}
                th{background:#334155;}
                .btn{background:#22c55e;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;color:white;display:inline-block;margin:1rem 0;}
                .btn-ver{background:#06b6d4;padding:4px 8px;border-radius:6px;text-decoration:none;color:white;}
                .deuda{color:#ef4444;font-weight:bold;}
                .total{color:#22c55e;font-weight:bold;}
                @media print{
                    .navbar, .btn, .no-print{display:none;}
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
                <h2>💳 Clientes con Deuda</h2>
                
                <div class="no-print" style="margin-bottom:1rem;">
                    <button onclick="window.print()" class="btn">🖨 Imprimir</button>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Total Comprado</th>
                                <th>Pagado</th>
                                <th>Deuda</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        $contador = 1;
        foreach ($deudores as $d) {
            echo '<tr>
                    <td>' . $contador++ . '</td>
                    <td>' . h($d['nombre']) . '</td>
                    <td>' . h($d['telefono']) . '</td>
                    <td>' . h($d['email']) . '</td>
                    <td class="total">$' . number_format($d['total_comprado'], 2) . '</td>
                    <td>$' . number_format($d['total_pagado'], 2) . '</td>
                    <td class="deuda">$' . number_format($d['deuda'], 2) . '</td>
                    <td class="no-print">
                        <a href="' . BASE_URL . 'clientes/editar/' . $d['id'] . '" class="btn-ver">✏ Editar</a>
                        <a href="' . BASE_URL . 'ventas?cliente=' . $d['id'] . '" class="btn-ver">📊 Historial</a>
                    </td>
                </tr>';
        }
        
        if (empty($deudores)) {
            echo '<tr><td colspan="8" style="text-align:center;">✅ No hay clientes con deuda pendiente</td></tr>';
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
    
    /**
     * Muestra el reporte de stock por campaña
     * URL: /reportes/stock
     */
    public function stock() {
        requireLogin();
        
        // Obtener campaña seleccionada
        $campana_actual = $_GET['campana'] ?? null;
        $campanas = $this->campana->all();
        
        // Si no hay campaña seleccionada y hay campañas, usar la primera
        if (!$campana_actual && !empty($campanas)) {
            $campana_actual = $campanas[0]['id'];
        }
        
        // Productos de la campaña actual
        $actual = [];
        if ($campana_actual) {
            $actual = $this->producto->getByCampana($campana_actual);
        }
        
        // Productos de otras campañas (stock viejo)
        $db = Database::getInstance();
        $sql = "SELECT p.codigo, p.nombre, p.stock, p.precio_compra, p.precio_normal,
                       c.nombre as campana
                FROM productos p
                LEFT JOIN campanas c ON p.campana_id = c.id
                WHERE p.estado = 1 
                AND p.stock > 0";
        
        if ($campana_actual) {
            $sql .= " AND (p.campana_id != ? OR p.campana_id IS NULL)";
            $viejo = $db->fetchAll($sql, [$campana_actual]);
        } else {
            $viejo = $db->fetchAll($sql);
        }
        
        // Obtener nombre de la campaña actual
        $nombreCampanaActual = '';
        foreach ($campanas as $c) {
            if ($c['id'] == $campana_actual) {
                $nombreCampanaActual = $c['nombre'];
                break;
            }
        }
        
        // Mostrar la vista
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reporte de Stock</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
                .navbar{background:#1e293b;padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;}
                .navbar a:hover{background:#334155;}
                .logo{color:#22c55e;font-weight:bold;margin-right:auto;}
                .container{max-width:1400px;margin:0 auto;padding:2rem;}
                table{width:100%;border-collapse:collapse;background:#1e293b;border-radius:12px;overflow:hidden;margin-bottom:2rem;}
                th,td{padding:10px;text-align:center;border-bottom:1px solid #334155;}
                th{background:#334155;}
                .btn{background:#22c55e;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;color:white;display:inline-block;margin:1rem 0;}
                select{padding:8px;border-radius:6px;background:#0f172a;color:white;border:1px solid #334155;}
                .rojo{color:#ef4444;font-weight:bold;}
                .amarillo{color:#facc15;font-weight:bold;}
                .verde{color:#22c55e;font-weight:bold;}
                h3{margin:1rem 0;color:#22c55e;}
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
                <h2>📦 Reporte de Stock</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <div class="no-print" style="margin-bottom:1rem;">
                    <form method="GET" style="display:inline-block;">
                        <label>Seleccionar campaña actual:</label>
                        <select name="campana" onchange="this.form.submit()">
                            <option value="">Todas</option>';
        foreach ($campanas as $c) {
            echo '<option value="' . $c['id'] . '" ' . ($campana_actual == $c['id'] ? 'selected' : '') . '>' . h($c['nombre']) . '</option>';
        }
        echo '</select>
                    </form>
                    <button onclick="window.print()" class="btn" style="margin-left:1rem;">🖨 Imprimir</button>
                </div>
                
                <h3>📦 Stock Actual ' . ($nombreCampanaActual ? '(' . h($nombreCampanaActual) . ')' : '') . '</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Campaña</th>
                                <th>Stock</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($actual as $p) {
            $stockClass = $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde');
            echo '<tr>
                    <td>' . h($p['codigo']) . '</td>
                    <td>' . h($p['nombre']) . '</td>
                    <td>' . h($p['campana_nombre'] ?? 'Sin campaña') . '</td>
                    <td class="' . $stockClass . '">' . number_format($p['stock']) . '</td>
                    <td>$' . number_format($p['precio_compra'], 2) . '</td>
                    <td>$' . number_format($p['precio_normal'], 2) . '</td>
                </tr>';
        }
        
        if (empty($actual)) {
            echo '<tr><td colspan="6" style="text-align:center;">No hay productos en esta campaña</td></tr>';
        }
        
        echo '</tbody>
                    </table>
                </div>
                
                <h3>📦 Stock de Campañas Anteriores</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Campaña</th>
                                <th>Stock</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($viejo as $p) {
            $stockClass = $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde');
            echo '<tr>
                    <td>' . h($p['codigo']) . '</td>
                    <td>' . h($p['nombre']) . '</td>
                    <td>' . h($p['campana'] ?? 'Sin campaña') . '</td>
                    <td class="' . $stockClass . '">' . number_format($p['stock']) . '</td>
                    <td>$' . number_format($p['precio_compra'], 2) . '</td>
                    <td>$' . number_format($p['precio_normal'], 2) . '</td>
                </tr>';
        }
        
        if (empty($viejo)) {
            echo '<tr><td colspan="6" style="text-align:center;">No hay productos de campañas anteriores</td></tr>';
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