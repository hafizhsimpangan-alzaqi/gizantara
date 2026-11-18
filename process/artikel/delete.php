<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/upload.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); exit('Metode tidak diizinkan.'); }
wajib_login();
periksa_csrf();
$id_artikel = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_artikel || ($_POST['confirm_delete'] ?? '') !== '1') {
    http_response_code(400); exit('Permintaan hapus tidak valid.');
}

$transaksi = false;
try {
    $koneksi = koneksi_database();
    mysqli_begin_transaction($koneksi);
    $transaksi = true;
    $cek = mysqli_prepare($koneksi, 'SELECT user_id, cover_image FROM articles WHERE id = ? FOR UPDATE');
    mysqli_stmt_bind_param($cek, 'i', $id_artikel);
    mysqli_stmt_execute($cek);
    mysqli_stmt_bind_result($cek, $pemilik, $sampul);
    $ditemukan = mysqli_stmt_fetch($cek);
    mysqli_stmt_close($cek);
    if (!$ditemukan) { mysqli_rollback($koneksi); http_response_code(404); exit('Artikel tidak ditemukan.'); }
    if (!boleh_ubah_konten($pemilik)) { mysqli_rollback($koneksi); http_response_code(403); exit('Akses ditolak.'); }

    if (role_pengguna() === 'admin') {
        $hapus = mysqli_prepare($koneksi, 'DELETE FROM articles WHERE id = ?');
        mysqli_stmt_bind_param($hapus, 'i', $id_artikel);
    } else {
        $id_user = id_user_login();
        $hapus = mysqli_prepare($koneksi, 'DELETE FROM articles WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($hapus, 'ii', $id_artikel, $id_user);
    }
    mysqli_stmt_execute($hapus);
    $terhapus = mysqli_stmt_affected_rows($hapus) === 1;
    mysqli_stmt_close($hapus);
    if (!$terhapus) throw new RuntimeException('Artikel tidak dapat dihapus.');
    mysqli_commit($koneksi);
    $transaksi = false;
} catch (Throwable $error) {
    if ($transaksi) mysqli_rollback($koneksi);
    error_log('Penghapusan artikel gagal.');
    pesan_sekali('Artikel belum dapat dihapus. Coba lagi nanti.');
    redirect_ke('artikel_kelola');
}

if ($sampul) {
    try { hapus_sampul_artikel($sampul, $koneksi); }
    catch (Throwable $error) { error_log('Sampul artikel terhapus belum dapat dibersihkan.'); }
}

pesan_sekali('Artikel berhasil dihapus.', 'success');
redirect_ke('artikel_kelola');
