<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }

$daftar_catatan = [];
try {
    $koneksi = koneksi_database();
    $id_user = id_user_login();
    $stmt = mysqli_prepare($koneksi,
        'SELECT id, log_date, water_glasses, sleep_hours, activity_minutes
         FROM healthy_logs WHERE user_id = ? ORDER BY log_date DESC, id DESC');
    mysqli_stmt_bind_param($stmt, 'i', $id_user);
    mysqli_stmt_execute($stmt);
    $daftar_catatan = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} catch (Throwable $error) {
    error_log('Riwayat monitoring gagal dimuat.');
    http_response_code(500);
    exit('Riwayat catatan belum dapat ditampilkan.');
}
