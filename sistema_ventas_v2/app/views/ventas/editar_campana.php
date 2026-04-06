<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>🛠 Asignar Campaña a Venta #<?= $venta['id'] ?></h2>

<div class="card">
    <form method="POST">
        
        <label>Campaña:</label>
        <select name="campana" required>
            <option value="">Seleccione una campaña</option>
            <?php foreach ($campanas as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($venta['campana_id'] == $c['id']) ? 'selected' : '' ?>>
                    <?= h($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Guardar</button>
            <a href="<?= BASE_URL ?>ventas" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>