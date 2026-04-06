<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>➕ Agregar Producto a Venta #<?= $id ?></h2>

<div class="card" style="margin-bottom: 1rem;">
    <p><strong>Cliente:</strong> <?= h($venta['cliente']) ?></p>
    <p><strong>Total actual:</strong> <?= money($venta['total']) ?></p>
    <p><strong>Saldo pendiente:</strong> <?= money($venta['saldo']) ?></p>
</div>

<div class="card">
    <h3>📦 Productos ya comprados</h3>
    <?php if (empty($productosVenta)): ?>
        <p>No hay productos registrados en esta venta.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productosVenta as $pv): ?>
                        <tr>
                            <td><?= h($pv['nombre']) ?></td>
                            <td><?= number_format($pv['cantidad']) ?></td>
                            <td><?= money($pv['precio_aplicado']) ?></td>
                            <td><?= money($pv['cantidad'] * $pv['precio_aplicado']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>➕ Agregar nuevo producto</h3>
    
    <form method="POST">
        <label>Producto *</label>
        <select name="producto_id" required autofocus>
            <option value="">Seleccione un producto</option>
            <?php foreach ($productos as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= h($p['nombre']) ?> (Stock: <?= $p['stock'] ?>) - <?= money($p['precio_normal']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Cantidad *</label>
        <input type="number" name="cantidad" step="1" min="1" required>
        
        <label>Precio unitario *</label>
        <input type="number" step="0.01" name="precio" required>
        
        <label>Tipo de venta</label>
        <select name="tipo_venta">
            <option value="normal">Normal</option>
            <option value="promo">Promo</option>
            <option value="regalo">Regalo</option>
        </select>
        
        <div style="margin-top: 1rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn">➕ Agregar a la venta</button>
            <a href="<?= BASE_URL ?>ventas/ver/<?= $id ?>" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
    </form>
</div>

<div class="card" style="background: #facc15; color: #000;">
    <p><strong>⚠️ Importante:</strong></p>
    <ul style="margin-left: 1.5rem;">
        <li>Al agregar un producto, se actualizará el TOTAL de la venta</li>
        <li>El SALDO pendiente aumentará según el precio del nuevo producto</li>
        <li>El STOCK del producto se descontará automáticamente</li>
        <li>Esta acción NO se puede deshacer</li>
    </ul>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>