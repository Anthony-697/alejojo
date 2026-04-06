<?php
// Configuración del sistema
define('BASE_URL', '/sistema_ventas_v2/');
define('APP_PATH', dirname(__DIR__) . '/');
define('UPLOAD_PATH', APP_PATH . 'uploads/');

// Configuración de zona horaria
date_default_timezone_set('America/El_Salvador');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para debug
function debug($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

// Función para redireccionar
function redirect($url) {
    header('Location: ' . BASE_URL . ltrim($url, '/'));
    exit;
}

// Función para escapar HTML
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Función para formatear moneda
function money($amount) {
    return '$' . number_format((float)$amount, 2);
}

// Función para verificar si usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Función para requerir login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

// Función para verificar rol
function hasRole($role) {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === $role;
}

// 🔥 FUNCIÓN PARA GENERAR EL MENÚ SEGÚN EL ROL
function renderMenu() {
    $menu = '<div class="navbar">
                <div class="logo">🛍️ ALEJOJO V2</div>
                <a href="' . BASE_URL . 'dashboard">🏠 Inicio</a>';
    
    // Módulos que todos ven
    $menu .= '<a href="' . BASE_URL . 'productos">📦 Productos</a>';
    $menu .= '<a href="' . BASE_URL . 'clientes">👤 Clientes</a>';
    $menu .= '<a href="' . BASE_URL . 'ventas/crear">🛒 Nueva Venta</a>';
    $menu .= '<a href="' . BASE_URL . 'ventas">📊 Historial</a>';
    $menu .= '<a href="' . BASE_URL . 'reportes/deudores">💳 Deudores</a>';
    $menu .= '<a href="' . BASE_URL . 'reportes/stock">📦 Stock</a>';
    
    // Módulos solo para admin
    if (hasRole('admin')) {
        $menu .= '<a href="' . BASE_URL . 'campanas">📢 Campañas</a>';
        $menu .= '<a href="' . BASE_URL . 'entradas/crear">📥 Compras</a>';
        $menu .= '<a href="' . BASE_URL . 'usuarios">👥 Usuarios</a>';
    }
    
    // Cierre del menú
    $menu .= '<div style="margin-left:auto;">
                👤 ' . h($_SESSION['usuario_nombre']) . '
                <a href="' . BASE_URL . 'usuarios/cambiar_password" style="background:#f59e0b; margin-left:10px; padding:5px 10px; border-radius:8px; text-decoration:none; color:white;">🔒 Cambiar Contraseña</a>
                <a href="' . BASE_URL . 'auth/logout" style="background:#ef4444; margin-left:10px; padding:5px 10px; border-radius:8px; text-decoration:none; color:white;">🚪 Salir</a>
            </div>
        </div>';
    
    return $menu;
}
?>