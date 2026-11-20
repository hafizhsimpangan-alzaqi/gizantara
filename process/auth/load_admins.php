<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }

try {
    $hasil = mysqli_query(
        koneksi_database(),
        "SELECT id, username, email, role, status, banned_at, ban_reason, created_at
         FROM users
         WHERE role = 'admin'
         ORDER BY created_at DESC, id DESC"
    );
    $daftar_admins = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
} catch (Throwable $error) {
    error_log('Daftar Admin gagal dimuat.');
    http_response_code(500);
    exit('Data Admin belum dapat ditampilkan.');
}
