<?php
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_valid($token) {
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function periksa_csrf() {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Permintaan tidak valid.');
    }
}
