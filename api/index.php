<?php
/**
 * Vercel PHP Serverless Entrypoint - Magang ID
 * Routes all requests to the appropriate PHP file
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Default to index.php if root
if (empty($path) || $path === '/') {
    $path = 'index.php';
}

$root = dirname(__DIR__);
$fullPath = $root . '/' . $path;

// If path is a directory, look for index.php inside
if (is_dir($fullPath)) {
    $fullPath = rtrim($fullPath, '/') . '/index.php';
    $path = rtrim($path, '/') . '/index.php';
}

// If it's a PHP file that exists, include it
if (file_exists($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
    // Set the working directory to the file's directory so relative includes work
    chdir(dirname($fullPath));
    require $fullPath;
} elseif (file_exists($fullPath)) {
    // Serve static file
    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($fullPath);
} else {
    // Fallback to index.php
    chdir($root);
    require $root . '/index.php';
}
