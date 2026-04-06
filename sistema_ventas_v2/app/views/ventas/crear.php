<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>🛒 Nueva Venta</h2>

<!-- Campaña activa -->
<div class="card" style="margin-bottom: 1rem; background: #0f172a;">
    <strong>📢 Campaña Activa:</strong> 
    <?= $campanaActiva ? h($campanaActiva['nombre']) : 'No hay campaña activa' ?>
</div>

<div class="card">
    <form action="<?= BASE_URL ?>ventas/guardar" method="POST" onsubmit="return validarVenta()">
        
        <!-- Campaña -->
        <div style="margin-bottom: 1rem;">
            <label>Campaña:</label>
            <select name="campana" id="campana" onchange="filtrarProductos()">
                <option value="">Seleccione una campaña</option>
                <?php foreach ($campanas as $c): ?>
                    <option value="<?= $c['id'] ?>" 
                        <?= ($campanaActiva && $campanaActiva['id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= h($c['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Cliente -->
        <div style="margin-bottom: 1rem;">
            <label>Cliente *</label>
            <select name="cliente_id" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= h($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Productos -->
        <h3>📦 Productos</h3>
        
        <div id="productos-container">
            <div class="producto-fila" style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                
                <select name="producto_id[]" class="producto_select" style="flex: 2;" onchange="calcularTotal()">
                    <option value="">Seleccione producto</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p['id'] ?>" data-campana="<?= $p['campana_id'] ?>">
                            <?= h($p['nombre']) ?> (Stock: <?= $p['stock'] ?>) - <?= money($p['precio_normal']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="cantidad[]" placeholder="Cantidad" style="flex: 1;" oninput="calcularTotal()">
                
                <input type="number" step="0.01" name="precio[]" placeholder="Precio" style="flex: 1;" oninput="calcularTotal()">
                
                <select name="tipo_venta[]" style="flex: 1;" onchange="tipoVenta(this)">
                    <option value="normal">Normal</option>
                    <option value="promo">Promo</option>
                    <option value="regalo">Regalo</option>
                </select>
                
                <span class="total-producto" style="flex: 1; display: flex; align-items: center;">$0.00</span>
                
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button type="button" onclick="agregarProducto()" class="btn">➕ Agregar producto</button>
            <button type="button" onclick="abrirModalPromociones()" class="btn" style="background: #8b5cf6;">🎯 Promociones</button>
        </div>
        
        <br><br>
        
        <!-- Pagos -->
        <h3>💳 Pagos</h3>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div>
                <label>Pago Inicial</label>
                <input type="number" step="0.01" name="pago_inicial" value="0" oninput="calcularSaldo()">
            </div>
            <div>
                <label>Abono</label>
                <input type="number" step="0.01" name="abono" value="0" oninput="calcularSaldo()">
            </div>
        </div>
        
        <br>
        
        <!-- Resumen -->
        <h3>💰 Resumen</h3>
        
        <div class="card" style="background: #0f172a;">
            <p>Total General: <strong>$<span id="total_general">0.00</span></strong></p>
            <p>Saldo Pendiente: <strong>$<span id="saldo">0.00</span></strong></p>
        </div>
        
        <button type="submit">💾 Guardar Venta</button>
        
    </form>
</div>

<!-- Incluir modal de promociones -->
<?php include_once __DIR__ . '/../promociones/aplicar.php'; ?>

<script>
// Inicializar filtro al cargar
document.addEventListener('DOMContentLoaded', function() {
    filtrarProductos();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>