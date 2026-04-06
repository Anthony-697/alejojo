<?php
require_once __DIR__ . '/Model.php';

class Campana extends Model {
    protected $table = 'campanas';
    
    public function getActive() {
        $hoy = date('Y-m-d');
        $sql = "SELECT * FROM campanas 
                WHERE fecha_inicio <= ? 
                AND fecha_fin >= ? 
                ORDER BY id DESC 
                LIMIT 1";
        return $this->db->fetchOne($sql, [$hoy, $hoy]);
    }
    
    public function getWithStatus() {
        $hoy = date('Y-m-d');
        $sql = "SELECT *, 
                CASE 
                    WHEN fecha_inicio > ? THEN 'pendiente'
                    WHEN fecha_fin < ? THEN 'finalizada'
                    ELSE 'activa'
                END as estado_texto
                FROM campanas 
                ORDER BY id DESC";
        return $this->db->fetchAll($sql, [$hoy, $hoy]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO campanas (nombre, fecha_inicio, fecha_fin) 
                VALUES (?, ?, ?)";
        
        return $this->db->insert($sql, [
            $data['nombre'],
            $data['fecha_inicio'],
            $data['fecha_fin']
        ]);
    }
}