<?php
/**
 * Reporte imprimible por campaña
 * URL: /ventas/plantilla?campana=:id
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Campaña - <?= h($campana['nombre']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            font-size: 11px;
            padding: 20px;
            background: white;
            color: black;
        }
        h1, h2 { 
            text-align: center; 
            margin: 0 0 10px 0;
        }
        h1 { font-size: 18px; }
        h2 { font-size: 14px; }
        .info { 
            margin-bottom: 15px;
            font-size: 11px;
        }
        .linea {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 200px;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: top;
        }
        th {
            background-color: #eaeaea;
            font-weight: bold;
        }
        .firma {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .firma div {
            text-align: center;
            width: 200px;
        }
        .firma hr {
            margin-top: 40px;
            width: 100%;
        }
        .totales {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
        }
        @media print {
            button { display: none; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

<div style="margin-bottom: 10px; text-align: center;">
    <button onclick="window.print()" style="padding:8px 15px; background:#22c55e; color:white; border:none; border-radius:5px; cursor:pointer;">🖨️ Imprimir</button>
    <button onclick="history.back()" style="padding:8px 15px; background:#64748b; color:white; border:none; border-radius:5px; cursor:pointer;">⬅ Volver</button>
</div>

<!-- Encabezado -->
<h1>🛍️ ALEJOJO</h1>
<h2>Control de Ventas por Campaña</h2>

<div class="info">
    <p>Campaña: <span class="linea"><?= h($campana['nombre']) ?></span></p>
    <p>Fecha: <span class="linea"><?= date('d/m/Y') ?></span></p>
    <p>Período: <?= date('d/m/Y', strtotime($campana['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($campana['fecha_fin'])) ?></p>
</div>

<!-- Tabla de productos vendidos -->
<h3>📦 Detalle de Productos Vendidos</h3>

<table>
    <thead>
        <tr>
            <th width="30">#</th>
            <th width="80">Código</th>
            <th>Producto</th>
            <th width="40">Cant</th>
            <th width="70">P. Venta</th>
            <th width="70">Total</th>
            <th>Cliente</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        <?php foreach ($detalles as $d): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= h($d['codigo']) ?></td>
            <td><?= h($d['producto']) ?></td>
            <td><?= number_format($d['cantidad']) ?></td>
            <td><?= money($d['precio_aplicado']) ?></td>
            <td><?= money($d['cantidad'] * $d['precio_aplicado']) ?></td>
            <td><?= h($d['cliente']) ?></td>
        </tr>
        <?php endforeach; ?>
        
        <!-- Filas en blanco hasta llegar a 25 -->
        <?php for (; $i <= 25; $i++): ?>
        <tr>
            <td><?= $i ?></td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>

<!-- Tabla de control de pagos -->
<h3>💳 Control de Pagos por Cliente</h3>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Total Compra</th>
            <th>Pago Inicial</th>
            <th>Abonos</th>
            <th>Saldo Pendiente</th>
            <th width="30">✔</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pagos as $p): ?>
            <?php if ($p['total'] > 0): ?>
            <tr>
                <td><?= h($p['nombre']) ?></td>
                <td><?= money($p['total']) ?></td>
                <td><?= money($p['inicial']) ?></td>
                <td><?= money($p['abono']) ?></td>
                <td><?= money($p['saldo']) ?></td>
                <td>☐</td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- Filas en blanco -->
        <?php for ($i = count($pagos); $i < 15; $i++): ?>
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td>☐</td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>

<!-- Totales generales -->
<div class="totales">
    <?php 
        $totalGeneral = 0;
        $totalSaldo = 0;
        foreach ($pagos as $p) {
            $totalGeneral += $p['total'];
            $totalSaldo += $p['saldo'];
        }
    ?>
    <p><strong>Total General de Ventas:</strong> <?= money($totalGeneral) ?></p>
    <p><strong>Saldo Total Pendiente:</strong> <?= money($totalSaldo) ?></p>
</div>

<!-- Firmas -->
<div class="firma">
    <div>____________________<br>Firma Cliente</div>
    <div>____________________<br>Firma Vendedor</div>
    <div>____________________<br>Sello</div>
</div>

</body>
</html>