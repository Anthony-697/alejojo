<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>✏ Editar Cliente</h2>

<div class="card">
    <form action="<?= BASE_URL ?>clientes/actualizar" method="POST">
        
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
        
        <label>Nombre *</label>
        <input type="text" name="nombre" value="<?= h($cliente['nombre']) ?>" required>
        
        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= h($cliente['telefono']) ?>">
        
        <label>Email</label>
        <input type="email" name="email" value="<?= h($cliente['email']) ?>">
        
        <label>Dirección</label>
        <textarea name="direccion" rows="3"><?= h($cliente['direccion']) ?></textarea>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Actualizar</button>
            <a href="<?= BASE_URL ?>clientes" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>