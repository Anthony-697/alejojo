<?php
require_once __DIR__ . '/../models/Promocion.php';
require_once __DIR__ . '/../models/Producto.php';

class PromocionController {
    private $promocion;
    private $producto;
    
    public function __construct() {
        $this->promocion = new Promocion();
        $this->producto = new Producto();
    }
    
    public function index() {
        requireLogin();
        
        if (!hasRole('admin')) {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $promociones = $this->promocion->getAllWithDetails();
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Promociones</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:#0f172a;color:#f1f5f9;}
                .navbar{background:linear-gradient(135deg,#1e293b,#0f172a);padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:8px;transition:all 0.3s;}
                .navbar a:hover{background:#334155;transform:translateY(-2px);}
                .logo{color:#22c55e;font-weight:bold;margin-right:auto;}
                .container{max-width:1200px;margin:0 auto;padding:2rem;}
                table{width:100%;border-collapse:collapse;background:#1e293b;border-radius:16px;overflow:hidden;}
                th,td{padding:12px;text-align:center;border-bottom:1px solid #334155;}
                th{background:#334155;}
                .btn{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border-radius:10px;text-decoration:none;color:white;display:inline-block;margin:1rem 0;}
                .btn-editar{background:#3b82f6;padding:6px 12px;border-radius:8px;text-decoration:none;color:white;}
                .btn-eliminar{background:#ef4444;padding:6px 12px;border-radius:8px;text-decoration:none;color:white;}
                .activo{color:#22c55e;font-weight:bold;}
                .inactivo{color:#ef4444;font-weight:bold;}
            </style>
        </head>
        <body>
            ' . renderMenu() . '
            <div class="container">
                <h2>🎯 Promociones</h2>
                <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                <a href="' . BASE_URL . 'promociones/crear" class="btn">➕ Nueva Promoción</a>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Rangos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        if (!empty($promociones)) {
            foreach ($promociones as $p) {
                echo '<tr>
                        <td>' . $p['id'] . '</td>
                        <td>' . htmlspecialchars($p['nombre']) . '</td>
                        <td>' . htmlspecialchars($p['descripcion']) . '</td>
                        <td style="text-align:left;">';
                if (!empty($p['detalles'])) {
                    foreach ($p['detalles'] as $d) {
                        echo '<div>📦 ' . htmlspecialchars($d['producto_nombre']) . ': ' . $d['cantidad_min'];
                        if ($d['cantidad_max']) echo ' a ' . $d['cantidad_max'];
                        echo ' → $' . number_format($d['precio_promo'], 2) . '</div>';
                    }
                } else {
                    echo 'Sin rangos';
                }
                echo '</td>
                        <td class="' . ($p['activo'] ? 'activo' : 'inactivo') . '">' . ($p['activo'] ? 'Activa' : 'Inactiva') . '</td>
<td class="acciones">
    <a href="' . BASE_URL . 'promociones/editar/' . $p['id'] . '" class="btn-editar">✏ Editar</a>
    <a href="' . BASE_URL . 'promociones/eliminar/' . $p['id'] . '" class="btn-eliminar" onclick="return confirm(\'¿Eliminar esta promoción?\')">🗑 Eliminar</a>
</td>
                    </tr>';
            }
        } else {
            echo '<tr><td colspan="6">No hay promociones registradas</td></tr>';
        }
        
        echo '</tbody>
                </table>
                </div>
            </div>
        </body>
        </html>';
    }
    
    public function crear() {
        requireLogin();
        
        if (!hasRole('admin')) {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $productos = $this->producto->getByCampana();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            $promocion_id = $this->promocion->crear([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'tipo' => 'cantidad',
                'activo' => $activo,
                'fecha_inicio' => null,
                'fecha_fin' => null
            ]);
            
            $productos_ids = $_POST['producto_id'] ?? [];
            $cantidades_min = $_POST['cantidad_min'] ?? [];
            $cantidades_max = $_POST['cantidad_max'] ?? [];
            $precios_promo = $_POST['precio_promo'] ?? [];
            
            for ($i = 0; $i < count($productos_ids); $i++) {
                if (empty($productos_ids[$i])) continue;
                $this->promocion->agregarDetalle($promocion_id, [
                    'producto_id' => $productos_ids[$i],
                    'cantidad_min' => (int)$cantidades_min[$i],
                    'cantidad_max' => !empty($cantidades_max[$i]) ? (int)$cantidades_max[$i] : null,
                    'precio_promo' => (float)$precios_promo[$i]
                ]);
            }
            
            $_SESSION['success'] = '✅ Promoción creada correctamente';
            redirect('promociones');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nueva Promoción</title>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:"Segoe UI",sans-serif;background:linear-gradient(135deg,#0f172a,#1e293b);color:#f1f5f9;min-height:100vh;}
                .navbar{background:rgba(30,41,59,0.95);backdrop-filter:blur(10px);padding:1rem 2rem;display:flex;gap:0.5rem;flex-wrap:wrap;box-shadow:0 4px 6px rgba(0,0,0,0.1);position:sticky;top:0;z-index:1000;}
                .navbar a{color:#e2e8f0;text-decoration:none;padding:0.5rem 1rem;border-radius:12px;transition:all 0.3s;}
                .navbar a:hover{background:#334155;transform:translateY(-2px);}
                .logo{color:#22c55e;font-weight:bold;font-size:1.2rem;margin-right:auto;}
                .container{max-width:700px;margin:0 auto;padding:2rem;}
                .card{background:rgba(30,41,59,0.8);backdrop-filter:blur(5px);padding:2rem;border-radius:24px;box-shadow:0 8px 32px rgba(0,0,0,0.2);border:1px solid rgba(34,197,94,0.2);}
                h2{color:#22c55e;margin-bottom:1rem;}
                input,select,textarea{width:100%;padding:12px;margin:10px 0;border-radius:12px;border:1px solid #334155;background:#0f172a;color:white;transition:all 0.3s;}
                input:focus,select:focus,textarea:focus{outline:none;border-color:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,0.1);}
                button{background:linear-gradient(135deg,#22c55e,#16a34a);padding:12px 24px;border:none;border-radius:12px;color:white;cursor:pointer;font-weight:bold;transition:all 0.3s;}
                button:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(34,197,94,0.3);}
                .btn{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border-radius:12px;text-decoration:none;color:white;display:inline-block;}
                .btn-eliminar{background:#ef4444;padding:6px 12px;border:none;border-radius:8px;color:white;cursor:pointer;}
                .btn-eliminar:hover{background:#dc2626;transform:scale(1.05);}
                .rango-fila{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;align-items:center;}
                .rango-fila select{flex:2;}
                .rango-fila input{flex:1;}
                hr{margin:15px 0;border-color:#334155;}
                a{color:#22c55e;text-decoration:none;}
                a:hover{text-decoration:underline;}
            </style>
        </head>
        <body>
            ' . renderMenu() . '
            <div class="container">
                <div class="card">
                    <h2>➕ Nueva Promoción por Cantidad</h2>
                    <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
                    <p style="margin-bottom:15px; color:#94a3b8;">Ejemplo: 1 producto = $8.99, 2 productos = $12.99, 3 productos = $15.99</p>
                    <form method="POST">
                        <label>Nombre de la Promoción *</label>
                        <input type="text" name="nombre" required autofocus>
                        
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="2" placeholder="Ej: Oferta especial por cantidad"></textarea>
                        
                        <label style="display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" name="activo" checked style="width:auto;"> Promoción Activa
                        </label>
                        
                        <hr>
                        
                        <h3>📦 Rangos de Promoción</h3>
                        
                        <div id="rangos-container">
                            <div class="rango-fila">
                                <select name="producto_id[]" required>
                                    <option value="">Seleccione producto</option>';
        foreach ($productos as $p) {
            echo '<option value="' . $p['id'] . '">' . htmlspecialchars($p['nombre']) . ' - $' . number_format($p['precio_normal'], 2) . '</option>';
        }
        echo '</select>
                                <input type="number" name="cantidad_min[]" placeholder="Cantidad min" required>
                                <input type="number" name="cantidad_max[]" placeholder="Cantidad max (opcional)">
                                <input type="number" step="0.01" name="precio_promo[]" placeholder="Precio promoción" required>
                                <button type="button" class="btn-eliminar" onclick="eliminarRango(this)">✖</button>
                            </div>
                        </div>
                        <button type="button" onclick="agregarRango()" class="btn" style="margin-top:10px;">➕ Agregar rango</button>
                        
                        <hr>
                        
                        <button type="submit">💾 Guardar Promoción</button>
                        <a href="' . BASE_URL . 'promociones" style="margin-left:10px;">Cancelar</a>
                    </form>
                </div>
            </div>
            <script>
                function agregarRango() {
                    var container = document.getElementById("rangos-container");
                    var template = container.querySelector(".rango-fila");
                    var nuevo = template.cloneNode(true);
                    nuevo.querySelectorAll("input, select").forEach(function(el) { el.value = ""; });
                    container.appendChild(nuevo);
                }
                function eliminarRango(btn) {
                    var container = document.getElementById("rangos-container");
                    if(container.querySelectorAll(".rango-fila").length > 1) {
                        btn.closest(".rango-fila").remove();
                    } else {
                        alert("Debe haber al menos un rango de promoción");
                    }
                }
            </script>
        </body>
        </html>';
    }
    
    public function editar($id) {
        requireLogin();
        
        if (!hasRole('admin')) {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $promocion = $this->promocion->find($id);
        if (!$promocion) {
            $_SESSION['error'] = 'Promoción no encontrada';
            redirect('promociones');
        }
        
        $detalles = $this->promocion->getDetalles($id);
        $productos = $this->producto->getByCampana();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->promocion->actualizar($id, [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion']),
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'fecha_inicio' => null,
                'fecha_fin' => null
            ]);
            
            $this->promocion->eliminarDetalles($id);
            
            $productos_ids = $_POST['producto_id'] ?? [];
            $cantidades_min = $_POST['cantidad_min'] ?? [];
            $cantidades_max = $_POST['cantidad_max'] ?? [];
            $precios_promo = $_POST['precio_promo'] ?? [];
            
            for ($i = 0; $i < count($productos_ids); $i++) {
                if (empty($productos_ids[$i])) continue;
                $this->promocion->agregarDetalle($id, [
                    'producto_id' => $productos_ids[$i],
                    'cantidad_min' => (int)$cantidades_min[$i],
                    'cantidad_max' => !empty($cantidades_max[$i]) ? (int)$cantidades_max[$i] : null,
                    'precio_promo' => (float)$precios_promo[$i]
                ]);
            }
            
            $_SESSION['success'] = '✅ Promoción actualizada correctamente';
            redirect('promociones');
        }
        
        echo '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Promoción</title>
        <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
        <style>
            body{background:#0f172a;color:#f1f5f9;font-family:sans-serif;}
            .card{background:#1e293b;padding:2rem;border-radius:16px;max-width:700px;margin:2rem auto;}
            input,select,textarea{width:100%;padding:10px;margin:10px 0;border-radius:10px;border:none;background:#0f172a;color:white;}
            button{background:linear-gradient(135deg,#22c55e,#16a34a);padding:10px 20px;border:none;border-radius:10px;color:white;cursor:pointer;}
            a{color:#22c55e;text-decoration:none;}
            .rango-fila{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;align-items:center;}
            .rango-fila select{flex:2;}
            .rango-fila input{flex:1;}
            .btn-eliminar{background:#ef4444;padding:6px 12px;border:none;border-radius:8px;color:white;cursor:pointer;}
        </style>
        </head>
        <body>
        <div class="card">
            <h2>✏ Editar Promoción: ' . htmlspecialchars($promocion['nombre']) . '</h2>
            <link rel="stylesheet" href="/sistema_ventas_v2/assets/css/style.css">
            <form method="POST">
                <label>Nombre de la Promoción *</label>
                <input type="text" name="nombre" value="' . htmlspecialchars($promocion['nombre']) . '" required>
                
                <label>Descripción</label>
                <textarea name="descripcion" rows="2">' . htmlspecialchars($promocion['descripcion']) . '</textarea>
                
                <label style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" name="activo" ' . ($promocion['activo'] ? 'checked' : '') . ' style="width:auto;"> Promoción Activa
                </label>
                
                <hr style="margin:15px 0; border-color:#334155;">
                
                <h3>📦 Rangos de Promoción</h3>
                <div id="rangos-container">';
        
        foreach ($detalles as $d) {
            echo '<div class="rango-fila">
                        <select name="producto_id[]" required>
                            <option value="">Seleccione producto</option>';
            foreach ($productos as $p) {
                echo '<option value="' . $p['id'] . '" ' . ($d['producto_id'] == $p['id'] ? 'selected' : '') . '>' . htmlspecialchars($p['nombre']) . ' - $' . number_format($p['precio_normal'], 2) . '</option>';
            }
            echo '</select>
                        <input type="number" name="cantidad_min[]" value="' . $d['cantidad_min'] . '" placeholder="Cantidad min" required>
                        <input type="number" name="cantidad_max[]" value="' . $d['cantidad_max'] . '" placeholder="Cantidad max (opcional)">
                        <input type="number" step="0.01" name="precio_promo[]" value="' . $d['precio_promo'] . '" placeholder="Precio promoción" required>
                        <button type="button" class="btn-eliminar" onclick="eliminarRango(this)">✖</button>
                    </div>';
        }
        
        echo '</div>
                <button type="button" onclick="agregarRango()" class="btn" style="margin-top:10px;">➕ Agregar rango</button>
                
                <hr style="margin:15px 0; border-color:#334155;">
                
                <button type="submit">💾 Actualizar Promoción</button>
                <a href="' . BASE_URL . 'promociones" style="margin-left:10px;">Cancelar</a>
            </form>
        </div>
        <script>
            function agregarRango() {
                var container = document.getElementById("rangos-container");
                var template = container.querySelector(".rango-fila");
                var nuevo = template.cloneNode(true);
                nuevo.querySelectorAll("input, select").forEach(function(el) { el.value = ""; });
                container.appendChild(nuevo);
            }
            function eliminarRango(btn) {
                var container = document.getElementById("rangos-container");
                if(container.querySelectorAll(".rango-fila").length > 1) {
                    btn.closest(".rango-fila").remove();
                } else {
                    alert("Debe haber al menos un rango de promoción");
                }
            }
        </script>
        </body></html>';
    }
    
    public function eliminar($id) {
        requireLogin();
        
        if (!hasRole('admin')) {
            $_SESSION['error'] = 'No tienes permiso';
            redirect('dashboard');
        }
        
        $this->promocion->delete($id);
        $_SESSION['success'] = '✅ Promoción eliminada correctamente';
        redirect('promociones');
    }
    
    public function calcular() {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        
        $producto_id = (int)($_GET['producto_id'] ?? 0);
        $cantidad = (int)($_GET['cantidad'] ?? 0);
        
        if (!$producto_id || !$cantidad) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }
        
        $producto = $this->producto->find($producto_id);
        if (!$producto) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Producto no encontrado']);
            exit;
        }
        
        $promo = $this->promocion->calcularPrecioPromo($producto_id, $cantidad);
        
        if ($promo) {
            header('Content-Type: application/json');
            echo json_encode([
                'tiene_promo' => true,
                'precio_normal' => $producto['precio_normal'],
                'precio_normal_total' => $cantidad * $producto['precio_normal'],
                'precio_promo_total' => $promo['precio_promo'],
                'ahorro' => ($cantidad * $producto['precio_normal']) - $promo['precio_promo'],
                'promocion_nombre' => $promo['promo_nombre']
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'tiene_promo' => false,
                'precio_normal' => $producto['precio_normal'],
                'precio_normal_total' => $cantidad * $producto['precio_normal']
            ]);
        }
    }
    
    public function productos() {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        
        $productos = $this->promocion->getProductosConPromo();
        
        foreach ($productos as &$producto) {
            $sql = "SELECT cantidad_min, cantidad_max, precio_promo
                    FROM promocion_detalles pd
                    JOIN promociones p ON pd.promocion_id = p.id
                    WHERE pd.producto_id = ?
                    AND p.activo = 1
                    ORDER BY cantidad_min";
            $db = Database::getInstance();
            $producto['rangos'] = $db->fetchAll($sql, [$producto['id']]);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'productos' => $productos]);
    }
}
?>