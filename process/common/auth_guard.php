<?php
function id_user_login() {
    $id = $_SESSION['user_id'] ?? null;
    return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
}

function role_pengguna() {
    $role = $_SESSION['role'] ?? null;
    return in_array($role, ['admin', 'user'], true) ? $role : null;
}

function pengguna_login() {
    return id_user_login() !== null && role_pengguna() !== null;
}

function wajib_login() {
    if (!pengguna_login()) {
        redirect_ke('login');
    }

    try {
        $koneksi = koneksi_database();
        $id_user = id_user_login();
        $periksa = mysqli_prepare($koneksi, 'SELECT role FROM users WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($periksa, 'i', $id_user);
        mysqli_stmt_execute($periksa);
        mysqli_stmt_bind_result($periksa, $role_tersimpan);
        $ditemukan = mysqli_stmt_fetch($periksa);
        mysqli_stmt_close($periksa);
    } catch (Throwable $error) {
        error_log('Pemeriksaan session gagal.');
        http_response_code(500);
        exit('Akses akun belum dapat diperiksa.');
    }
    if (!$ditemukan || $role_tersimpan !== role_pengguna()) {
        $_SESSION = [];
        redirect_ke('login');
    }
}

function wajib_admin() {
    wajib_login();
    if (role_pengguna() !== 'admin') {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

function boleh_ubah_konten($pemilik_id) {
    return pengguna_login() && (role_pengguna() === 'admin' || id_user_login() === (int) $pemilik_id);
}
