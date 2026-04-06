<?php
/**
 * Modelo de Entrada (Compras)
 * Maneja el registro de compras de productos a proveedores
 */

require_once __DIR__ . '/Model.php';

class Entrada extends Model {
    protected $table = 'entradas';
    
    /**
     * Registra una nueva entrada de productos
     * 
     * @param int $producto_id ID del producto
     * @param int $campana_id ID de la campaña
     * @param int $cantidad Cantidad comprada
     * @param float $costo Costo unitario
     * @return int ID de la entrada creada
     */
    public function registrar($producto_id, $campana_id, $cantidad, $costo) {
        $sql = "INSERT INTO entradas 
                (producto_id, campana_id, cantidad, costo_unitario, tipo, fecha)
                VALUES (?, ?, ?, ?, 'normal', NOW())";
        
        return $this->db->insert($sql, [
            $producto_id,
            $campana_id,
            $cantidad,
            $costo
        ]);
    }
    
    /**
     * Obtiene el historial de entradas por producto
     * 
     * @param int $producto_id ID del producto
     * @return array Historial de compras
     */
    public function getByProducto($producto_id) {
        $sql = "SELECT e.*, c.nombre as campana_nombre
                FROM entradas e
                LEFT JOIN campanas c ON e.campana_id = c.id
                WHERE e.producto_id = ?
                ORDER BY e.fecha DESC";
        return $this->db->fetchAll($sql, [$producto_id]);
    }
    
    /**
     * Obtiene todas las entradas con filtro opcional
     * 
     * @param string $fecha_inicio Fecha inicio (opcional)
     * @param string $fecha_fin Fecha fin (opcional)
     * @return array Lista de entradas
     */
    public function getAll($fecha_inicio = null, $fecha_fin = null) {
        $sql = "SELECT e.*, p.nombre as producto_nombre, p.codigo, c.nombre as campana_nombre
                FROM entradas e
                JOIN productos p ON e.producto_id = p.id
                LEFT JOIN campanas c ON e.campana_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if ($fecha_inicio) {
            $sql .= " AND DATE(e.fecha) >= ?";
            $params[] = $fecha_inicio;
        }
        
        if ($fecha_fin) {
            $sql .= " AND DATE(e.fecha) <= ?";
            $params[] = $fecha_fin;
        }
        
        $sql .= " ORDER BY e.fecha DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
}