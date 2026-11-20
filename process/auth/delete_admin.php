<?php
require_once __DIR__ . '/../common/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
wajib_super_admin();
periksa_csrf();

$id_target = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_target || $id_target === id_user_login() || ($_POST['confirm_delete'] ?? '') !== '1') {
    pesan_sekali('Permintaan hapus Admin tidak valid.');
    redirect_ke('admins');
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
    if (!$ditemukan || $role_target !== 'admin') {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun Admin tidak ditemukan atau tidak dapat dihapus.');
        redirect_ke('admins');
    }

    $konten = mysqli_prepare(
        $koneksi,
        'SELECT (SELECT COUNT(*) FROM articles WHERE user_id = ?), (SELECT COUNT(*) FROM foods WHERE user_id = ?)'
    );
    mysqli_stmt_bind_param($konten, 'ii', $id_target, $id_target);
    mysqli_stmt_execute($konten);
    mysqli_stmt_bind_result($konten, $jumlah_artikel, $jumlah_pangan);
    mysqli_stmt_fetch($konten);
    mysqli_stmt_close($konten);
    if ($jumlah_artikel > 0 || $jumlah_pangan > 0) {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun Admin masih memiliki artikel atau bahan pangan. Tangani konten tersebut sebelum menghapus Admin.');
        redirect_ke('admins');
    }

    $id_super_admin = id_user_login();
    $hapus = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ? AND role = 'admin' AND id <> ?");
    mysqli_stmt_bind_param($hapus, 'ii', $id_target, $id_super_admin);
    mysqli_stmt_execute($hapus);
    $terhapus = mysqli_stmt_affected_rows($hapus) === 1;
    mysqli_stmt_close($hapus);
    if (!$terhapus) {
        mysqli_rollback($koneksi);
        pesan_sekali('Akun Admin tidak ditemukan atau tidak dapat dihapus.');
        redirect_ke('admins');
    }

    mysqli_commit($koneksi);
    $transaksi = false;
} catch (mysqli_sql_exception $error) {
    if ($transaksi) {
        mysqli_rollback($koneksi);
    }
    if ($error->getCode() === 1451) {
        pesan_sekali('Akun Admin tidak dapat dihapus karena masih memiliki data terkait.');
    } else {
        error_log('Penghapusan Admin gagal.');
        pesan_sekali('Akun Admin belum dapat dihapus. Coba lagi nanti.');
    }
    redirect_ke('admins');
} catch (Throwable $error) {
    if ($transaksi) {
        mysqli_rollback($koneksi);
    }
    error_log('Penghapusan Admin gagal.');
    pesan_sekali('Akun Admin belum dapat dihapus. Coba lagi nanti.');
    redirect_ke('admins');
}

pesan_sekali('Akun Admin berhasil dihapus.', 'success');
redirect_ke('admins');
