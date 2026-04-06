<div id="modalPromociones" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:#1e293b; border-radius:16px; width:90%; max-width:500px; padding:20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <h3>🎯 Promociones</h3>
            <button onclick="cerrarModalPromociones()" style="background:#ef4444; border:none; color:white; padding:5px 10px; border-radius:8px;">✖</button>
        </div>
        <div>
            <label>Producto:</label>
            <select id="productoPromoSelect" style="width:100%; padding:8px; border-radius:8px; margin-bottom:10px;">
                <option value="">Seleccione</option>
            </select>
        </div>
        <div id="rangosPromoInfo" style="display:none;">
            <div style="background:#0f172a; padding:10px; border-radius:8px; margin-bottom:10px;">
                <strong>Ofertas:</strong>
                <div id="rangosLista"></div>
            </div>
            <label>Cantidad:</label>
            <input type="number" id="cantidadPromo" min="1" style="width:100%; padding:8px; border-radius:8px; margin-bottom:10px;">
            <div id="resultadoPromo" style="display:none; background:#0f172a; padding:10px; border-radius:8px; margin-bottom:10px;">
                <p>💰 Precio normal: $<span id="precioNormalTotal">0</span></p>
                <p>🎯 Precio promoción: $<span id="precioPromoTotal">0</span></p>
                <p>💚 Ahorro: $<span id="ahorroTotal">0</span></p>
            </div>
            <button onclick="agregarProductoConPromo()" class="btn" style="width:100%;">➕ Agregar</button>
        </div>
    </div>
</div>

<script>
var productosPromo = [];
var promoSeleccionada = null;
var rangoSeleccionado = null;

function abrirModalPromociones() {
    document.getElementById('modalPromociones').style.display = 'flex';
    cargarProductosConPromo();
}

function cerrarModalPromociones() {
    document.getElementById('modalPromociones').style.display = 'none';
    document.getElementById('productoPromoSelect').value = '';
    document.getElementById('rangosPromoInfo').style.display = 'none';
    document.getElementById('resultadoPromo').style.display = 'none';
}

function cargarProductosConPromo() {
    fetch('<?= BASE_URL ?>promociones/productos')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                productosPromo = data.productos;
                var select = document.getElementById('productoPromoSelect');
                select.innerHTML = '<option value="">Seleccione</option>';
                productosPromo.forEach(p => {
                    select.innerHTML += '<option value="' + p.id + '">' + p.nombre + ' ($' + parseFloat(p.precio_normal).toFixed(2) + ')</option>';
                });
            }
        });
}

document.getElementById('productoPromoSelect').addEventListener('change', function() {
    var productoId = this.value;
    if(!productoId) { document.getElementById('rangosPromoInfo').style.display = 'none'; return; }
    var producto = productosPromo.find(p => p.id == productoId);
    if(producto) {
        promoSeleccionada = producto;
        var rangosLista = document.getElementById('rangosLista');
        rangosLista.innerHTML = '';
        producto.rangos.forEach(r => {
            rangosLista.innerHTML += '<div>' + r.cantidad_min + (r.cantidad_max ? ' a ' + r.cantidad_max : '+') + ' productos → $' + parseFloat(r.precio_promo).toFixed(2) + '</div>';
        });
        document.getElementById('rangosPromoInfo').style.display = 'block';
        document.getElementById('cantidadPromo').value = '';
        document.getElementById('resultadoPromo').style.display = 'none';
    }
});

document.getElementById('cantidadPromo').addEventListener('input', function() {
    var cantidad = parseInt(this.value);
    var producto = promoSeleccionada;
    if(!cantidad || cantidad < 1 || !producto) return;
    var mejorRango = null;
    for(var r of producto.rangos) {
        if(cantidad >= r.cantidad_min) {
            if(!r.cantidad_max || cantidad <= r.cantidad_max) mejorRango = r;
        }
    }
    if(mejorRango) {
        rangoSeleccionado = mejorRango;
        var precioNormalTotal = cantidad * producto.precio_normal;
        var precioPromoTotal = mejorRango.precio_promo;
        var ahorro = precioNormalTotal - precioPromoTotal;
        document.getElementById('precioNormalTotal').innerText = precioNormalTotal.toFixed(2);
        document.getElementById('precioPromoTotal').innerText = precioPromoTotal.toFixed(2);
        document.getElementById('ahorroTotal').innerText = ahorro.toFixed(2);
        document.getElementById('resultadoPromo').style.display = 'block';
    }
});

function agregarProductoConPromo() {
    if(!rangoSeleccionado || !promoSeleccionada) { alert('Seleccione una cantidad válida'); return; }
    var cantidad = parseInt(document.getElementById('cantidadPromo').value);
    var container = document.getElementById('productos-container');
    var template = container.querySelector('.producto-fila');
    var nuevo = template.cloneNode(true);
    nuevo.querySelector('.producto_select').value = promoSeleccionada.id;
    nuevo.querySelector('[name="cantidad[]"]').value = cantidad;
    var precioUnitario = rangoSeleccionado.precio_promo / cantidad;
    nuevo.querySelector('[name="precio[]"]').value = precioUnitario.toFixed(2);
    nuevo.querySelector('[name="tipo_venta[]"]').value = 'promo';
    nuevo.querySelector('.total-producto').innerText = '$0.00';
    container.appendChild(nuevo);
    calcularTotal();
    cerrarModalPromociones();
}
</script>