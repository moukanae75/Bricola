<?php


require_once __DIR__ . '/../config/database.php';


require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/ArtisanController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ServiceController.php';
require_once __DIR__ . '/../app/controllers/WishController.php';
require_once __DIR__ . '/../app/controllers/EvaluationController.php';


require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';


$url = $_GET['url'] ?? '';
$url = trim($url, '/');
$url = strtolower($url);


$routes = [
    ''                => ['HomeController', 'index'],
    'home'            => ['HomeController', 'index'],
    'artisans'        => ['ArtisanController', 'index'],
    'login'           => ['AuthController', 'login'],
    'register'        => ['AuthController', 'register'],
    'logout'          => ['AuthController', 'logout'],
    'verify-email'    => ['AuthController', 'verifyEmail'],
    'dashboard'       => ['DashboardController', 'index'],
    'service-request' => ['ServiceController', 'index'],
    'toggle-wish'     => ['WishController', 'toggle'],
    'evaluer'         => ['EvaluationController', 'index'],
];


if (array_key_exists($url, $routes)) {

    [$controllerName, $method] = $routes[$url];

    $controller = new $controllerName();
    $controller->$method();

} else {

    http_response_code(404);

    echo '<div style="font-family:sans-serif;text-align:center;padding:4rem;">
        <h1>404 — Page introuvable</h1>
        <a href="/">← Retour à l\'accueil</a>
    </div>';
}