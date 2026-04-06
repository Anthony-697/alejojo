// Filtrar productos por campaña
function filtrarProductos() {
    const campana = document.getElementById('campana')?.value;
    if (!campana) return;
    
    const selects = document.querySelectorAll('.producto_select');
    selects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            if (option.value && option.dataset.campana) {
                option.style.display = option.dataset.campana == campana ? 'block' : 'none';
            }
        });
    });
}

// Calcular totales en venta
function calcularTotal() {
    let totalGeneral = 0;
    
    document.querySelectorAll('.producto-fila').forEach(fila => {
        const cantidad = parseFloat(fila.querySelector('[name="cantidad[]"]')?.value) || 0;
        const precio = parseFloat(fila.querySelector('[name="precio[]"]')?.value) || 0;
        const total = cantidad * precio;
        
        const totalSpan = fila.querySelector('.total-producto');
        if (totalSpan) totalSpan.innerText = '$' + total.toFixed(2);
        
        totalGeneral += total;
    });
    
    const totalGeneralSpan = document.getElementById('total_general');
    if (totalGeneralSpan) totalGeneralSpan.innerText = totalGeneral.toFixed(2);
    
    calcularSaldo();
}

// Calcular saldo pendiente
function calcularSaldo() {
    const total = parseFloat(document.getElementById('total_general')?.innerText) || 0;
    const inicial = parseFloat(document.querySelector('[name="pago_inicial"]')?.value) || 0;
    const abono = parseFloat(document.querySelector('[name="abono"]')?.value) || 0;
    
    const saldo = total - (inicial + abono);
    const saldoSpan = document.getElementById('saldo');
    if (saldoSpan) saldoSpan.innerText = saldo.toFixed(2);
}

// Agregar fila de producto
function agregarProducto() {
    const container = document.getElementById('productos-container');
    if (!container) return;
    
    const template = container.querySelector('.producto-fila');
    if (!template) return;
    
    const nuevo = template.cloneNode(true);
    nuevo.querySelectorAll('input, select').forEach(input => {
        if (input.type !== 'button') {
            input.value = '';
        }
    });
    
    const totalSpan = nuevo.querySelector('.total-producto');
    if (totalSpan) totalSpan.innerText = '$0.00';
    
    container.appendChild(nuevo);
}

// Manejar tipo de venta
function tipoVenta(select) {
    const fila = select.closest('.producto-fila');
    const precioInput = fila.querySelector('[name="precio[]"]');
    
    if (select.value === 'regalo') {
        precioInput.value = 0;
        precioInput.readOnly = true;
    } else {
        precioInput.readOnly = false;
    }
    
    calcularTotal();
}

// Validar venta antes de enviar
function validarVenta() {
    const productos = document.querySelectorAll('[name="producto_id[]"]');
    let tieneProducto = false;
    
    productos.forEach(p => {
        if (p.value) tieneProducto = true;
    });
    
    if (!tieneProducto) {
        alert('❌ Debes agregar al menos un producto');
        return false;
    }
    
    return true;
}

// Calcular nuevo saldo en abonos
function calcularNuevoSaldo() {
    const saldoActual = parseFloat(document.getElementById('saldo_actual')?.innerText) || 0;
    const abono = parseFloat(document.getElementById('abono')?.value) || 0;
    const nuevoSaldo = saldoActual - abono;
    
    const saldoNuevoSpan = document.getElementById('saldo_nuevo');
    if (saldoNuevoSpan) saldoNuevoSpan.innerText = nuevoSaldo.toFixed(2);
}

// Auto-cerrar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});