<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>➕ Nuevo Cliente</h2>

<div class="card">
    <form action="<?= BASE_URL ?>clientes/guardar" method="POST">
        
        <label>Nombre *</label>
        <input type="text" name="nombre" required autofocus>
        
        <label>Teléfono</label>
        <input type="text" name="telefono" placeholder="Ej: 7777-7777">
        
        <label>Email</label>
        <input type="email" name="email" placeholder="cliente@email.com">
        
        <label>Dirección</label>
        <textarea name="direccion" rows="3"></textarea>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Guardar Cliente</button>
            <a href="<?= BASE_URL ?>clientes" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>