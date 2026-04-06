<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Obtener la URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');
$urlParts = explode('/', $url);

// 🔥 IMPORTANTE: Definir controlador y acción por defecto
if (empty($url) || $url == '') {
    // Si no hay URL, ir al login (AuthController/login)
    $controller = 'auth';
    $action = 'login';
    $param = null;
} else {
    $controller = isset($urlParts[0]) ? $urlParts[0] : 'auth';
    $action = isset($urlParts[1]) ? $urlParts[1] : 'index';
    $param = isset($urlParts[2]) ? $urlParts[2] : null;
}

// Mapeo de controladores
$controllers = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'productos' => 'ProductoController',
    'clientes' => 'ClienteController',
    'campanas' => 'CampanaController',
    'ventas' => 'VentaController',
    'entradas' => 'EntradaController',
    'pagos' => 'PagoController',
    'promociones' => 'PromocionController',
    'reportes' => 'ReporteController',
    'usuarios' => 'UsuarioController'  // ← Agregar esta línea
];

if (!isset($controllers[$controller])) {
    die("Controlador no encontrado: " . htmlspecialchars($controller));
}

$controllerClass = $controllers[$controller];
$controllerFile = __DIR__ . '/app/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    die("Archivo no encontrado: " . $controllerFile);
}

require_once $controllerFile;
$controllerInstance = new $controllerClass();

if (!method_exists($controllerInstance, $action)) {
    die("Acción no encontrada: " . $action . " en " . $controllerClass);
}

// Ejecutar
if ($param !== null) {
    $controllerInstance->$action($param);
} else {
    $controllerInstance->$action();
}