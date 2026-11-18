<?php
require_once __DIR__ . '/../common/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); exit('Metode tidak diizinkan.'); }
wajib_login();
periksa_csrf();

$id_catatan = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_catatan || ($_POST['confirm_delete'] ?? '') !== '1') {
    http_response_code(400); exit('Permintaan hapus tidak valid.');
}

try {
    $koneksi = koneksi_database();
    $id_user = id_user_login();
    $hapus = mysqli_prepare($koneksi, 'DELETE FROM healthy_logs WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($hapus, 'ii', $id_catatan, $id_user);
    mysqli_stmt_execute($hapus);
    $terhapus = mysqli_stmt_affected_rows($hapus) === 1;
    mysqli_stmt_close($hapus);
    if (!$terhapus) { http_response_code(404); exit('Catatan tidak ditemukan.'); }
} catch (Throwable $error) {
    error_log('Penghapusan monitoring gagal.');
    pesan_sekali('Catatan belum dapat dihapus. Coba lagi nanti.');
    redirect_ke('monitoring');
}

pesan_sekali('Catatan berhasil dihapus.', 'success');
redirect_ke('monitoring');
