<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/upload.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); exit('Metode tidak diizinkan.'); }
wajib_login();
periksa_csrf();
$id_pangan = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_pangan || ($_POST['confirm_delete'] ?? '') !== '1') { http_response_code(400); exit('Permintaan hapus tidak valid.'); }
$transaksi = false;
try {
    $koneksi = koneksi_database();
    mysqli_begin_transaction($koneksi); $transaksi = true;
    $cek = mysqli_prepare($koneksi, 'SELECT user_id, image FROM foods WHERE id=? FOR UPDATE');
    mysqli_stmt_bind_param($cek, 'i', $id_pangan); mysqli_stmt_execute($cek);
    mysqli_stmt_bind_result($cek, $pemilik, $gambar);
    $ada = mysqli_stmt_fetch($cek); mysqli_stmt_close($cek);
    if (!$ada) { mysqli_rollback($koneksi); http_response_code(404); exit('Bahan pangan tidak ditemukan.'); }
    if (!boleh_ubah_konten($pemilik)) { mysqli_rollback($koneksi); http_response_code(403); exit('Akses ditolak.'); }
    $sql = 'DELETE FROM foods WHERE id=?';
    if (!adalah_admin()) $sql .= ' AND user_id=?';
    $hapus = mysqli_prepare($koneksi, $sql);
    if (adalah_admin()) mysqli_stmt_bind_param($hapus, 'i', $id_pangan);
    else { $id_user = id_user_login(); mysqli_stmt_bind_param($hapus, 'ii', $id_pangan, $id_user); }
    mysqli_stmt_execute($hapus);
    if (mysqli_stmt_affected_rows($hapus) !== 1) throw new RuntimeException('Bahan pangan tidak dapat dihapus.');
    mysqli_stmt_close($hapus);
    mysqli_commit($koneksi); $transaksi = false;
} catch (Throwable $error) {
    if ($transaksi) mysqli_rollback($koneksi);
    error_log('Penghapusan pangan gagal.'); pesan_sekali('Bahan pangan belum dapat dihapus.'); redirect_ke('pangan_kelola');
}
if ($gambar) { try { hapus_gambar_pangan($gambar, $koneksi); } catch (Throwable $error) { error_log('Gambar pangan belum dapat dibersihkan.'); } }
pesan_sekali('Bahan pangan berhasil dihapus.', 'success'); redirect_ke('pangan_kelola');
