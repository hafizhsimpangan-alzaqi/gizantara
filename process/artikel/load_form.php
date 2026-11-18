<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/quill.php';
require_once __DIR__ . '/../common/upload.php';

$artikel_form = null;
if (isset($_GET['id'])) {
    $id_artikel = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id_artikel) {
        http_response_code(404); $template = '404.php'; $pesan_404 = 'Artikel tidak ditemukan.';
        return;
    }
    try {
        $koneksi = koneksi_database();
        $cek = mysqli_prepare($koneksi, 'SELECT id, user_id, title, category, cover_image, body_delta FROM articles WHERE id = ?');
        mysqli_stmt_bind_param($cek, 'i', $id_artikel);
        mysqli_stmt_execute($cek);
        $artikel_form = mysqli_fetch_assoc(mysqli_stmt_get_result($cek)) ?: null;
        mysqli_stmt_close($cek);
    } catch (Throwable $error) {
        error_log('Form artikel gagal dimuat.');
        http_response_code(500); exit('Form artikel belum dapat ditampilkan.');
    }
    if (!$artikel_form) {
        http_response_code(404); $template = '404.php'; $pesan_404 = 'Artikel tidak ditemukan.';
        return;
    }
    if (!boleh_ubah_konten($artikel_form['user_id'])) {
        http_response_code(403); exit('Akses ditolak.');
    }
    if (artikel_delta_lama($artikel_form['body_delta'])) {
        http_response_code(409); exit('Artikel format lama perlu dikonversi sebelum diedit.');
    }
    $artikel_form['body_delta'] = sanitasi_html_artikel($artikel_form['body_delta']);
    if ($artikel_form['body_delta'] === false) {
        http_response_code(409); exit('Isi artikel perlu ditinjau sebelum diedit.');
    }
    $artikel_form['cover_url'] = path_sampul_artikel($artikel_form['cover_image']);
    $page_title = 'Edit Artikel';
}
