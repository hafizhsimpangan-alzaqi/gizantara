<?php
// Router ini hanya untuk `php -S ... -t . process/common/dev_router.php` saat pengembangan.
$root = dirname(__DIR__, 2);
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

if ($path === '/' || $path === '/index.php') {
    require $root . '/index.php';
    return true;
}

if (preg_match('#^/view/(css|js|uploads)/#', $path)) {
    $file = realpath($root . $path);
    $folder = explode('/', trim($path, '/'))[1];
    $allowed = realpath($root . '/view/' . $folder);
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $jenis_aman = ($folder === 'css' && in_array($extension, ['css', 'woff2'], true))
        || ($folder === 'js' && $extension === 'js')
        || ($folder === 'uploads' && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true));
    if ($file !== false && $allowed !== false && str_starts_with($file, $allowed . DIRECTORY_SEPARATOR)
        && is_file($file) && $jenis_aman
        && !preg_match('/\.(php[0-9]?|phtml|phar|cgi|pl|sh)(\.|$)/i', $path)) {
        return false;
    }
}

http_response_code(404);
echo 'Halaman tidak ditemukan.';
