<?php
require_once __DIR__ . '/Model.php';

class Promocion extends Model {
    protected $table = 'promociones';
    
    public function getActivas() {
        $hoy = date('Y-m-d');
        $sql = "SELECT * FROM promociones 
                WHERE activo = 1 
                AND (fecha_inicio <= ? OR fecha_inicio IS NULL)
                AND (fecha_fin >= ? OR fecha_fin IS NULL)
                ORDER BY id DESC";
        return $this->db->fetchAll($sql, [$hoy, $hoy]);
    }
    
    public function getDetalles($promocion_id) {
        $sql = "SELECT pd.*, p.nombre as producto_nombre, p.codigo, p.precio_normal
                FROM promocion_detalles pd
                JOIN productos p ON pd.producto_id = p.id
                WHERE pd.promocion_id = ?
                ORDER BY pd.producto_id, pd.cantidad_min";
        return $this->db->fetchAll($sql, [$promocion_id]);
    }
    
    public function getAllWithDetails() {
        $promociones = $this->getActivas();
        foreach ($promociones as &$promo) {
            $promo['detalles'] = $this->getDetalles($promo['id']);
        }
        return $promociones;
    }
    
    public function calcularPrecioPromo($producto_id, $cantidad) {
        $hoy = date('Y-m-d');
        $sql = "SELECT pd.*, p.nombre as promo_nombre
                FROM promocion_detalles pd
                JOIN promociones p ON pd.promocion_id = p.id
                WHERE pd.producto_id = ?
                AND p.activo = 1
                AND (p.fecha_inicio <= ? OR p.fecha_inicio IS NULL)
                AND (p.fecha_fin >= ? OR p.fecha_fin IS NULL)
                ORDER BY pd.cantidad_min DESC";
        $promos = $this->db->fetchAll($sql, [$producto_id, $hoy, $hoy]);
        
        foreach ($promos as $promo) {
            if ($cantidad >= $promo['cantidad_min']) {
                if ($promo['cantidad_max'] === null || $cantidad <= $promo['cantidad_max']) {
                    return $promo;
                }
            }
        }
        return null;
    }
    
    public function getProductosConPromo() {
        $hoy = date('Y-m-d');
        $sql = "SELECT DISTINCT p.id, p.nombre, p.codigo, p.precio_normal
                FROM productos p
                JOIN promocion_detalles pd ON p.id = pd.producto_id
                JOIN promociones pro ON pd.promocion_id = pro.id
                WHERE pro.activo = 1
                AND (pro.fecha_inicio <= ? OR pro.fecha_inicio IS NULL)
                AND (pro.fecha_fin >= ? OR pro.fecha_fin IS NULL)
                AND p.estado = 1
                ORDER BY p.nombre";
        return $this->db->fetchAll($sql, [$hoy, $hoy]);
    }
    
    public function crear($data) {
        $sql = "INSERT INTO promociones (nombre, descripcion, tipo, activo, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['tipo'],
            $data['activo'] ?? 1,
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null
        ]);
    }
    
    public function agregarDetalle($promocion_id, $detalle) {
        $sql = "INSERT INTO promocion_detalles 
                (promocion_id, producto_id, cantidad_min, cantidad_max, precio_promo)
                VALUES (?, ?, ?, ?, ?)";
        return $this->db->query($sql, [
            $promocion_id,
            $detalle['producto_id'],
            $detalle['cantidad_min'],
            $detalle['cantidad_max'] ?? null,
            $detalle['precio_promo']
        ]);
    }
    
    public function eliminarDetalles($promocion_id) {
        return $this->db->query("DELETE FROM promocion_detalles WHERE promocion_id = ?", [$promocion_id]);
    }
    
    public function actualizar($id, $data) {
        $sql = "UPDATE promociones 
                SET nombre = ?, 
                    descripcion = ?, 
                    activo = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?
                WHERE id = ?";
        return $this->db->query($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['activo'] ?? 1,
            $data['fecha_inicio'] ?? null,
            $data['fecha_fin'] ?? null,
            $id
        ]);
    }
}
?>