<?php
require_once __DIR__ . '/Model.php';

class Pago extends Model {
    protected $table = 'pagos';
    
    /**
     * Obtiene el pago de una venta
     */
    public function getByVenta($venta_id) {
        $sql = "SELECT * FROM pagos WHERE venta_id = ?";
        return $this->db->fetchOne($sql, [$venta_id]);
    }
    
    /**
     * Registra un nuevo pago (primera vez)
     */
    public function registrar($venta_id, $pago_inicial, $pago_final, $saldo) {
        $sql = "INSERT INTO pagos (venta_id, pago_inicial, pago_final, saldo)
                VALUES (?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $venta_id,
            $pago_inicial,
            $pago_final,
            $saldo
        ]);
    }
    
    /**
     * Actualiza un pago existente (nuevo abono)
     */
    public function actualizarAbono($venta_id, $nuevo_abono, $nuevo_saldo) {
        $sql = "UPDATE pagos 
                SET pago_final = ?, saldo = ?
                WHERE venta_id = ?";
        
        return $this->db->query($sql, [
            $nuevo_abono,
            $nuevo_saldo,
            $venta_id
        ]);
    }
}