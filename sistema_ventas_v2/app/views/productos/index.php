<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📦 Productos</h2>

<div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
    <a href="<?= BASE_URL ?>productos/crear" class="btn">➕ Nuevo Producto</a>
    <a href="<?= BASE_URL ?>productos/importar" class="btn">📥 Importar CSV</a>
</div>

<form method="GET" style="margin-bottom: 1.5rem;">
    <label>Filtrar por campaña:</label>
    <select name="campana" onchange="this.form.submit()" style="width: auto; min-width: 200px;">
        <option value="">Todas las campañas</option>
        <?php foreach ($campanas as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($filtro == $c['id']) ? 'selected' : '' ?>>
                <?= h($c['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Compra</th>
                <th>Venta</th>
                <th>Stock</th>
                <th>Campaña</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= h($p['codigo']) ?></td>
                    <td><?= h($p['nombre']) ?></td>
                    <td><?= money($p['precio_compra']) ?></td>
                    <td><?= money($p['precio_normal']) ?></td>
                    <td class="<?= $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde') ?>">
                        <?= number_format($p['stock']) ?>
                    </td>
                    <td><?= h($p['campana_nombre'] ?? 'Sin campaña') ?></td>
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>productos/editar/<?= $p['id'] ?>" class="btn-editar">✏ Editar</a>
                        <a href="<?= BASE_URL ?>productos/eliminar/<?= $p['id'] ?>" 
                           class="btn-eliminar"
                           onclick="return confirm('¿Eliminar este producto?')">🗑 Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No hay productos registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>