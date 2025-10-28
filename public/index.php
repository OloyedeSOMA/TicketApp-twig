<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, ['cache' => false]);

// === GET URI PROPERLY ===
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = parse_url($requestUri, PHP_URL_PATH);
$query = parse_url($requestUri, PHP_URL_QUERY);
parse_str($query ?? '', $params);

// === CLEAN URI (FIX ALL CASES) ===
$uri = trim($uri, '/');  // Remove leading/trailing slash
$uri = $uri === '' || $uri === 'index.php' ? '/' : '/' . $uri;



// === FEATURES ===
$features = [
    ['icon' => 'ticket.svg', 'color' => 'bg-blue-100 text-blue-600', 'title' => 'Create Tickets', 'desc' => 'Quickly create...'],
    ['icon' => 'chart.svg', 'color' => 'bg-yellow-100 text-yellow-600', 'title' => 'Track Progress', 'desc' => 'Monitor...'],
    ['icon' => 'check.svg', 'color' => 'bg-green-100 text-green-600', 'title' => 'Resolve Faster', 'desc' => 'Streamline...'],
    ['icon' => 'zap.svg', 'color' => 'bg-purple-100 text-purple-600', 'title' => 'Boost Productivity', 'desc' => 'Increase...'],
];

// === ROUTES ===
$routes = [
    '/' => ['template' => 'base.html.twig', 'vars' => ['features' => $features]],
    '/dashboard' => ['template' => 'dashboard.html.twig', 'vars' => []],
    '/tickets' => ['template' => 'tickets.html.twig', 'vars' => []],
    '/auth' => ['template' => 'auth-form.html.twig', 'vars' => function() use ($params) {
        $mode = $params['mode'] ?? 'login';
        $mode = in_array($mode, ['login', 'signup']) ? $mode : 'login';
        $redirect = $params['redirect'] ?? '/dashboard';
        return ['mode' => $mode, 'redirect' => $redirect];
    }],
];

// === RENDER ===
if (array_key_exists($uri, $routes)) {
    $config = $routes[$uri];
    $template = $config['template'];
    $vars = is_callable($config['vars']) ? $config['vars']() : $config['vars'];
    echo $twig->render($template, $vars);
} else {
    http_response_code(404);
    echo "
    <!DOCTYPE html>
    <html>
    <head>
      <title>404</title>
      <link href='/assets/css/tailwind.css' rel='stylesheet'>
    </head>
    <body class='bg-gray-50 min-h-screen flex items-center justify-center'>
      <div class='text-center'>
        <h1 class='text-6xl font-bold text-red-600'>404</h1>
        <p class='text-xl'>Page not found: <code>" . htmlspecialchars($uri) . "</code></p>
        <a href='/' class='text-indigo-600'>Go Home</a>
      </div>
    </body>
    </html>";
}

