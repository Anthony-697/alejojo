<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📊 Dashboard General</h2>

<div class="dashboard-grid">
    <div class="card">
        <h3>💰 Ventas Totales</h3>
        <p class="big-number"><?= money($ventasTotal) ?></p>
    </div>
    
    <div class="card">
        <h3>📈 Ganancia Total</h3>
        <p class="big-number"><?= money($gananciaTotal) ?></p>
    </div>
    
    <div class="card">
        <h3>📦 Productos Vendidos</h3>
        <p class="big-number"><?= number_format($productosVendidos) ?></p>
    </div>
</div>

<h2>📊 Dashboard por Campaña</h2>

<div class="dashboard-grid">
    <?php foreach ($campanas as $c): ?>
        <div class="card">
            <h3>📦 <?= h($c['nombre']) ?></h3>
            <p>💰 Ventas: <strong><?= money($c['ventas']) ?></strong></p>
            <p>📈 Ganancia: <strong><?= money($c['ganancia']) ?></strong></p>
            <p>📦 Vendidos: <?= number_format($c['vendidos']) ?></p>
            <p>🗂 Productos: <?= number_format($c['total_productos']) ?></p>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($campanas)): ?>
        <div class="card">
            <p>No hay campañas registradas</p>
            <a href="<?= BASE_URL ?>campanas/crear" class="btn">➕ Crear Campaña</a>
        </div>
    <?php endif; ?>
</div>

<style>
.big-number {
    font-size: 2rem;
    font-weight: bold;
    color: #22c55e;
    margin-top: 0.5rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>