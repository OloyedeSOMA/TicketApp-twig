<?php
require_once __DIR__ . '/../vendor/autoload.php';
// === STATIC FILE HANDLER ===
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$filePath = __DIR__ . '/../public' . $requestUri;
if (is_file($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'css' => 'text/css',
        'js' => 'application/javascript',
    ];
    $mime = $mimeTypes[$ext] ?? 'application/octet-stream';
    header("Content-Type: $mime");
    readfile($filePath);
    exit;
}

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// === TWIG SETUP ===
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

// === FEATURES (EXAMPLE DATA) ===
$features = [
    ['icon' => 'ticket.svg', 'color' => 'bg-blue-100 text-blue-600', 'title' => 'Create Tickets', 'desc' => 'Quickly create and organize support tickets with detailed descriptions and priorities.'],
    ['icon' => 'chart.svg', 'color' => 'bg-yellow-100 text-yellow-600', 'title' => 'Track Progress', 'desc' => 'Monitor ticket status in real-time and keep your team aligned on priorities.'],
    ['icon' => 'check.svg', 'color' => 'bg-green-100 text-green-600', 'title' => 'Resolve Faster', 'desc' => 'Streamline your workflow and close tickets efficiently with smart organization.'],
    ['icon' => 'zap.svg', 'color' => 'bg-purple-100 text-purple-600', 'title' => 'Boost Productivity', 'desc' => 'Increase team efficiency with intuitive tools and clear visibility into all tickets.'],
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

// === RENDER PAGE OR 404 ===
if (array_key_exists($uri, $routes)) {
    $config = $routes[$uri];
    $template = $config['template'];
    $vars = is_callable($config['vars']) ? $config['vars']() : $config['vars'];
    echo $twig->render($template, $vars);
    exit;
}

// === 404 PAGE ===
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 | Page Not Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="text-center">
    <h1 class="text-6xl font-bold text-red-600 mb-4">404</h1>
    <p class="text-lg text-gray-700 mb-6">
      Page not found:
      <code class="bg-gray-100 px-2 py-1 rounded text-sm text-gray-600">
        <?= htmlspecialchars($uri) ?>
      </code>
    </p>
    <a href="/" class="text-indigo-600 hover:underline text-base font-medium">← Go Back Home</a>
  </div>
</body>
</html>


