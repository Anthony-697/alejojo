<?php
require_once __DIR__ . '/Model.php';

class Producto extends Model {
    protected $table = 'productos';
    
    /**
     * Obtiene productos por campaña
     */
    public function getByCampana($campanaId = null, $onlyStock = false) {
        $sql = "SELECT p.*, c.nombre as campana_nombre 
                FROM productos p
                LEFT JOIN campanas c ON p.campana_id = c.id
                WHERE p.estado = 1";
        
        $params = [];
        
        if ($campanaId) {
            $sql .= " AND p.campana_id = ?";
            $params[] = $campanaId;
        }
        
        if ($onlyStock) {
            $sql .= " AND p.stock > 0";
        }
        
        $sql .= " ORDER BY p.nombre";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Crea un nuevo producto
     */
    public function create($data) {
        $sql = "INSERT INTO productos (codigo, nombre, precio_compra, precio_normal, stock, campana_id, estado)
                VALUES (?, ?, ?, ?, 0, ?, 1)";
        
        return $this->db->insert($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['precio_compra'],
            $data['precio_venta'],
            $data['campana_id']
        ]);
    }
    
    /**
     * Actualiza un producto existente
     */
    public function update($id, $data) {
        $sql = "UPDATE productos SET 
                codigo = ?,
                nombre = ?,
                precio_compra = ?,
                precio_normal = ?,
                stock = ?
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['precio_compra'],
            $data['precio_venta'],
            $data['stock'],
            $id
        ]);
    }
    
    /**
     * Eliminación lógica (soft delete)
     */
    public function softDelete($id) {
        $sql = "UPDATE productos SET estado = 0 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    /**
     * Actualiza el stock de un producto (suma o resta)
     */
    public function updateStock($id, $cantidad, $operacion = 'restar') {
        $signo = $operacion === 'restar' ? '-' : '+';
        $sql = "UPDATE productos SET stock = stock {$signo} ? WHERE id = ?";
        return $this->db->query($sql, [$cantidad, $id]);
    }
    
    /**
     * Importa productos desde CSV
     */
    public function importFromCSV($filePath) {
        $importados = 0;
        $errores = 0;
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            fgetcsv($handle);
            
            $this->db->beginTransaction();
            
            try {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($data) < 6 || empty($data[0])) {
                        $errores++;
                        continue;
                    }
                    
                    $codigo = trim($data[0]);
                    $nombre = trim($data[1]);
                    $compra = (float)$data[2];
                    $venta = (float)$data[3];
                    $stock = (int)$data[4];
                    $campana = (int)trim($data[5]);
                    
                    $sql = "INSERT INTO productos 
                            (codigo, nombre, precio_compra, precio_normal, stock, campana_id, estado)
                            VALUES (?, ?, ?, ?, ?, ?, 1)
                            ON DUPLICATE KEY UPDATE
                            stock = stock + VALUES(stock)";
                    
                    $this->db->query($sql, [$codigo, $nombre, $compra, $venta, $stock, $campana]);
                    $importados++;
                }
                
                $this->db->commit();
                fclose($handle);
                
                return ['success' => true, 'importados' => $importados, 'errores' => $errores];
                
            } catch (Exception $e) {
                $this->db->rollback();
                fclose($handle);
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }
        
        return ['success' => false, 'error' => 'No se pudo abrir el archivo'];
    }
}