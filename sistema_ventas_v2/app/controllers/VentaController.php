<?php
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Campana.php';

class VentaController {
    private $venta;
    private $producto;
    private $cliente;
    private $campana;
    
    public function __construct() {
        $this->venta = new Venta();
        $this->producto = new Producto();
        $this->cliente = new Cliente();
        $this->campana = new Campana();
    }
    
    public function index() {
        requireLogin();
        $filtro = $_GET['campana'] ?? '';
        $ventas = $this->venta->getAllWithDetails($filtro);
        $campanas = $this->campana->all();
        
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Historial de Ventas</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        </head>
        <body>' . renderMenu() . '
        <div class="container">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:10px;">
                <h2>📊 Historial de Ventas</h2>
                <a href="' . BASE_URL . 'ventas/plantilla?campana=todas" target="_blank" class="btn btn-imprimir">🖨️ Imprimir Reporte General</a>
            </div>
            
            <form method="GET" style="margin-bottom:1rem; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <label>Filtrar por campaña: </label>
                <select name="campana" id="filtroCampana" onchange="this.form.submit()" style="padding:8px; border-radius:8px;">
                    <option value="">Todas</option>';
        foreach ($campanas as $c) {
            echo '<option value="' . $c['id'] . '" ' . ($filtro == $c['id'] ? 'selected' : '') . '>' . htmlspecialchars($c['nombre']) . '</option>';
        }
        echo '</select>
                <button type="button" onclick="imprimirCampanaSeleccionada()" class="btn btn-imprimir">🖨️ Imprimir esta campaña</button>
            </form>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Saldo</th><th>Campaña</th><th>Estado</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($ventas as $v) {
            $estado = ($v['saldo'] > 0) ? "Pendiente" : "Pagado";
            $claseEstado = ($v['saldo'] > 0) ? "estado-pendiente" : "estado-pagado";
            echo '<tr>
                    <td>' . $v['id'] . '</td>
                    <td>' . htmlspecialchars($v['cliente']) . '</td>
                    <td>' . date('d/m/Y H:i', strtotime($v['fecha'])) . '</td>
                    <td>$' . number_format($v['total'], 2) . '</td>
                    <td>$' . number_format($v['saldo'], 2) . '</td>
                    <td>' . htmlspecialchars($v['campana'] ?? 'Sin campaña') . '</td>
                    <td class="' . $claseEstado . '">' . $estado . '</td>
                    <td class="acciones">
                        <a href="' . BASE_URL . 'ventas/ver/' . $v['id'] . '" class="btn-ver">👁 Ver</a>
                        <a href="' . BASE_URL . 'pagos/abonar/' . $v['id'] . '" class="btn-pago">💰 Abonar</a>
                        <a href="' . BASE_URL . 'ventas/agregar_producto/' . $v['id'] . '" class="btn" style="background:#8b5cf6;">➕ Agregar</a>
                        <a href="' . BASE_URL . 'ventas/editar_campana/' . $v['id'] . '" class="btn-editar">🛠 Campaña</a>
                    </td>
                </tr>';
        }
        if (empty($ventas)) echo '<tr><td colspan="8">No hay ventas registradas</td></tr>';
        echo '</tbody></table></div></div>
        <script>
        function imprimirCampanaSeleccionada() {
            var campana = document.getElementById("filtroCampana").value;
            if(campana) {
                window.open("' . BASE_URL . 'ventas/plantilla?campana=" + campana, "_blank");
            } else {
                alert("Seleccione una campaña primero");
            }
        }
        </script>
        </body></html>';
    }
    
    public function crear() {
        requireLogin();
        
        $campanaActiva = $this->campana->getActive();
        $campanas = $this->campana->all();
        $productos = $this->producto->getByCampana(null, true);
        $clientes = $this->cliente->all();
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nueva Venta</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                .producto-fila{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;}
                .producto-fila select{flex:2; padding:8px; border-radius:8px;}
                .producto-fila input{flex:1; padding:8px; border-radius:8px;}
                .producto-fila span{flex:1; display:flex; align-items:center;}
                .total-producto{font-weight:bold;color:#22c55e;}
                .btn-promo{background:#8b5cf6; margin-left:10px;}
                .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:1000;justify-content:center;align-items:center;}
                .modal-content{background:#1e293b;border-radius:20px;width:90%;max-width:500px;padding:20px;}
                .modal-header{display:flex;justify-content:space-between;margin-bottom:15px;}
                .close-modal{background:#ef4444;border:none;color:white;padding:8px 15px;border-radius:10px;cursor:pointer;}
                .promo-info{background:#0f172a;padding:15px;border-radius:10px;margin-bottom:15px;}
            </style>
        </head>
        <body>' . renderMenu() . '
        <div class="container">
            <h2>🛒 Nueva Venta</h2>
            <div class="card" style="background:#0f172a;">
                <strong>📢 Campaña Activa:</strong> ' . ($campanaActiva ? htmlspecialchars($campanaActiva['nombre']) : 'No hay campaña activa') . '
            </div>
            <div class="card">
                <form action="' . BASE_URL . 'ventas/guardar" method="POST" onsubmit="return validarVenta()">
                    <div><label>Campaña:</label><select name="campana" id="campana"><option value="">Seleccione</option>';
        foreach ($campanas as $c) {
            $selected = ($campanaActiva && $campanaActiva['id'] == $c['id']) ? 'selected' : '';
            echo '<option value="' . $c['id'] . '" ' . $selected . '>' . htmlspecialchars($c['nombre']) . '</option>';
        }
        echo '</select></div>
                    <div><label>Cliente *</label><select name="cliente_id" required><option value="">Seleccione</option>';
        foreach ($clientes as $c) {
            echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['nombre']) . '</option>';
        }
        echo '</select></div>
                    
                    <h3>📦 Productos</h3>
                    <div id="productos-container">
                        <div class="producto-fila">
                            <select name="producto_id[]" class="producto_select" onchange="calcularTotal()">
                                <option value="">Seleccione producto</option>';
        foreach ($productos as $p) {
            echo '<option value="' . $p['id'] . '" data-campana="' . $p['campana_id'] . '">' . htmlspecialchars($p['nombre']) . ' (Stock: ' . $p['stock'] . ') - $' . number_format($p['precio_normal'], 2) . '</option>';
        }
        echo '</select>
                            <input type="number" name="cantidad[]" placeholder="Cantidad" oninput="calcularTotal()">
                            <input type="number" step="0.01" name="precio[]" placeholder="Precio" oninput="calcularTotal()">
                            <select name="tipo_venta[]" onchange="tipoVenta(this)">
                                <option value="normal">Normal</option>
                                <option value="promo">Promo</option>
                                <option value="regalo">Regalo</option>
                            </select>
                            <span class="total-producto">$0.00</span>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="button" onclick="agregarProducto()" class="btn">➕ Agregar producto</button>
                        <button type="button" onclick="abrirModalPromociones()" class="btn btn-promo">🎯 Promociones</button>
                    </div>
                    <hr>
                    <h3>💳 Pagos</h3>
                    <div style="display:flex; gap:20px;">
                        <div style="flex:1;"><label>Pago Inicial</label><input type="number" step="0.01" name="pago_inicial" value="0" oninput="calcularSaldo()"></div>
                        <div style="flex:1;"><label>Abono</label><input type="number" step="0.01" name="abono" value="0" oninput="calcularSaldo()"></div>
                    </div>
                    <hr>
                    <h3>💰 Resumen</h3>
                    <div class="card" style="background:#0f172a;">
                        <p>Total General: <strong>$<span id="total_general">0.00</span></strong></p>
                        <p>Saldo Pendiente: <strong>$<span id="saldo">0.00</span></strong></p>
                    </div>
                    <button type="submit">💾 Guardar Venta</button>
                </form>
            </div>
        </div>
        
        <!-- Modal de Promociones -->
        <div id="modalPromociones" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>🎯 Promociones Especiales</h3>
                    <button class="close-modal" onclick="cerrarModalPromociones()">✖ Cerrar</button>
                </div>
                <div>
                    <label>Producto:</label>
                    <select id="productoPromoSelect" style="width:100%; padding:10px; border-radius:10px;">
                        <option value="">Seleccione un producto</option>
                    </select>
                </div>
                <div id="rangosPromoInfo" style="display:none;">
                    <div class="promo-info">
                        <strong>📊 Ofertas disponibles:</strong>
                        <div id="rangosLista" style="margin-top:10px;"></div>
                    </div>
                    <label>Cantidad:</label>
                    <input type="number" id="cantidadPromo" min="1" style="width:100%; padding:10px; border-radius:10px;">
                    <div id="resultadoPromo" style="display:none; background:#0f172a; padding:15px; border-radius:10px; margin-bottom:15px;">
                        <p>💰 Precio normal total: $<span id="precioNormalTotal">0.00</span></p>
                        <p>🎯 Precio con promoción: $<span id="precioPromoTotal">0.00</span></p>
                        <p>💚 Ahorro: $<span id="ahorroTotal">0.00</span></p>
                    </div>
                    <button onclick="agregarProductoConPromo()" style="background:#22c55e; border:none; padding:12px; border-radius:10px; color:white; cursor:pointer; width:100%; font-weight:bold;">➕ Agregar a la venta</button>
                </div>
            </div>
        </div>
        
        <script>
        var productosPromo = [];
        var promoSeleccionada = null;
        var rangoSeleccionado = null;
        
        function abrirModalPromociones() {
            document.getElementById("modalPromociones").style.display = "flex";
            cargarProductosConPromo();
        }
        
        function cerrarModalPromociones() {
            document.getElementById("modalPromociones").style.display = "none";
            document.getElementById("productoPromoSelect").value = "";
            document.getElementById("rangosPromoInfo").style.display = "none";
            document.getElementById("resultadoPromo").style.display = "none";
            document.getElementById("cantidadPromo").value = "";
        }
        
        function cargarProductosConPromo() {
            fetch("/sistema_ventas_v2/promociones/productos")
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if(data.success) {
                        productosPromo = data.productos;
                        var select = document.getElementById("productoPromoSelect");
                        select.innerHTML = "<option value=\"\">Seleccione un producto</option>";
                        for(var i = 0; i < productosPromo.length; i++) {
                            var p = productosPromo[i];
                            select.innerHTML += "<option value=\"" + p.id + "\">" + p.nombre + " ($" + parseFloat(p.precio_normal).toFixed(2) + ")</option>";
                        }
                    }
                })
                .catch(function(error) { console.error("Error:", error); });
        }
        
        document.getElementById("productoPromoSelect").addEventListener("change", function() {
            var productoId = this.value;
            if(!productoId) {
                document.getElementById("rangosPromoInfo").style.display = "none";
                return;
            }
            var producto = null;
            for(var i = 0; i < productosPromo.length; i++) {
                if(productosPromo[i].id == productoId) {
                    producto = productosPromo[i];
                    break;
                }
            }
            if(producto) {
                promoSeleccionada = producto;
                var rangosLista = document.getElementById("rangosLista");
                rangosLista.innerHTML = "";
                for(var j = 0; j < producto.rangos.length; j++) {
                    var r = producto.rangos[j];
                    var texto = r.cantidad_min;
                    if(r.cantidad_max) texto += " a " + r.cantidad_max;
                    else texto += "+";
                    rangosLista.innerHTML += "<div>📦 " + texto + " productos → $" + parseFloat(r.precio_promo).toFixed(2) + "</div>";
                }
                document.getElementById("rangosPromoInfo").style.display = "block";
                document.getElementById("cantidadPromo").value = "";
                document.getElementById("resultadoPromo").style.display = "none";
                rangoSeleccionado = null;
            }
        });
        
        document.getElementById("cantidadPromo").addEventListener("input", function() {
            var cantidad = parseInt(this.value);
            var producto = promoSeleccionada;
            if(!cantidad || cantidad < 1 || !producto) return;
            
            var mejorRango = null;
            for(var i = 0; i < producto.rangos.length; i++) {
                var r = producto.rangos[i];
                if(cantidad >= r.cantidad_min) {
                    if(!r.cantidad_max || cantidad <= r.cantidad_max) {
                        mejorRango = r;
                    }
                }
            }
            
            if(mejorRango) {
                rangoSeleccionado = mejorRango;
                var precioNormalTotal = cantidad * producto.precio_normal;
                var precioPromoTotal = mejorRango.precio_promo;
                var ahorro = precioNormalTotal - precioPromoTotal;
                document.getElementById("precioNormalTotal").innerText = precioNormalTotal.toFixed(2);
                document.getElementById("precioPromoTotal").innerText = precioPromoTotal.toFixed(2);
                document.getElementById("ahorroTotal").innerText = ahorro.toFixed(2);
                document.getElementById("resultadoPromo").style.display = "block";
            } else {
                document.getElementById("resultadoPromo").style.display = "none";
            }
        });
        
        function agregarProductoConPromo() {
            if(!rangoSeleccionado || !promoSeleccionada) {
                alert("Seleccione una cantidad válida");
                return;
            }
            var cantidad = parseInt(document.getElementById("cantidadPromo").value);
            var container = document.getElementById("productos-container");
            var template = container.querySelector(".producto-fila");
            var nuevo = template.cloneNode(true);
            
            var selectProducto = nuevo.querySelector(".producto_select");
            selectProducto.value = promoSeleccionada.id;
            
            var inputCantidad = nuevo.querySelector("input[name=\"cantidad[]\"]");
            inputCantidad.value = cantidad;
            
            var precioUnitario = rangoSeleccionado.precio_promo / cantidad;
            var inputPrecio = nuevo.querySelector("input[name=\"precio[]\"]");
            inputPrecio.value = precioUnitario.toFixed(2);
            
            var selectTipo = nuevo.querySelector("select[name=\"tipo_venta[]\"]");
            selectTipo.value = "promo";
            
            nuevo.querySelector(".total-producto").innerText = "$0.00";
            container.appendChild(nuevo);
            
            if(typeof calcularTotal === "function") calcularTotal();
            cerrarModalPromociones();
        }
        
        function filtrarProductos() {
            var campana = document.getElementById("campana").value;
            var selects = document.querySelectorAll(".producto_select");
            for(var s = 0; s < selects.length; s++) {
                var select = selects[s];
                var options = select.querySelectorAll("option");
                for(var o = 0; o < options.length; o++) {
                    var option = options[o];
                    if(option.value && option.dataset.campana) {
                        if(campana === "" || option.dataset.campana == campana) {
                            option.style.display = "block";
                        } else {
                            option.style.display = "none";
                        }
                    }
                }
                if(select.value) {
                    var selectedOption = select.options[select.selectedIndex];
                    if(selectedOption && selectedOption.style.display === "none") {
                        select.value = "";
                        calcularTotal();
                    }
                }
            }
        }
        
        function calcularTotal() {
            var totalGeneral = 0;
            var filas = document.querySelectorAll(".producto-fila");
            for(var f = 0; f < filas.length; f++) {
                var fila = filas[f];
                var cantidad = parseFloat(fila.querySelector("input[name=\"cantidad[]\"]").value) || 0;
                var precio = parseFloat(fila.querySelector("input[name=\"precio[]\"]").value) || 0;
                var total = cantidad * precio;
                fila.querySelector(".total-producto").innerText = "$" + total.toFixed(2);
                totalGeneral += total;
            }
            document.getElementById("total_general").innerText = totalGeneral.toFixed(2);
            calcularSaldo();
        }
        
        function calcularSaldo() {
            var total = parseFloat(document.getElementById("total_general").innerText) || 0;
            var inicial = parseFloat(document.querySelector("input[name=\"pago_inicial\"]").value) || 0;
            var abono = parseFloat(document.querySelector("input[name=\"abono\"]").value) || 0;
            var saldo = total - (inicial + abono);
            document.getElementById("saldo").innerText = saldo.toFixed(2);
        }
        
        function agregarProducto() {
            var container = document.getElementById("productos-container");
            var template = container.querySelector(".producto-fila");
            var nuevo = template.cloneNode(true);
            nuevo.querySelectorAll("input, select").forEach(function(input) {
                if(input.type !== "button") input.value = "";
            });
            nuevo.querySelector(".total-producto").innerText = "$0.00";
            container.appendChild(nuevo);
            filtrarProductos();
        }
        
        function tipoVenta(select) {
            var fila = select.closest(".producto-fila");
            var precioInput = fila.querySelector("input[name=\"precio[]\"]");
            if(select.value === "regalo") {
                precioInput.value = 0;
                precioInput.readOnly = true;
            } else {
                precioInput.readOnly = false;
            }
            calcularTotal();
        }
        
        function validarVenta() {
            var productos = document.querySelectorAll("select[name=\"producto_id[]\"]");
            for(var p = 0; p < productos.length; p++) {
                if(productos[p].value) return true;
            }
            alert("❌ Debes agregar al menos un producto");
            return false;
        }
        
        document.getElementById("campana").addEventListener("change", filtrarProductos);
        document.addEventListener("DOMContentLoaded", filtrarProductos);
        </script>
        </body>
        </html>';
    }
    
    public function guardar() {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('ventas');
        }
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $cliente_id = (int)($_POST['cliente_id'] ?? 0);
            $campana_id = $_POST['campana'] ?? null;
            $productos = $_POST['producto_id'] ?? [];
            $cantidades = $_POST['cantidad'] ?? [];
            $precios = $_POST['precio'] ?? [];
            $tipos = $_POST['tipo_venta'] ?? [];
            $pago_inicial = (float)($_POST['pago_inicial'] ?? 0);
            $abono = (float)($_POST['abono'] ?? 0);
            
            if (!$cliente_id) throw new Exception('Debe seleccionar un cliente');
            if (!$campana_id) {
                $campanaActiva = $this->campana->getActive();
                if ($campanaActiva) $campana_id = $campanaActiva['id'];
            }
            if (!$campana_id) throw new Exception('Debe seleccionar una campaña');
            
            $total_general = 0;
            $detalles = array();
            for ($i = 0; $i < count($productos); $i++) {
                if (empty($productos[$i])) continue;
                $producto_id = (int)$productos[$i];
                $cantidad = (float)$cantidades[$i];
                $precio = (float)$precios[$i];
                $tipo = $tipos[$i] ?? 'normal';
                if ($tipo == 'regalo') $precio = 0;
                $producto = $this->producto->find($producto_id);
                if (!$producto) throw new Exception("Producto no encontrado");
                if ($cantidad > $producto['stock']) throw new Exception("Stock insuficiente para {$producto['nombre']}");
                $total_general += $cantidad * $precio;
                $detalles[] = array('producto_id' => $producto_id, 'cantidad' => $cantidad, 'precio' => $precio, 'tipo' => $tipo);
            }
            if (empty($detalles)) throw new Exception('Debe agregar al menos un producto');
            $total_pagado = $pago_inicial + $abono;
            if ($total_pagado > $total_general) throw new Exception('El pago no puede ser mayor al total');
            $saldo = $total_general - $total_pagado;
            $venta_id = $this->venta->create(array(
                'cliente_id' => $cliente_id,
                'campana_id' => $campana_id,
                'usuario_id' => $_SESSION['usuario_id'],
                'total' => $total_general
            ));
            foreach ($detalles as $detalle) {
                $this->venta->addDetalle($venta_id, $detalle);
                $this->producto->updateStock($detalle['producto_id'], $detalle['cantidad'], 'restar');
            }
            $this->venta->addPago($venta_id, $pago_inicial, $abono, $saldo);
            $db->commit();
            $_SESSION['success'] = 'Venta registrada correctamente';
            redirect('ventas');
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['error'] = $e->getMessage();
            redirect('ventas/crear');
        }
    }
    
    public function ver($id) {
        requireLogin();
        $venta = $this->venta->getWithPago($id);
        if (!$venta) {
            $_SESSION['error'] = 'Venta no encontrada';
            redirect('ventas');
        }
        $detalles = $this->venta->getDetalles($id);
        $totalGanancia = 0;
        foreach ($detalles as $d) {
            $ganancia = ($d['precio_aplicado'] - $d['precio_compra']) * $d['cantidad'];
            $totalGanancia += $ganancia;
        }
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalle Venta</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        </head>
        <body>' . renderMenu() . '
        <div class="container">
            <h2>🧾 Detalle de Venta #' . $venta['id'] . '</h2>
            <div class="card">
                <p><strong>Cliente:</strong> ' . htmlspecialchars($venta['cliente']) . '</p>
                <p><strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($venta['fecha'])) . '</p>
                <p><strong>Total:</strong> $' . number_format($venta['total'], 2) . '</p>
                <p><strong>Pago Inicial:</strong> $' . number_format($venta['pago_inicial'] ?? 0, 2) . '</p>
                <p><strong>Abono:</strong> $' . number_format($venta['pago_final'] ?? 0, 2) . '</p>
                <p><strong>Saldo:</strong> $' . number_format($venta['saldo'] ?? $venta['total'], 2) . '</p>
            </div>
            <button onclick="window.print()">🖨 Imprimir</button>
            <a href="' . BASE_URL . 'ventas/agregar_producto/' . $venta['id'] . '" class="btn" style="background:#8b5cf6;">➕ Agregar producto</a>
            <br><br>
            <table>
                <thead><tr><th>Producto</th><th>Cantidad</th><th>Compra</th><th>Venta</th><th>Total</th><th>Ganancia</th><th>Tipo</th></tr></thead>
                <tbody>';
        foreach ($detalles as $d) {
            $total = $d['cantidad'] * $d['precio_aplicado'];
            $ganancia = ($d['precio_aplicado'] - $d['precio_compra']) * $d['cantidad'];
            $color = ($d['tipo_venta'] == 'promo') ? '#facc15' : (($d['tipo_venta'] == 'regalo') ? '#38bdf8' : '#22c55e');
            echo '<tr>
                    <td>' . htmlspecialchars($d['nombre']) . '</td>
                    <td>' . $d['cantidad'] . '</td>
                    <td>$' . number_format($d['precio_compra'], 2) . '</td>
                    <td>$' . number_format($d['precio_aplicado'], 2) . '</td>
                    <td>$' . number_format($total, 2) . '</td>
                    <td>$' . number_format($ganancia, 2) . '</td>
                    <td style="color:' . $color . ';">' . strtoupper($d['tipo_venta']) . '</td>
                </tr>';
        }
        echo '</tbody></table>
            <h3>💰 Ganancia Total: $' . number_format($totalGanancia, 2) . '</h3>
        </div></body></html>';
    }
    
    public function editar_campana($id) {
        requireLogin();
        $venta = $this->venta->find($id);
        if (!$venta) {
            $_SESSION['error'] = 'Venta no encontrada';
            redirect('ventas');
        }
        $campanas = $this->campana->all();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campana_id = (int)($_POST['campana'] ?? 0);
            $this->venta->updateCampana($id, $campana_id);
            $_SESSION['success'] = 'Campaña actualizada';
            redirect('ventas');
        }
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Campaña</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        </head>
        <body>' . renderMenu() . '
        <div class="container">
            <div class="card">
                <h2>🛠 Asignar Campaña a Venta #' . $id . '</h2>
                <form method="POST">
                    <select name="campana" required>
                        <option value="">Seleccione</option>';
        foreach ($campanas as $c) {
            echo '<option value="' . $c['id'] . '" ' . ($venta['campana_id'] == $c['id'] ? 'selected' : '') . '>' . htmlspecialchars($c['nombre']) . '</option>';
        }
        echo '</select>
                    <button type="submit">💾 Guardar</button>
                    <a href="' . BASE_URL . 'ventas">Cancelar</a>
                </form>
            </div>
        </div>
        </body></html>';
    }
    
    public function agregar_producto($id) {
        requireLogin();
        $venta = $this->venta->getWithPago($id);
        if (!$venta) {
            $_SESSION['error'] = 'Venta no encontrada';
            redirect('ventas');
        }
        $productosVenta = $this->venta->getProductosDeVenta($id);
        $productos = $this->producto->getByCampana(null, true);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto_id = (int)($_POST['producto_id'] ?? 0);
            $cantidad = (float)($_POST['cantidad'] ?? 0);
            $precio = (float)($_POST['precio'] ?? 0);
            $tipo_venta = $_POST['tipo_venta'] ?? 'normal';
            $errors = array();
            if ($producto_id <= 0) $errors[] = 'Debe seleccionar un producto';
            if ($cantidad <= 0) $errors[] = 'La cantidad debe ser mayor a 0';
            if ($precio <= 0 && $tipo_venta != 'regalo') $errors[] = 'El precio debe ser mayor a 0';
            $producto = $this->producto->find($producto_id);
            if ($producto && $cantidad > $producto['stock']) $errors[] = "Stock insuficiente para {$producto['nombre']}";
            if (empty($errors)) {
                try {
                    $this->venta->agregarProductoAVenta($id, $producto_id, $cantidad, $precio, $tipo_venta);
                    $this->producto->updateStock($producto_id, $cantidad, 'restar');
                    $_SESSION['success'] = '✅ Producto agregado correctamente';
                    redirect("ventas/ver/{$id}");
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Agregar Producto</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        </head>
        <body>' . renderMenu() . '
        <div class="container">
            <h2>➕ Agregar Producto a Venta #' . $id . '</h2>
            <div class="card">
                <p><strong>Cliente:</strong> ' . htmlspecialchars($venta['cliente']) . '</p>
                <p><strong>Total actual:</strong> $' . number_format($venta['total'], 2) . '</p>
                <p><strong>Saldo pendiente:</strong> $' . number_format($venta['saldo'], 2) . '</p>
            </div>
            <div class="card">
                <h3>📦 Productos actuales</h3>';
        if (empty($productosVenta)) {
            echo '<p>No hay productos registrados en esta venta.</p>';
        } else {
            echo '<table>
                <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead>
                <tbody>';
            foreach ($productosVenta as $pv) {
                echo '<tr>
                        <td>' . htmlspecialchars($pv['nombre']) . '</td>
                        <td>' . $pv['cantidad'] . '</td>
                        <td>$' . number_format($pv['precio_aplicado'], 2) . '</td>
                        <td>$' . number_format($pv['cantidad'] * $pv['precio_aplicado'], 2) . '</td>
                    </tr>';
            }
            echo '</tbody></table>';
        }
        echo '</div>
            <div class="card">
                <h3>➕ Agregar nuevo producto</h3>
                <form method="POST">
                    <label>Producto</label>
                    <select name="producto_id" required>
                        <option value="">Seleccione</option>';
        foreach ($productos as $p) {
            echo '<option value="' . $p['id'] . '">' . htmlspecialchars($p['nombre']) . ' (Stock: ' . $p['stock'] . ') - $' . number_format($p['precio_normal'], 2) . '</option>';
        }
        echo '</select>
                    <label>Cantidad</label>
                    <input type="number" name="cantidad" min="1" required>
                    <label>Precio unitario</label>
                    <input type="number" step="0.01" name="precio" required>
                    <label>Tipo</label>
                    <select name="tipo_venta">
                        <option value="normal">Normal</option>
                        <option value="promo">Promo</option>
                        <option value="regalo">Regalo</option>
                    </select>
                    <button type="submit">➕ Agregar</button>
                    <a href="' . BASE_URL . 'ventas/ver/' . $id . '">Cancelar</a>
                </form>
            </div>
        </div>
        </body></html>';
    }
    
    public function plantilla() {
        requireLogin();
        $campana_id = $_GET['campana'] ?? null;
        
        // Si el parámetro es "todas" o está vacío, mostrar todas
        if ($campana_id === 'todas' || $campana_id === null || $campana_id === '') {
            $campana = array('nombre' => 'TODAS LAS CAMPAÑAS', 'fecha_inicio' => '1900-01-01', 'fecha_fin' => date('Y-m-d'));
            $detalles = $this->venta->getDetallesByCampana(null);
            $pagos = $this->venta->getPagosResumenByCampana(null);
        } else {
            // Mostrar solo la campaña específica
            $campana = $this->campana->find($campana_id);
            if (!$campana) {
                $_SESSION['error'] = 'Campaña no encontrada';
                redirect('ventas');
            }
            $detalles = $this->venta->getDetallesByCampana($campana_id);
            $pagos = $this->venta->getPagosResumenByCampana($campana_id);
        }
        
        $totalGeneral = 0;
        $totalSaldo = 0;
        foreach ($pagos as $p) {
            $totalGeneral += $p['total'];
            $totalSaldo += $p['saldo'];
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reporte - ' . htmlspecialchars($campana['nombre']) . '</title>
            <style>
                body{font-family:"Courier New",monospace;font-size:11px;padding:20px;background:white;color:black;}
                h1,h2{text-align:center;}
                table{width:100%;border-collapse:collapse;margin-bottom:20px;}
                th,td{border:1px solid #000;padding:4px;text-align:center;}
                th{background:#eaeaea;}
                .firma{display:flex;justify-content:space-between;margin-top:30px;}
                .totales{text-align:right;}
                @media print{button{display:none;}}
            </style>
        </head>
        <body>
            <div style="text-align:center;margin-bottom:10px;">
                <button onclick="window.print()">🖨️ Imprimir</button>
                <button onclick="history.back()">⬅ Volver</button>
            </div>
            <h1>🛍️ ALEJOJO</h1>
            <h2>Control de Ventas</h2>
            <div>
                <p>Campaña: <span style="border-bottom:1px solid #000;display:inline-block;width:200px;">' . htmlspecialchars($campana['nombre']) . '</span></p>
                <p>Fecha: <span style="border-bottom:1px solid #000;display:inline-block;width:200px;">' . date('d/m/Y') . '</span></p>
                <p>Período: ' . date('d/m/Y', strtotime($campana['fecha_inicio'])) . ' al ' . date('d/m/Y', strtotime($campana['fecha_fin'])) . '</p>
            </div>
            <h3>📦 Detalle de Productos Vendidos</h3>
            <table>
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th width="80">Código</th>
                        <th>Producto</th>
                        <th width="40">Cant</th>
                        <th width="70">P. Venta</th>
                        <th width="70">Total</th>
                        <th>Cliente</th>
                    </tr>
                </thead>
                <tbody>';
        $i = 1;
        foreach ($detalles as $d) {
            echo '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . htmlspecialchars($d['codigo']) . '</td>
                    <td>' . htmlspecialchars($d['producto']) . '</td>
                    <td>' . $d['cantidad'] . '</td>
                    <td>$' . number_format($d['precio_aplicado'], 2) . '</td>
                    <td>$' . number_format($d['cantidad'] * $d['precio_aplicado'], 2) . '</td>
                    <td>' . htmlspecialchars($d['cliente']) . '</td>
                </tr>';
        }
        for (; $i <= 25; $i++) {
            echo '<tr><td colspan="7"> </td></tr>';
        }
        echo '</tbody>
            </table>
            <h3>💳 Control de Pagos por Cliente</h3>
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Total Compra</th>
                        <th>Pago Inicial</th>
                        <th>Abonos</th>
                        <th>Saldo Pendiente</th>
                        <th width="30">✔</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($pagos as $p) {
            if ($p['total'] > 0) {
                echo '<tr>
                        <td>' . htmlspecialchars($p['nombre']) . '</td>
                        <td>$' . number_format($p['total'], 2) . '</td>
                        <td>$' . number_format($p['inicial'], 2) . '</td>
                        <td>$' . number_format($p['abono'], 2) . '</td>
                        <td>$' . number_format($p['saldo'], 2) . '</td>
                        <td>☐</td>
                    </tr>';
            }
        }
        for ($i = count($pagos); $i < 15; $i++) {
            echo '<tr><td colspan="6"> </td></tr>';
        }
        echo '</tbody>
            </table>
            <div class="totales">
                <p><strong>Total General de Ventas:</strong> $' . number_format($totalGeneral, 2) . '</p>
                <p><strong>Saldo Total Pendiente:</strong> $' . number_format($totalSaldo, 2) . '</p>
            </div>
            <div class="firma">
                <div>____________________<br>Firma Cliente</div>
                <div>____________________<br>Firma Vendedor</div>
                <div>____________________<br>Sello</div>
            </div>
        </body>
        </html>';
    }
}
?>