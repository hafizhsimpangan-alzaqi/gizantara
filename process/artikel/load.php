<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/upload.php';

try {
    $koneksi = koneksi_database();
    if (adalah_admin()) {
        $hasil = mysqli_query($koneksi, 'SELECT a.id, a.title, a.category, a.cover_image, a.moderation_status, a.moderated_at, a.rejection_reason, a.created_at, u.username AS author
            FROM articles a JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC, a.id DESC');
    } else {
        $id_user = id_user_login();
        $periksa = mysqli_prepare($koneksi, 'SELECT a.id, a.title, a.category, a.cover_image, a.moderation_status, a.moderated_at, a.rejection_reason, a.created_at, u.username AS author
            FROM articles a JOIN users u ON u.id = a.user_id WHERE a.user_id = ? ORDER BY a.created_at DESC, a.id DESC');
        mysqli_stmt_bind_param($periksa, 'i', $id_user);
        mysqli_stmt_execute($periksa);
        $hasil = mysqli_stmt_get_result($periksa);
    }
    $daftar_kelola = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
    if (isset($periksa)) mysqli_stmt_close($periksa);
    foreach ($daftar_kelola as &$item) $item['cover_url'] = path_sampul_artikel($item['cover_image']);
    unset($item);
} catch (Throwable $error) {
    error_log('Daftar kelola artikel gagal dimuat.');
    http_response_code(500);
    exit('Daftar artikel belum dapat ditampilkan.');
}
