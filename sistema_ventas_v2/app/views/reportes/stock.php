<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📦 Reporte de Stock</h2>

<!-- Selector de campaña -->
<form method="GET" style="margin-bottom: 1.5rem;">
    <label>Seleccionar campaña actual:</label>
    <select name="campana" onchange="this.form.submit()" style="width: auto; min-width: 200px;">
        <?php foreach ($campanas as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($campana_actual == $c['id']) ? 'selected' : '' ?>>
                <?= h($c['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<h3>📦 Stock Actual (Campaña: <?= h($campanas[array_search($campana_actual, array_column($campanas, 'id'))]['nombre'] ?? 'N/A') ?>)</h3>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Campaña</th>
                <th>Stock</th>
                <th>Compra</th>
                <th>Venta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actual as $p): ?>
                <tr>
                    <td><?= h($p['codigo']) ?></td>
                    <td><?= h($p['nombre']) ?></td>
                    <td><?= h($p['campana_nombre'] ?? 'Sin campaña') ?></td>
                    <td class="<?= $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde') ?>">
                        <?= number_format($p['stock']) ?>
                    </td>
                    <td><?= money($p['precio_compra']) ?></td>
                    <td><?= money($p['precio_normal']) ?></td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($actual)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay productos en esta campaña</td>
                </tr>
            <?php endif; ?>
        </tbody>
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
                <th>Compra</th>
                <th>Venta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($viejo as $p): ?>
                <tr>
                    <td><?= h($p['codigo']) ?></td>
                    <td><?= h($p['nombre']) ?></td>
                    <td><?= h($p['campana'] ?? 'Sin campaña') ?></td>
                    <td class="<?= $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde') ?>">
                        <?= number_format($p['stock']) ?>
                    </td>
                    <td><?= money($p['precio_compra']) ?></td>
                    <td><?= money($p['precio_normal']) ?></td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($viejo)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay productos de campañas anteriores</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<button onclick="window.print()" class="btn">🖨 Imprimir</button>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>