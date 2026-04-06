<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>💳 Abonar a Venta #<?= $venta_id ?></h2>

<div class="card">
    <p><strong>Total:</strong> <?= money($data['total']) ?></p>
    <p><strong>Pagado:</strong> <?= money($total_pagado) ?></p>
    <p><strong>Saldo actual:</strong> $<span id="saldo_actual"><?= number_format($data['saldo'], 2) ?></span></p>
    
    <form action="<?= BASE_URL ?>pagos/guardar_abono" method="POST">
        
        <input type="hidden" name="venta_id" value="<?= $venta_id ?>">
        
        <label>Nuevo Abono:</label>
        <input type="number" step="0.01" name="abono" id="abono" 
               max="<?= $data['saldo'] ?>" required oninput="calcularNuevoSaldo()">
        
        <p><strong>Saldo después:</strong> $<span id="saldo_nuevo"><?= number_format($data['saldo'], 2) ?></span></p>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💰 Guardar Abono</button>
            <a href="<?= BASE_URL ?>ventas" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>