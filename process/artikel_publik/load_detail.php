<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/quill.php';
require_once __DIR__ . '/../common/upload.php';

$id_artikel = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id_artikel) {
    http_response_code(404); $template = '404.php'; $pesan_404 = 'Artikel tidak ditemukan.';
    return;
}
try {
    $koneksi = koneksi_database();
    $cek = mysqli_prepare($koneksi, 'SELECT a.id, a.title, a.category, a.cover_image, a.body_delta, a.created_at, a.updated_at,
        u.username AS author FROM articles a JOIN users u ON u.id = a.user_id WHERE a.id = ?');
    mysqli_stmt_bind_param($cek, 'i', $id_artikel);
    mysqli_stmt_execute($cek);
    $artikel = mysqli_fetch_assoc(mysqli_stmt_get_result($cek)) ?: null;
    mysqli_stmt_close($cek);
} catch (Throwable $error) {
    error_log('Detail artikel gagal dimuat.');
    http_response_code(500); exit('Artikel belum dapat ditampilkan.');
}
if (!$artikel) {
    http_response_code(404); $template = '404.php'; $pesan_404 = 'Artikel tidak ditemukan.';
    return;
}
$artikel['content_html'] = sanitasi_html_artikel($artikel['body_delta']);
$artikel['cover_url'] = path_sampul_artikel($artikel['cover_image']);
$page_title = $artikel['title'];
