<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }

try {
    $koneksi = koneksi_database();
    $id_user = id_user_login();
    $periksa = mysqli_prepare($koneksi, 'SELECT username, email, role FROM users WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($periksa, 'i', $id_user);
    mysqli_stmt_execute($periksa);
    mysqli_stmt_bind_result($periksa, $nama_profil, $email_profil, $role_profil);
    $ada_profil = mysqli_stmt_fetch($periksa);
    mysqli_stmt_close($periksa);
    if (!$ada_profil || $role_profil !== role_pengguna()) {
        $_SESSION = [];
        redirect_ke('login');
    }
    $profil = ['username' => $nama_profil, 'email' => $email_profil, 'role' => $role_profil];
} catch (Throwable $error) {
    error_log('Pemuatan Profil gagal.');
    http_response_code(500);
    exit('Profil belum dapat ditampilkan.');
}

$profile_state = $_SESSION['profile_state'] ?? [];
unset($_SESSION['profile_state']);
if (!is_array($profile_state)) $profile_state = [];
