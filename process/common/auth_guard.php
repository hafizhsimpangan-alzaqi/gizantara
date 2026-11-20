<?php
function id_user_login() {
    $id = $_SESSION['user_id'] ?? null;
    return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
}

function role_pengguna() {
    $role = $_SESSION['role'] ?? null;
    return in_array($role, ['super_admin', 'admin', 'user'], true) ? $role : null;
}

function adalah_admin() {
    return in_array(role_pengguna(), ['admin', 'super_admin'], true);
}

function adalah_super_admin() {
    return role_pengguna() === 'super_admin';
}

function label_role($role) {
    if ($role === 'super_admin') {
        return 'Super Admin';
    }
    if ($role === 'admin') {
        return 'Admin';
    }
    return 'User';
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
        $periksa = mysqli_prepare($koneksi, 'SELECT role, status FROM users WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($periksa, 'i', $id_user);
        mysqli_stmt_execute($periksa);
        mysqli_stmt_bind_result($periksa, $role_tersimpan, $status_tersimpan);
        $ditemukan = mysqli_stmt_fetch($periksa);
        mysqli_stmt_close($periksa);
    } catch (Throwable $error) {
        error_log('Pemeriksaan session gagal.');
        http_response_code(500);
        exit('Akses akun belum dapat diperiksa.');
    }
    if (!$ditemukan || $role_tersimpan !== role_pengguna() || $status_tersimpan !== 'active') {
        $_SESSION = [];
        session_regenerate_id(true);
        if ($ditemukan && $status_tersimpan === 'banned') {
            pesan_sekali('Akun Anda sedang diblokir. Hubungi administrator bila memerlukan bantuan.');
        }
        redirect_ke('login');
    }
}

function wajib_admin() {
    wajib_login();
    if (!adalah_admin()) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

function wajib_super_admin() {
    wajib_login();
    if (!adalah_super_admin()) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

function boleh_ubah_konten($pemilik_id) {
    return pengguna_login() && (adalah_admin() || id_user_login() === (int) $pemilik_id);
}
