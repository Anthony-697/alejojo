<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>➕ Nuevo Producto</h2>

<div class="card">
    <form action="<?= BASE_URL ?>productos/guardar" method="POST">
        
        <label>Código *</label>
        <input type="text" name="codigo" required autofocus>
        
        <label>Nombre *</label>
        <input type="text" name="nombre" required>
        
        <label>Precio de Compra *</label>
        <input type="number" step="0.01" name="precio_compra" required>
        
        <label>Precio de Venta *</label>
        <input type="number" step="0.01" name="precio" required>
        
        <label>Campaña *</label>
        <select name="campana_id" required>
            <option value="">Seleccione una campaña</option>
            <?php foreach ($campanas as $c): ?>
                <option value="<?= $c['id'] ?>"><?= h($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Guardar Producto</button>
            <a href="<?= BASE_URL ?>productos" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>