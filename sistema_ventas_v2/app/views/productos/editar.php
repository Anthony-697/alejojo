<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>✏ Editar Producto</h2>

<div class="card">
    <form action="<?= BASE_URL ?>productos/actualizar" method="POST">
        
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">
        
        <label>Código *</label>
        <input type="text" name="codigo" value="<?= h($producto['codigo']) ?>" required>
        
        <label>Nombre *</label>
        <input type="text" name="nombre" value="<?= h($producto['nombre']) ?>" required>
        
        <label>Precio de Compra *</label>
        <input type="number" step="0.01" name="precio_compra" value="<?= $producto['precio_compra'] ?>" required>
        
        <label>Precio de Venta *</label>
        <input type="number" step="0.01" name="precio" value="<?= $producto['precio_normal'] ?>" required>
        
        <label>Stock</label>
        <input type="number" name="stock" value="<?= $producto['stock'] ?>">
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit">💾 Actualizar</button>
            <a href="<?= BASE_URL ?>productos" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>