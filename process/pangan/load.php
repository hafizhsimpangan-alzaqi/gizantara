<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
require_once __DIR__ . '/../common/upload.php';
try {
    $koneksi = koneksi_database();
    if (role_pengguna() === 'admin') {
        $hasil = mysqli_query($koneksi, 'SELECT f.*, u.username AS author FROM foods f JOIN users u ON u.id=f.user_id ORDER BY f.created_at DESC, f.id DESC');
    } else {
        $id_user = id_user_login();
        $cek = mysqli_prepare($koneksi, 'SELECT f.*, u.username AS author FROM foods f JOIN users u ON u.id=f.user_id WHERE f.user_id=? ORDER BY f.created_at DESC, f.id DESC');
        mysqli_stmt_bind_param($cek, 'i', $id_user);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);
    }
    $daftar_kelola_pangan = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
    if (isset($cek)) mysqli_stmt_close($cek);
    foreach ($daftar_kelola_pangan as &$item) $item['image_url'] = path_gambar_pangan($item['image']);
    unset($item);
} catch (Throwable $error) {
    error_log('Daftar kelola pangan gagal dimuat.');
    http_response_code(500); exit('Katalog belum dapat ditampilkan.');
}
