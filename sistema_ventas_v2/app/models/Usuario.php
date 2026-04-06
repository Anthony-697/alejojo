<?php
require_once __DIR__ . '/Model.php';

class Usuario extends Model {
    protected $table = 'usuarios';
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM usuarios WHERE username = :username AND activo = 1";
        return $this->db->fetchOne($sql, [':username' => $username]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    public function actualizarPassword($id, $nuevaPassword) {
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = :password, debe_cambiar_password = 0 WHERE id = :id";
        return $this->db->query($sql, [':password' => $hash, ':id' => $id]);
    }
    
    public function actualizarUltimoLogin($id) {
        $sql = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]);
    }
    
    public function generarUsername($nombres, $apellidos) {
        // Limpiar y normalizar
        $nombreLimpio = strtolower(trim($nombres));
        $apellidoLimpio = strtolower(trim($apellidos));
        
        // Separar primer nombre y primer apellido
        $primerNombre = explode(' ', $nombreLimpio)[0];
        $primerApellido = explode(' ', $apellidoLimpio)[0];
        
        $usernameBase = $primerNombre . '.' . $primerApellido;
        
        // Verificar si ya existe y agregar número si es necesario
        $username = $usernameBase;
        $contador = 1;
        
        while ($this->findByUsername($username)) {
            $username = $usernameBase . $contador;
            $contador++;
        }
        
        return $username;
    }
}