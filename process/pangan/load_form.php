<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/upload.php';
$pangan_form = null;
if (isset($_GET['id'])) {
    $id_pangan = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id_pangan) { http_response_code(404); $template='404.php'; $pesan_404='Bahan pangan tidak ditemukan.'; return; }
    try {
        $cek = mysqli_prepare(koneksi_database(), 'SELECT * FROM foods WHERE id=?');
        mysqli_stmt_bind_param($cek, 'i', $id_pangan);
        mysqli_stmt_execute($cek);
        $pangan_form = mysqli_fetch_assoc(mysqli_stmt_get_result($cek)) ?: null;
        mysqli_stmt_close($cek);
    } catch (Throwable $error) { error_log('Form pangan gagal dimuat.'); http_response_code(500); exit('Form belum dapat ditampilkan.'); }
    if (!$pangan_form) { http_response_code(404); $template='404.php'; $pesan_404='Bahan pangan tidak ditemukan.'; return; }
    if (!boleh_ubah_konten($pangan_form['user_id'])) { http_response_code(403); exit('Akses ditolak.'); }
    $pangan_form['image_url'] = path_gambar_pangan($pangan_form['image']);
    $page_title = 'Edit Bahan Pangan';
}
