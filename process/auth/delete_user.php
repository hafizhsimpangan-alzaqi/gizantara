<?php
require_once __DIR__ . '/../common/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
wajib_admin();
periksa_csrf();

$id_target = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_target || $id_target === id_user_login() || ($_POST['confirm_delete'] ?? '') !== '1') {
    pesan_sekali('Permintaan hapus User tidak valid.');
    redirect_ke('users');
}

$koneksi = koneksi_database();
$transaksi = false;
try {
    mysqli_begin_transaction($koneksi);
    $transaksi = true;

    $periksa = mysqli_prepare($koneksi, 'SELECT role FROM users WHERE id = ? LIMIT 1 FOR UPDATE');
    mysqli_stmt_bind_param($periksa, 'i', $id_target);
    mysqli_stmt_execute($periksa);
    mysqli_stmt_bind_result($periksa, $role_target);
    $ditemukan = mysqli_stmt_fetch($periksa);
    mysqli_stmt_close($periksa);
    if (!$ditemukan || $role_target !== 'user') {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun User tidak ditemukan atau tidak dapat dihapus.');
        redirect_ke('users');
    }

    $konten = mysqli_prepare($koneksi,
        'SELECT (SELECT COUNT(*) FROM articles WHERE user_id = ?), (SELECT COUNT(*) FROM foods WHERE user_id = ?)');
    mysqli_stmt_bind_param($konten, 'ii', $id_target, $id_target);
    mysqli_stmt_execute($konten);
    mysqli_stmt_bind_result($konten, $jumlah_artikel, $jumlah_pangan);
    mysqli_stmt_fetch($konten);
    mysqli_stmt_close($konten);
    if ($jumlah_artikel > 0 || $jumlah_pangan > 0) {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun masih memiliki artikel atau bahan pangan. Tangani konten tersebut sebelum menghapus User.');
        redirect_ke('users');
    }

    $hapus = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ? AND role = 'user' AND id <> ?");
    $id_admin = id_user_login();
    mysqli_stmt_bind_param($hapus, 'ii', $id_target, $id_admin);
    mysqli_stmt_execute($hapus);
    $terhapus = mysqli_stmt_affected_rows($hapus) === 1;
    mysqli_stmt_close($hapus);
    if (!$terhapus) {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun User tidak ditemukan atau tidak dapat dihapus.');
        redirect_ke('users');
    }

    mysqli_commit($koneksi);
    $transaksi = false;
} catch (Throwable $error) {
    if ($transaksi) mysqli_rollback($koneksi);
    error_log('Penghapusan User gagal.');
    pesan_sekali('Akun User belum dapat dihapus. Coba lagi nanti.');
    redirect_ke('users');
}

pesan_sekali('Akun User berhasil dihapus.', 'success');
redirect_ke('users');
