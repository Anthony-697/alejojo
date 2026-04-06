<?php
require_once __DIR__ . '/Model.php';

class Venta extends Model {
    protected $table = 'ventas';
    
    public function getAllWithDetails($campana_id = '') {
        $sql = "SELECT v.id, v.fecha, c.nombre as cliente, v.total, 
                       IFNULL(p.saldo, v.total) as saldo,
                       ca.nombre as campana, ca.id as campana_id
                FROM ventas v
                JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN pagos p ON v.id = p.venta_id
                LEFT JOIN campanas ca ON v.campana_id = ca.id";
        
        $params = array();
        
        if ($campana_id != '') {
            $sql .= " WHERE v.campana_id = ?";
            $params[] = $campana_id;
        }
        
        $sql .= " ORDER BY v.id DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getWithPago($id) {
        $sql = "SELECT v.*, c.nombre AS cliente, 
                       p.pago_inicial, p.pago_final, p.saldo
                FROM ventas v
                JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN pagos p ON v.id = p.venta_id
                WHERE v.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getDetalles($venta_id) {
        $sql = "SELECT d.*, 
                       p.nombre, 
                       p.codigo,
                       p.precio_compra, 
                       c.nombre as campana_origen
                FROM detalle_venta d
                JOIN productos p ON d.producto_id = p.id
                LEFT JOIN campanas c ON p.campana_id = c.id
                WHERE d.venta_id = ?
                ORDER BY d.id";
        
        return $this->db->fetchAll($sql, [$venta_id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO ventas (cliente_id, campana_id, usuario_id, fecha, total)
                VALUES (?, ?, ?, NOW(), ?)";
        
        return $this->db->insert($sql, [
            $data['cliente_id'],
            $data['campana_id'],
            $data['usuario_id'],
            $data['total']
        ]);
    }
    
    public function addDetalle($venta_id, $detalle) {
        $sql = "INSERT INTO detalle_venta
                (venta_id, producto_id, cantidad, precio_aplicado, tipo_venta)
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $venta_id,
            $detalle['producto_id'],
            $detalle['cantidad'],
            $detalle['precio'],
            $detalle['tipo']
        ]);
    }
    
    public function addPago($venta_id, $pago_inicial, $pago_final, $saldo) {
        $sql = "INSERT INTO pagos (venta_id, pago_inicial, pago_final, saldo)
                VALUES (?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $venta_id,
            $pago_inicial,
            $pago_final,
            $saldo
        ]);
    }
    
    public function updateCampana($id, $campana_id) {
        $sql = "UPDATE ventas SET campana_id = ? WHERE id = ?";
        return $this->db->query($sql, [$campana_id, $id]);
    }
    
    public function getDetallesByCampana($campana_id = null) {
        $sql = "SELECT d.cantidad, d.precio_aplicado,
                       p.nombre AS producto, p.codigo,
                       c.nombre AS cliente
                FROM detalle_venta d
                JOIN productos p ON d.producto_id = p.id
                JOIN ventas v ON d.venta_id = v.id
                JOIN clientes c ON v.cliente_id = c.id";
        
        $params = array();
        
        if ($campana_id && $campana_id !== 'todas') {
            $sql .= " WHERE v.campana_id = ?";
            $params[] = $campana_id;
        }
        
        $sql .= " ORDER BY c.nombre, p.nombre";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getPagosResumenByCampana($campana_id = null) {
        $sql = "SELECT 
                    c.nombre,
                    SUM(d2.cantidad * d2.precio_aplicado) as total,
                    SUM(IFNULL(p.pago_inicial, 0)) as inicial,
                    SUM(IFNULL(p.pago_final, 0)) as abono,
                    SUM(IFNULL(p.saldo, 0)) as saldo
                FROM ventas v
                JOIN clientes c ON v.cliente_id = c.id
                JOIN detalle_venta d2 ON v.id = d2.venta_id
                LEFT JOIN pagos p ON v.id = p.venta_id";
        
        $params = array();
        
        if ($campana_id && $campana_id !== 'todas') {
            $sql .= " WHERE v.campana_id = ?";
            $params[] = $campana_id;
        }
        
        $sql .= " GROUP BY c.id, c.nombre
                  ORDER BY c.nombre";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function agregarProductoAVenta($venta_id, $producto_id, $cantidad, $precio, $tipo_venta = 'normal') {
        $this->db->beginTransaction();
        
        try {
            $sql = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_aplicado, tipo_venta)
                    VALUES (?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [$venta_id, $producto_id, $cantidad, $precio, $tipo_venta]);
            
            $nuevo_total = $this->calcularTotalVenta($venta_id);
            
            $sql = "UPDATE ventas SET total = ? WHERE id = ?";
            $this->db->query($sql, [$nuevo_total, $venta_id]);
            
            $pago = $this->db->fetchOne("SELECT * FROM pagos WHERE venta_id = ?", [$venta_id]);
            
            if ($pago) {
                $nuevo_saldo = $pago['saldo'] + ($cantidad * $precio);
                $sql = "UPDATE pagos SET saldo = ? WHERE venta_id = ?";
                $this->db->query($sql, [$nuevo_saldo, $venta_id]);
            } else {
                $sql = "INSERT INTO pagos (venta_id, pago_inicial, pago_final, saldo)
                        VALUES (?, 0, 0, ?)";
                $this->db->query($sql, [$venta_id, $nuevo_total]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function calcularTotalVenta($venta_id) {
        $sql = "SELECT SUM(cantidad * precio_aplicado) as total FROM detalle_venta WHERE venta_id = ?";
        $result = $this->db->fetchOne($sql, [$venta_id]);
        return $result['total'] ?? 0;
    }
    
    public function getProductosDeVenta($venta_id) {
        $sql = "SELECT d.producto_id, p.nombre, d.cantidad, d.precio_aplicado, d.tipo_venta
                FROM detalle_venta d
                JOIN productos p ON d.producto_id = p.id
                WHERE d.venta_id = ?
                ORDER BY d.id";
        return $this->db->fetchAll($sql, [$venta_id]);
    }
}
?>