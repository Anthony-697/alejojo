<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>🎯 Nueva Promoción por Cantidad</h2>

<div class="card">
    <form action="<?= BASE_URL ?>promociones/guardar" method="POST" id="formPromocion">
        
        <label>Nombre de la Promoción *</label>
        <input type="text" name="nombre" required autofocus>
        
        <label>Descripción</label>
        <textarea name="descripcion" rows="2" placeholder="Ej: Compra más y ahorra: 1x$8.99, 2x$12.99, 3x$15.99"></textarea>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="flex: 1;">
                <label>Fecha Inicio (opcional)</label>
                <input type="date" name="fecha_inicio">
            </div>
            <div style="flex: 1;">
                <label>Fecha Fin (opcional)</label>
                <input type="date" name="fecha_fin">
            </div>
        </div>
        
        <div style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="activo" checked> Promoción Activa
            </label>
        </div>
        
        <input type="hidden" name="tipo" value="cantidad">
        
        <hr style="margin: 1.5rem 0; border-color: #334155;">
        
        <h3>📦 Rangos de Promoción</h3>
        <p style="font-size: 14px; color: #94a3b8; margin-bottom: 1rem;">
            Ejemplo: 1 producto = $8.99, 2 productos = $12.99, 3 productos = $15.99
        </p>
        
        <div id="rangos-container">
            <div class="rango-fila" style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; align-items: center;">
                <select name="producto_id[]" class="producto-select" style="flex: 2;" required>
                    <option value="">Seleccione producto</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= h($p['nombre']) ?> (Normal: $<?= number_format($p['precio_normal'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="cantidad_min[]" placeholder="Cantidad min" style="flex: 1;" required>
                <input type="number" name="cantidad_max[]" placeholder="Cantidad max (opcional)" style="flex: 1;">
                <input type="number" step="0.01" name="precio_promo[]" placeholder="Precio promoción" style="flex: 1;" required>
                
                <button type="button" class="btn-eliminar-rango" style="background: #ef4444; padding: 6px 12px;" onclick="eliminarRango(this)">✖</button>
            </div>
        </div>
        
        <button type="button" onclick="agregarRango()" class="btn" style="margin-top: 0.5rem;">➕ Agregar Rango</button>
        
        <hr style="margin: 1.5rem 0; border-color: #334155;">
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit">💾 Guardar Promoción</button>
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

<style>
.btn-eliminar-rango:hover {
    background: #dc2626 !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>