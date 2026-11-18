<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/quill.php';
require_once __DIR__ . '/../common/upload.php';

try {
    $hasil = mysqli_query(koneksi_database(), 'SELECT id, title, category, cover_image, body_delta, created_at
        FROM articles ORDER BY created_at DESC, id DESC LIMIT 3');
    $artikel_terbaru = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
    foreach ($artikel_terbaru as &$item) {
        $item['summary'] = ringkasan_html_artikel($item['body_delta']);
        $item['cover_url'] = path_sampul_artikel($item['cover_image']);
    }
    unset($item);
    $hasil_pangan = mysqli_query(koneksi_database(), 'SELECT id, name, image, energy_kcal FROM foods ORDER BY created_at DESC, id DESC LIMIT 4');
    $pangan_terbaru = mysqli_fetch_all($hasil_pangan, MYSQLI_ASSOC);
    foreach ($pangan_terbaru as &$item) $item['image_url'] = path_gambar_pangan($item['image']);
    unset($item);
} catch (Throwable $error) {
    error_log('Artikel terbaru gagal dimuat.');
    http_response_code(500); exit('Beranda belum dapat ditampilkan.');
}
