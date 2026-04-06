<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>🧾 Detalle de Venta #<?= $venta['id'] ?></h2>

<div class="card">
    <p><strong>Cliente:</strong> <?= h($venta['cliente']) ?></p>
    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
    <p><strong>Total:</strong> <?= money($venta['total']) ?></p>
    <p><strong>Pago Inicial:</strong> <?= money($venta['pago_inicial'] ?? 0) ?></p>
    <p><strong>Abono:</strong> <?= money($venta['pago_final'] ?? 0) ?></p>
    <p><strong>Saldo:</strong> <?= money($venta['saldo'] ?? $venta['total']) ?></p>
</div>

<div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
    <button onclick="window.print()" class="btn">🖨 Imprimir</button>
    <a href="<?= BASE_URL ?>ventas/agregar_producto/<?= $venta['id'] ?>" class="btn" style="background: #8b5cf6;">➕ Agregar producto</a>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Compra</th>
                <th>Venta</th>
                <th>Total</th>
                <th>Ganancia</th>
                <th>Tipo</th>
                <th>Origen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $d): 
                $total = $d['cantidad'] * $d['precio_aplicado'];
                $ganancia = ($d['precio_aplicado'] - $d['precio_compra']) * $d['cantidad'];
                
                $color = "#22c55e";
                if ($d['tipo_venta'] == 'promo') $color = "#facc15";
                if ($d['tipo_venta'] == 'regalo') $color = "#38bdf8";
            ?>
                <tr>
                    <td><?= h($d['nombre']) ?></td>
                    <td><?= number_format($d['cantidad']) ?></td>
                    <td><?= money($d['precio_compra']) ?></td>
                    <td><?= money($d['precio_aplicado']) ?></td>
                    <td><?= money($total) ?></td>
                    <td><?= money($ganancia) ?></td>
                    <td style="color: <?= $color ?>; font-weight: bold;">
                        <?= strtoupper($d['tipo_venta']) ?>
                    </td>
                    <td><?= h($d['campana_origen'] ?? 'Sin campaña') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>💰 Ganancia Total: <?= money($totalGanancia) ?></h3>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>