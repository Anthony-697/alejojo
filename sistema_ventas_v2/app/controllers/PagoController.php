<?php
/**
 * Controlador de Pagos
 * Maneja los abonos a ventas
 */

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Pago.php';

class PagoController {
    private $venta;
    private $pago;
    
    public function __construct() {
        $this->venta = new Venta();
        $this->pago = new Pago();
    }
    
    /**
     * Muestra el formulario para abonar a una venta
     * URL: /pagos/abonar/:id
     */
    public function abonar($venta_id) {
        requireLogin();
        
        $db = Database::getInstance();
        
        // Obtener datos de la venta y pagos
        $sql = "SELECT v.total, 
                       IFNULL(p.pago_inicial, 0) as inicial,
                       IFNULL(p.pago_final, 0) as abono,
                       IFNULL(p.saldo, v.total) as saldo
                FROM ventas v
                LEFT JOIN pagos p ON v.id = p.venta_id
                WHERE v.id = ?";
        
        $data = $db->fetchOne($sql, [$venta_id]);
        
        if (!$data) {
            $_SESSION['error'] = 'Venta no encontrada';
            redirect('ventas');
        }
        
        $total_pagado = $data['inicial'] + $data['abono'];
        
        // Mostrar formulario HTML
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Abonar a Venta</title>
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
                input{width:100%;padding:8px;margin:5px 0 15px 0;border-radius:6px;border:none;background:#0f172a;color:white;}
                button{background:#22c55e;padding:10px 20px;border:none;border-radius:8px;color:white;cursor:pointer;}
                .btn{background:#22c55e;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;color:white;display:inline-block;}
                .monto{font-size:1.5rem;font-weight:bold;color:#22c55e;}
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
                <h2>💳 Abonar a Venta #' . $venta_id . '</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                
                <div class="card">
                    <p><strong>Total de la venta:</strong> <span class="monto">$' . number_format($data['total'], 2) . '</span></p>
                    <p><strong>Pagado hasta ahora:</strong> $' . number_format($total_pagado, 2) . '</p>
                    <p><strong>Saldo pendiente:</strong> $<span id="saldo_actual">' . number_format($data['saldo'], 2) . '</span></p>
                </div>
                
                <div class="card">
                    <form action="' . BASE_URL . 'pagos/guardar_abono" method="POST" onsubmit="return validarAbono()">
                        <input type="hidden" name="venta_id" value="' . $venta_id . '">
                        
                        <label>Nuevo Abono:</label>
                        <input type="number" step="0.01" name="abono" id="abono" 
                               max="' . $data['saldo'] . '" required oninput="calcularNuevoSaldo()">
                        
                        <p><strong>Saldo después del abono:</strong> $<span id="saldo_nuevo">' . number_format($data['saldo'], 2) . '</span></p>
                        
                        <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                            <button type="submit">💰 Guardar Abono</button>
                            <a href="' . BASE_URL . 'ventas" class="btn" style="background:#64748b;">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function calcularNuevoSaldo() {
                    var saldoActual = parseFloat(document.getElementById("saldo_actual").innerText) || 0;
                    var abono = parseFloat(document.getElementById("abono").value) || 0;
                    var nuevoSaldo = saldoActual - abono;
                    document.getElementById("saldo_nuevo").innerText = nuevoSaldo.toFixed(2);
                }
                
                function validarAbono() {
                    var abono = parseFloat(document.getElementById("abono").value) || 0;
                    var saldoActual = parseFloat(document.getElementById("saldo_actual").innerText) || 0;
                    if (abono <= 0) {
                        alert("❌ El abono debe ser mayor a 0");
                        return false;
                    }
                    if (abono > saldoActual) {
                        alert("❌ El abono no puede ser mayor al saldo pendiente");
                        return false;
                    }
                    return true;
                }
            </script>
        </body>
        </html>';
    }
    
    /**
     * Guarda un nuevo abono
     * URL: POST /pagos/guardar_abono
     */
    public function guardar_abono() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('ventas');
        }
        
        $venta_id = (int)($_POST['venta_id'] ?? 0);
        $abono = (float)($_POST['abono'] ?? 0);
        
        if ($abono <= 0) {
            $_SESSION['error'] = 'El abono debe ser mayor a 0';
            redirect("pagos/abonar/{$venta_id}");
        }
        
        $db = Database::getInstance();
        
        // Verificar si ya existe registro de pago
        $pago = $db->fetchOne("SELECT * FROM pagos WHERE venta_id = ?", [$venta_id]);
        
        if (!$pago) {
            // Crear nuevo registro de pago
            $venta = $db->fetchOne("SELECT total FROM ventas WHERE id = ?", [$venta_id]);
            $saldo = $venta['total'] - $abono;
            
            $sql = "INSERT INTO pagos (venta_id, pago_inicial, pago_final, saldo)
                    VALUES (?, 0, ?, ?)";
            
            $db->query($sql, [$venta_id, $abono, $saldo]);
        } else {
            // Actualizar registro existente
            $nuevo_abono = (float)$pago['pago_final'] + $abono;
            $nuevo_saldo = (float)$pago['saldo'] - $abono;
            
            if ($nuevo_saldo < 0) {
                $_SESSION['error'] = 'El abono excede el saldo pendiente';
                redirect("pagos/abonar/{$venta_id}");
            }
            
            $sql = "UPDATE pagos 
                    SET pago_final = ?, saldo = ?
                    WHERE venta_id = ?";
            
            $db->query($sql, [$nuevo_abono, $nuevo_saldo, $venta_id]);
        }
        
        $_SESSION['success'] = '✅ Abono registrado correctamente';
        redirect('ventas');
    }
}