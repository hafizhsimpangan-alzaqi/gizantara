<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }

$dashboard_admin = role_pengguna() === 'admin';
$id_user = id_user_login();
$jumlah_user = 0;
$jumlah_artikel = 0;
$jumlah_pangan = 0;
$jumlah_catatan = 0;
$catatan_terakhir = null;

try {
    $koneksi = koneksi_database();
    if ($dashboard_admin) {
        $jumlah_user = (int) mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE role = 'user'"))[0];
        $jumlah_artikel = (int) mysqli_fetch_row(mysqli_query($koneksi, 'SELECT COUNT(*) FROM articles'))[0];
        $jumlah_pangan = (int) mysqli_fetch_row(mysqli_query($koneksi, 'SELECT COUNT(*) FROM foods'))[0];
    } else {
        $artikel = mysqli_prepare($koneksi, 'SELECT COUNT(*) FROM articles WHERE user_id = ?');
        mysqli_stmt_bind_param($artikel, 'i', $id_user);
        mysqli_stmt_execute($artikel);
        mysqli_stmt_bind_result($artikel, $jumlah_artikel);
        mysqli_stmt_fetch($artikel);
        mysqli_stmt_close($artikel);

        $pangan = mysqli_prepare($koneksi, 'SELECT COUNT(*) FROM foods WHERE user_id = ?');
        mysqli_stmt_bind_param($pangan, 'i', $id_user);
        mysqli_stmt_execute($pangan);
        mysqli_stmt_bind_result($pangan, $jumlah_pangan);
        mysqli_stmt_fetch($pangan);
        mysqli_stmt_close($pangan);
    }

    $hitung_catatan = mysqli_prepare($koneksi, 'SELECT COUNT(*) FROM healthy_logs WHERE user_id = ?');
    mysqli_stmt_bind_param($hitung_catatan, 'i', $id_user);
    mysqli_stmt_execute($hitung_catatan);
    mysqli_stmt_bind_result($hitung_catatan, $jumlah_catatan);
    mysqli_stmt_fetch($hitung_catatan);
    mysqli_stmt_close($hitung_catatan);

    $monitoring = mysqli_prepare($koneksi,
        'SELECT log_date, water_glasses, sleep_hours, activity_minutes FROM healthy_logs WHERE user_id = ? ORDER BY log_date DESC, id DESC LIMIT 1');
    mysqli_stmt_bind_param($monitoring, 'i', $id_user);
    mysqli_stmt_execute($monitoring);
    mysqli_stmt_bind_result($monitoring, $tanggal, $air, $tidur, $aktivitas);
    if (mysqli_stmt_fetch($monitoring)) {
        $catatan_terakhir = ['tanggal' => $tanggal, 'air' => $air, 'tidur' => $tidur, 'aktivitas' => $aktivitas];
    }
    mysqli_stmt_close($monitoring);
} catch (Throwable $error) {
    error_log('Ringkasan dashboard gagal dimuat.');
    http_response_code(500);
    exit('Ringkasan belum dapat ditampilkan.');
}
