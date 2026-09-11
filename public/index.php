<?php
// StockFlow - Front Controller & Simple MVC Router

// Autoload manual simples para o namespace App
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Helpers\Session;

Session::start();

// Obter a rota atual
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Mapeamento de Rotas
$routes = [
    '/' => ['App\Controllers\DashboardController', 'index'],
    '/login' => ['App\Controllers\AuthController', 'showLogin'],
    '/logout' => ['App\Controllers\AuthController', 'logout'],
    '/dashboard' => ['App\Controllers\DashboardController', 'index'],
    
    // Categorias
    '/categories' => ['App\Controllers\CategoryController', 'index'],
    '/categories/store' => ['App\Controllers\CategoryController', 'store'],
    '/categories/update' => ['App\Controllers\CategoryController', 'update'],
    '/categories/delete' => ['App\Controllers\CategoryController', 'delete'],

    // Produtos
    '/products' => ['App\Controllers\ProductController', 'index'],
    '/products/store' => ['App\Controllers\ProductController', 'store'],
    '/products/update' => ['App\Controllers\ProductController', 'update'],
    '/products/delete' => ['App\Controllers\ProductController', 'delete'],

    // Requisições
    '/requisitions' => ['App\Controllers\RequisitionController', 'index'],
    '/requisitions/store' => ['App\Controllers\RequisitionController', 'store'],
    '/requisitions/status' => ['App\Controllers\RequisitionController', 'updateStatus'],

    // Utilizadores
    '/users' => ['App\Controllers\UserController', 'index'],
    '/users/store' => ['App\Controllers\UserController', 'store'],
    '/users/update' => ['App\Controllers\UserController', 'update'],
    '/users/delete' => ['App\Controllers\UserController', 'delete'],
];

// Processamento de formulários POST no login
if ($uri === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new App\Controllers\AuthController();
    $controller->login();
    exit;
}

// Despacho de rotas
if (array_key_exists($uri, $routes)) {
    [$class, $method] = $routes[$uri];
    $controller = new $class();
    $controller->$method();
} else {
    http_response_code(404);
    require __DIR__ . '/../views/errors/404.php';
}
