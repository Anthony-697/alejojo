<?php
class DashboardController {
    public function index() {
        requireLogin();
        
        $db = Database::getInstance();
        
        $ventasTotal = $db->fetchOne("SELECT SUM(total) as total FROM ventas")['total'] ?? 0;
        $gananciaTotal = $db->fetchOne("SELECT SUM((d.precio_aplicado - p.precio_compra) * d.cantidad) as ganancia FROM detalle_venta d JOIN productos p ON d.producto_id = p.id")['ganancia'] ?? 0;
        $productosVendidos = $db->fetchOne("SELECT SUM(cantidad) as total FROM detalle_venta")['total'] ?? 0;
        $totalClientes = $db->fetchOne("SELECT COUNT(*) as total FROM clientes")['total'] ?? 0;
        $totalProductos = $db->fetchOne("SELECT COUNT(*) as total FROM productos WHERE estado = 1")['total'] ?? 0;
        
        $ventasPorCampana = $db->fetchAll("SELECT c.nombre, COALESCE(SUM(v.total), 0) as total FROM campanas c LEFT JOIN ventas v ON v.campana_id = c.id GROUP BY c.id ORDER BY total DESC");
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard | ALEJOJO V2</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
                .navbar{background:linear-gradient(135deg,#1e293b,#0f172a);padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;box-shadow:0 4px 6px rgba(0,0,0,0.1);position:sticky;top:0;z-index:1000;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;transition:all 0.3s;}
                .navbar a:hover{background:#334155;transform:translateY(-2px);}
                .logo{color:#22c55e;font-weight:bold;font-size:1.2rem;margin-right:auto;}
                .container{max-width:1400px;margin:0 auto;padding:2rem;}
                .dashboard-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-bottom:2rem;}
                .card{background:#1e293b;padding:1.5rem;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.1);}
                .card h3{color:#22c55e;margin-bottom:1rem;font-size:1rem;}
                .big-number{font-size:2rem;font-weight:bold;color:#22c55e;}
                table{width:100%;border-collapse:collapse;background:#1e293b;border-radius:16px;overflow:hidden;}
                th,td{padding:12px;text-align:center;border-bottom:1px solid #334155;}
                th{background:#334155;}
                @media(max-width:768px){.container{padding:1rem;}}
            </style>
        </head>
        <body>
            ' . renderMenu() . '
            <div class="container">
                <h2 style="margin-bottom:1.5rem;">📊 Dashboard General</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <div class="dashboard-grid">
                    <div class="card"><h3>💰 Ventas Totales</h3><div class="big-number">' . money($ventasTotal) . '</div></div>
                    <div class="card"><h3>📈 Ganancia Total</h3><div class="big-number">' . money($gananciaTotal) . '</div></div>
                    <div class="card"><h3>📦 Productos Vendidos</h3><div class="big-number">' . number_format($productosVendidos) . '</div></div>
                    <div class="card"><h3>👤 Clientes</h3><div class="big-number">' . number_format($totalClientes) . '</div></div>
                    <div class="card"><h3>📦 Productos</h3><div class="big-number">' . number_format($totalProductos) . '</div></div>
                </div>
                <h2 style="margin-bottom:1rem;">📊 Ventas por Campaña</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Campaña</th><th>Total Vendido</th></tr></thead>
                        <tbody>';
        foreach ($ventasPorCampana as $v) {
            echo '<tr><td>' . h($v['nombre']) . '</td><td>' . money($v['total']) . '</td></tr>';
        }
        if (empty($ventasPorCampana)) echo '<tr><td colspan="2">No hay ventas registradas</td></tr>';
        echo '</tbody>
                    </table>
                </div>
                <div style="margin-top:2rem;text-align:center;font-size:12px;color:#64748b;">Sistema de Ventas ALEJOJO V2 | Todos los derechos reservados</div>
            </div>
        </body>
        </html>';
    }
}
?>