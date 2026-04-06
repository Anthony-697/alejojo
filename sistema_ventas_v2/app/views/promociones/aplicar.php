<?php
/**
 * Vista para el modal de promociones
 * Se carga vía AJAX o incluido en la página de ventas
 */
?>

<div id="modalPromociones" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>🎯 Promociones Especiales</h3>
            <span class="modal-close" onclick="cerrarModalPromociones()">&times;</span>
        </div>
        
        <div class="modal-body">
            <p>Selecciona un producto con promoción y la cantidad:</p>
            
            <div style="margin-bottom: 1rem;">
                <label>Producto:</label>
                <select id="productoPromoSelect" onchange="cargarRangosPromo()" style="width: 100%;">
                    <option value="">Seleccione un producto</option>
                </select>
            </div>
            
            <div id="rangosPromoInfo" style="display: none;">
                <div class="card" style="background: #0f172a; margin-bottom: 1rem;">
                    <h4>📊 Ofertas disponibles:</h4>
                    <div id="rangosLista"></div>
                </div>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 1;">
                        <label>Cantidad a comprar:</label>
                        <input type="number" id="cantidadPromo" min="1" oninput="calcularPromo()">
                    </div>
                    <div style="flex: 1;">
                        <label>Precio normal:</label>
                        <input type="text" id="precioNormalMostrar" readonly style="background: #334155;">
                    </div>
                </div>
                
                <div id="resultadoPromo" style="margin-top: 1rem; padding: 1rem; background: #0f172a; border-radius: 8px; display: none;">
                    <p><strong>💰 Precio normal total:</strong> $<span id="precioNormalTotal">0.00</span></p>
                    <p><strong>🎯 Precio con promoción:</strong> $<span id="precioPromoTotal">0.00</span></p>
                    <p><strong>💚 Ahorro:</strong> $<span id="ahorroTotal">0.00</span></p>
                    <p><strong>📦 Promoción:</strong> <span id="promoNombre"></span></p>
                </div>
                
                <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button class="btn" style="background: #64748b;" onclick="cerrarModalPromociones()">Cancelar</button>
                    <button class="btn" id="btnAgregarPromo" onclick="agregarProductoConPromo()" style="display: none; background: #22c55e;">➕ Agregar a la venta</button>
                </div>
            </div>
            
            <div id="cargandoPromo" style="text-align: center; padding: 2rem; display: none;">
                <div>Cargando...</div>
            </div>
        </div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #1e293b;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid #334155;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 1rem;
}

.modal-close {
    font-size: 1.5rem;
    cursor: pointer;
    color: #94a3b8;
}

.modal-close:hover {
    color: #ef4444;
}
</style>

<script>
let productosPromo = [];
let promoSeleccionada = null;
let rangoSeleccionado = null;

function abrirModalPromociones() {
    document.getElementById('modalPromociones').style.display = 'flex';
    cargarProductosConPromo();
}

function cerrarModalPromociones() {
    document.getElementById('modalPromociones').style.display = 'none';
    document.getElementById('productoPromoSelect').value = '';
    document.getElementById('rangosPromoInfo').style.display = 'none';
    document.getElementById('resultadoPromo').style.display = 'none';
    document.getElementById('cantidadPromo').value = '';
    productosPromo = [];
    promoSeleccionada = null;
    rangoSeleccionado = null;
}

function cargarProductosConPromo() {
    document.getElementById('cargandoPromo').style.display = 'block';
    
    fetch('<?= BASE_URL ?>promociones/productos')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cargandoPromo').style.display = 'none';
            
            if (data.success) {
                productosPromo = data.productos;
                const select = document.getElementById('productoPromoSelect');
                select.innerHTML = '<option value="">Seleccione un producto</option>';
                
                productosPromo.forEach(p => {
                    select.innerHTML += `<option value="${p.id}">${p.nombre} (Normal: $${parseFloat(p.precio_normal).toFixed(2)})</option>`;
                });
            } else {
                alert('Error al cargar productos con promoción');
            }
        })
        .catch(error => {
            document.getElementById('cargandoPromo').style.display = 'none';
            console.error('Error:', error);
            alert('Error al cargar productos');
        });
}

function cargarRangosPromo() {
    const productoId = document.getElementById('productoPromoSelect').value;
    if (!productoId) {
        document.getElementById('rangosPromoInfo').style.display = 'none';
        return;
    }
    
    const producto = productosPromo.find(p => p.id == productoId);
    if (!producto) return;
    
    promoSeleccionada = producto;
    
    const rangosLista = document.getElementById('rangosLista');
    rangosLista.innerHTML = '';
    
    producto.rangos.forEach(rango => {
        const texto = rango.cantidad_max 
            ? `${rango.cantidad_min} a ${rango.cantidad_max} productos → $${parseFloat(rango.precio_promo).toFixed(2)}`
            : `${rango.cantidad_min}+ productos → $${parseFloat(rango.precio_promo).toFixed(2)}`;
        rangosLista.innerHTML += `<div style="margin: 5px 0;">• ${texto}</div>`;
    });
    
    document.getElementById('rangosPromoInfo').style.display = 'block';
    document.getElementById('precioNormalMostrar').value = `$${parseFloat(producto.precio_normal).toFixed(2)} c/u`;
    document.getElementById('resultadoPromo').style.display = 'none';
    document.getElementById('btnAgregarPromo').style.display = 'none';
    document.getElementById('cantidadPromo').value = '';
}

function calcularPromo() {
    const cantidad = parseInt(document.getElementById('cantidadPromo').value);
    const producto = promoSeleccionada;
    
    if (!cantidad || cantidad < 1) {
        document.getElementById('resultadoPromo').style.display = 'none';
        document.getElementById('btnAgregarPromo').style.display = 'none';
        return;
    }
    
    // Buscar el mejor rango para esta cantidad
    let mejorRango = null;
    for (const rango of producto.rangos) {
        if (cantidad >= rango.cantidad_min) {
            if (!rango.cantidad_max || cantidad <= rango.cantidad_max) {
                mejorRango = rango;
            }
        }
    }
    
    if (!mejorRango) {
        alert('No hay promoción disponible para esta cantidad');
        document.getElementById('resultadoPromo').style.display = 'none';
        document.getElementById('btnAgregarPromo').style.display = 'none';
        return;
    }
    
    rangoSeleccionado = mejorRango;
    
    const precioNormalTotal = cantidad * parseFloat(producto.precio_normal);
    const precioPromoTotal = parseFloat(mejorRango.precio_promo);
    const ahorro = precioNormalTotal - precioPromoTotal;
    
    document.getElementById('precioNormalTotal').innerText = precioNormalTotal.toFixed(2);
    document.getElementById('precioPromoTotal').innerText = precioPromoTotal.toFixed(2);
    document.getElementById('ahorroTotal').innerText = ahorro.toFixed(2);
    document.getElementById('promoNombre').innerText = `Oferta: ${mejorRango.cantidad_min} productos por $${parseFloat(mejorRango.precio_promo).toFixed(2)}`;
    
    document.getElementById('resultadoPromo').style.display = 'block';
    document.getElementById('btnAgregarPromo').style.display = 'block';
}

function agregarProductoConPromo() {
    const cantidad = parseInt(document.getElementById('cantidadPromo').value);
    const producto = promoSeleccionada;
    
    if (!cantidad || !rangoSeleccionado) {
        alert('Seleccione una cantidad válida');
        return;
    }
    
    // Agregar a la tabla de venta
    const container = document.getElementById('productos-container');
    const template = container.querySelector('.producto-fila');
    if (!template) {
        alert('Error: No se encontró el contenedor de productos');
        cerrarModalPromociones();
        return;
    }
    
    const nuevo = template.cloneNode(true);
    
    // Seleccionar el producto
    const selectProducto = nuevo.querySelector('.producto_select');
    if (selectProducto) {
        selectProducto.value = producto.id;
    }
    
    // Configurar cantidad
    const inputCantidad = nuevo.querySelector('[name="cantidad[]"]');
    if (inputCantidad) {
        inputCantidad.value = cantidad;
    }
    
    // Configurar precio (usando el precio promocional total dividido entre cantidad)
    const precioUnitario = parseFloat(rangoSeleccionado.precio_promo) / cantidad;
    const inputPrecio = nuevo.querySelector('[name="precio[]"]');
    if (inputPrecio) {
        inputPrecio.value = precioUnitario.toFixed(2);
        inputPrecio.readOnly = true;
    }
    
    // Marcar como promoción
    const selectTipo = nuevo.querySelector('[name="tipo_venta[]"]');
    if (selectTipo) {
        selectTipo.value = 'promo';
    }
    
    // Agregar atributo para recordar que es promoción
    nuevo.dataset.esPromo = 'true';
    
    // Limpiar total
    const totalSpan = nuevo.querySelector('.total-producto');
    if (totalSpan) {
        totalSpan.innerText = '$0.00';
    }
    
    // Limpiar otros inputs
    nuevo.querySelectorAll('input, select').forEach(input => {
        if (input.type === 'number' && input.name !== 'cantidad[]' && input.name !== 'precio[]') {
            if (input.name !== 'cantidad[]' && input.name !== 'precio[]') {
                // No hacer nada
            }
        }
    });
    
    container.appendChild(nuevo);
    
    // Recalcular totales
    if (typeof calcularTotal === 'function') {
        calcularTotal();
    }
    
    cerrarModalPromociones();
}
</script>