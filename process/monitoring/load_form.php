<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }

$catatan_form = null;
if (isset($_GET['id'])) {
    $id_catatan = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id_catatan) {
        http_response_code(404); $template = '404.php'; $pesan_404 = 'Catatan tidak ditemukan.';
        return;
    }
    try {
        $koneksi = koneksi_database();
        $id_user = id_user_login();
        $stmt = mysqli_prepare($koneksi,
            'SELECT id, log_date, water_glasses, sleep_hours, activity_minutes
             FROM healthy_logs WHERE id = ? AND user_id = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'ii', $id_catatan, $id_user);
        mysqli_stmt_execute($stmt);
        $catatan_form = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
        mysqli_stmt_close($stmt);
    } catch (Throwable $error) {
        error_log('Form monitoring gagal dimuat.');
        http_response_code(500);
        exit('Form catatan belum dapat ditampilkan.');
    }
    if (!$catatan_form) {
        http_response_code(404); $template = '404.php'; $pesan_404 = 'Catatan tidak ditemukan.';
        return;
    }
    $page_title = 'Edit Catatan Monitoring';
}
