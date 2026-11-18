<?php
require_once __DIR__ . '/../common/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
wajib_login();
periksa_csrf();

$_SESSION = [];
$cookie = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 3600,
    'path' => $cookie['path'],
    'domain' => $cookie['domain'],
    'secure' => $cookie['secure'],
    'httponly' => $cookie['httponly'],
    'samesite' => $cookie['samesite']
]);
session_destroy();
header('Cache-Control: private, no-store, max-age=0');
redirect_ke('login');
