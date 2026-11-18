<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

$envPath = dirname(__DIR__, 2) . '/.env';
$env = is_file($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_RAW) : false;
if ($env === false) {
    http_response_code(500);
    exit('Konfigurasi aplikasi tidak tersedia.');
}
$baseUrl = rtrim($env['BASE_URL'] ?? '', '/');

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/database.php';
