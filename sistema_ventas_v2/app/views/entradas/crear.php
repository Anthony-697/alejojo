<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📥 Registrar Compra</h2>

<div class="card">
    <form action="<?= BASE_URL ?>entradas/guardar" method="POST">
        
        <label>Campaña *</label>
        <select name="campana_id" id="campana" onchange="filtrarProductos()" required>
            <option value="">Seleccione una campaña</option>
            <?php foreach ($campanas as $c): ?>
                <option value="<?= $c['id'] ?>"><?= h($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Producto *</label>
        <select name="producto_id" id="producto" required>
            <option value="">Seleccione un producto</option>
            <?php foreach ($productos as $p): ?>
                <option value="<?= $p['id'] ?>" data-campana="<?= $p['campana_id'] ?>">
                    <?= h($p['nombre']) ?> (Stock actual: <?= $p['stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Cantidad *</label>
        <input type="number" name="cantidad" required min="1">
        
        <label>Costo Unitario *</label>
        <input type="number" step="0.01" name="costo" required>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Guardar Compra</button>
            <a href="<?= BASE_URL ?>dashboard" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<script>
function filtrarProductos() {
    const campana = document.getElementById('campana').value;
    const opciones = document.querySelectorAll('#producto option');
    
    opciones.forEach(op => {
        if (!op.value) return;
        if (op.dataset.campana == campana) {
            op.style.display = 'block';
        } else {
            op.style.display = 'none';
        }
    });
    
    document.getElementById('producto').value = '';
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>