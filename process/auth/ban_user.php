<?php
require_once __DIR__ . '/../common/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

$aksi_page = $_GET['page'] ?? '';
$kelola_admin = in_array($aksi_page, ['admin_ban', 'admin_unban'], true);
$akan_ban = in_array($aksi_page, ['user_ban', 'admin_ban'], true);
$target_role_wajib = $kelola_admin ? 'admin' : 'user';
$redirect_page = $kelola_admin ? 'admins' : 'users';

if ($kelola_admin) {
    wajib_super_admin();
} else {
    wajib_admin();
}
periksa_csrf();

$id_target = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_target || $id_target === id_user_login()) {
    pesan_sekali('Permintaan pemblokiran akun tidak valid.');
    redirect_ke($redirect_page);
}

$alasan = is_string($_POST['ban_reason'] ?? null) ? trim($_POST['ban_reason']) : '';
if ($akan_ban && ($alasan === '' || !mb_check_encoding($alasan, 'UTF-8') || mb_strlen($alasan, 'UTF-8') > 255)) {
    pesan_sekali('Alasan pemblokiran wajib diisi dan maksimal 255 karakter.');
    redirect_ke($redirect_page);
}

$koneksi = koneksi_database();
$transaksi = false;
try {
    mysqli_begin_transaction($koneksi);
    $transaksi = true;

    $cek = mysqli_prepare($koneksi, 'SELECT role, status FROM users WHERE id = ? LIMIT 1 FOR UPDATE');
    mysqli_stmt_bind_param($cek, 'i', $id_target);
    mysqli_stmt_execute($cek);
    mysqli_stmt_bind_result($cek, $role_target, $status_target);
    $ditemukan = mysqli_stmt_fetch($cek);
    mysqli_stmt_close($cek);

    if (!$ditemukan || $role_target !== $target_role_wajib) {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun target tidak ditemukan atau tidak berada dalam kewenangan Anda.');
        redirect_ke($redirect_page);
    }

    if ($akan_ban) {
        if ($status_target === 'banned') {
            mysqli_rollback($koneksi);
            pesan_sekali('Akun sudah dalam status diblokir.');
            redirect_ke($redirect_page);
        }
        $id_pelaku = id_user_login();
        $ubah = mysqli_prepare(
            $koneksi,
            "UPDATE users SET status = 'banned', banned_at = NOW(), ban_reason = ?, banned_by = ? WHERE id = ? AND role = ? AND id <> ?"
        );
        mysqli_stmt_bind_param($ubah, 'siisi', $alasan, $id_pelaku, $id_target, $target_role_wajib, $id_pelaku);
    } else {
        if ($status_target === 'active') {
            mysqli_rollback($koneksi);
            pesan_sekali('Akun sudah dalam status aktif.');
            redirect_ke($redirect_page);
        }
        $id_pelaku = id_user_login();
        $ubah = mysqli_prepare(
            $koneksi,
            "UPDATE users SET status = 'active', banned_at = NULL, ban_reason = NULL, banned_by = NULL WHERE id = ? AND role = ? AND id <> ?"
        );
        mysqli_stmt_bind_param($ubah, 'isi', $id_target, $target_role_wajib, $id_pelaku);
    }

    mysqli_stmt_execute($ubah);
    $berhasil = mysqli_stmt_affected_rows($ubah) === 1;
    mysqli_stmt_close($ubah);

    if (!$berhasil) {
        mysqli_rollback($koneksi);
        pesan_sekali('Status akun belum dapat diperbarui.');
        redirect_ke($redirect_page);
    }

    mysqli_commit($koneksi);
    $transaksi = false;
} catch (Throwable $error) {
    if ($transaksi) {
        mysqli_rollback($koneksi);
    }
    error_log('Perubahan status blokir akun gagal.');
    pesan_sekali('Status akun belum dapat diperbarui. Coba lagi nanti.');
    redirect_ke($redirect_page);
}

$label = $kelola_admin ? 'Admin' : 'User';
pesan_sekali(
    $akan_ban ? "Akun $label berhasil diblokir." : "Blokir akun $label berhasil dibuka.",
    'success'
);
redirect_ke($redirect_page);
