<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>➕ Nueva Campaña</h2>

<div class="card">
    <form action="<?= BASE_URL ?>campanas/guardar" method="POST">
        
        <label>Nombre *</label>
        <input type="text" name="nombre" required autofocus>
        
        <label>Fecha Inicio *</label>
        <input type="date" name="fecha_inicio" required>
        
        <label>Fecha Fin *</label>
        <input type="date" name="fecha_fin" required>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Guardar Campaña</button>
            <a href="<?= BASE_URL ?>campanas" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>