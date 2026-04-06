<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>✏ Editar Promoción: <?= h($promocion['nombre']) ?></h2>

<div class="card">
    <form action="<?= BASE_URL ?>promociones/actualizar" method="POST" id="formPromocion">
        
        <input type="hidden" name="id" value="<?= $promocion['id'] ?>">
        
        <label>Nombre de la Promoción *</label>
        <input type="text" name="nombre" value="<?= h($promocion['nombre']) ?>" required>
        
        <label>Descripción</label>
        <textarea name="descripcion" rows="2"><?= h($promocion['descripcion']) ?></textarea>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="flex: 1;">
                <label>Fecha Inicio (opcional)</label>
                <input type="date" name="fecha_inicio" value="<?= $promocion['fecha_inicio'] ?>">
            </div>
            <div style="flex: 1;">
                <label>Fecha Fin (opcional)</label>
                <input type="date" name="fecha_fin" value="<?= $promocion['fecha_fin'] ?>">
            </div>
        </div>
        
        <div style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="activo" <?= $promocion['activo'] ? 'checked' : '' ?>> Promoción Activa
            </label>
        </div>
        
        <input type="hidden" name="tipo" value="cantidad">
        
        <hr style="margin: 1.5rem 0; border-color: #334155;">
        
        <h3>📦 Rangos de Promoción</h3>
        
        <div id="rangos-container">
            <?php foreach ($detalles as $d): ?>
                <div class="rango-fila" style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; align-items: center;">
                    <select name="producto_id[]" class="producto-select" style="flex: 2;" required>
                        <option value="">Seleccione producto</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($d['producto_id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= h($p['nombre']) ?> (Normal: $<?= number_format($p['precio_normal'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="number" name="cantidad_min[]" placeholder="Cantidad min" value="<?= $d['cantidad_min'] ?>" required style="flex: 1;">
                    <input type="number" name="cantidad_max[]" placeholder="Cantidad max (opcional)" value="<?= $d['cantidad_max'] ?>" style="flex: 1;">
                    <input type="number" step="0.01" name="precio_promo[]" placeholder="Precio promoción" value="<?= $d['precio_promo'] ?>" required style="flex: 1;">
                    
                    <button type="button" class="btn-eliminar-rango" style="background: #ef4444; padding: 6px 12px;" onclick="eliminarRango(this)">✖</button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="button" onclick="agregarRango()" class="btn" style="margin-top: 0.5rem;">➕ Agregar Rango</button>
        
        <hr style="margin: 1.5rem 0; border-color: #334155;">
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit">💾 Actualizar Promoción</button>
            <a href="<?= BASE_URL ?>promociones" class="btn" style="background: #64748b;">Cancelar</a>
        </div>
        
    </form>
</div>

<script>
function agregarRango() {
    const container = document.getElementById('rangos-container');
    const template = container.querySelector('.rango-fila');
    const nuevo = template.cloneNode(true);
    
    nuevo.querySelectorAll('input, select').forEach(input => {
        input.value = '';
    });
    
    container.appendChild(nuevo);
}

function eliminarRango(btn) {
    const container = document.getElementById('rangos-container');
    if (container.querySelectorAll('.rango-fila').length > 1) {
        btn.closest('.rango-fila').remove();
    } else {
        alert('Debe haber al menos un rango de promoción');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>