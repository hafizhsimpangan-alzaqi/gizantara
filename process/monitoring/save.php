<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/validasi.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); exit('Metode tidak diizinkan.'); }
wajib_login();
periksa_csrf();

$id_catatan = null;
if (($_POST['id'] ?? '') !== '') {
    $id_catatan = filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id_catatan) { http_response_code(404); exit('Catatan tidak ditemukan.'); }
}
$kembali = $baseUrl . '/index.php?page=monitoring_form' . ($id_catatan ? '&id=' . $id_catatan : '');
$tanggal = $_POST['log_date'] ?? null;
$air_input = $_POST['water_glasses'] ?? null;
$tidur_input = $_POST['sleep_hours'] ?? null;
$aktivitas_input = $_POST['activity_minutes'] ?? null;

if (!data_monitoring_valid($tanggal, $air_input, $tidur_input, $aktivitas_input)) {
    pesan_sekali('Periksa tanggal, gelas air (bilangan bulat), tidur (0–24 jam), dan aktivitas (0–1.440 menit).');
    header('Location: ' . $kembali); exit;
}

$id_user = id_user_login();
try {
    $koneksi = koneksi_database();
    if ($id_catatan) {
        $pemilik = mysqli_prepare($koneksi, 'SELECT id FROM healthy_logs WHERE id = ? AND user_id = ? LIMIT 1');
        mysqli_stmt_bind_param($pemilik, 'ii', $id_catatan, $id_user);
        mysqli_stmt_execute($pemilik);
        mysqli_stmt_store_result($pemilik);
        $catatan_milik_sendiri = mysqli_stmt_num_rows($pemilik) === 1;
        mysqli_stmt_close($pemilik);
        if (!$catatan_milik_sendiri) { http_response_code(404); exit('Catatan tidak ditemukan.'); }

        $simpan = mysqli_prepare($koneksi,
            'UPDATE healthy_logs SET log_date = ?, water_glasses = ?, sleep_hours = ?, activity_minutes = ?
             WHERE id = ? AND user_id = ?');
        $air = (int) $air_input;
        $tidur = (float) $tidur_input;
        $aktivitas = (int) $aktivitas_input;
        mysqli_stmt_bind_param($simpan, 'sidiii', $tanggal, $air, $tidur, $aktivitas, $id_catatan, $id_user);
        mysqli_stmt_execute($simpan);
        mysqli_stmt_close($simpan);
    } else {
        $cek_tanggal = mysqli_prepare($koneksi,
            'SELECT id FROM healthy_logs WHERE user_id = ? AND log_date = ? LIMIT 1');
        mysqli_stmt_bind_param($cek_tanggal, 'is', $id_user, $tanggal);
        mysqli_stmt_execute($cek_tanggal);
        mysqli_stmt_bind_result($cek_tanggal, $id_duplikat);
        $duplikat = mysqli_stmt_fetch($cek_tanggal);
        mysqli_stmt_close($cek_tanggal);
        if ($duplikat) {
            pesan_sekali('Tanggal tersebut sudah memiliki catatan. Perbarui catatan yang ada.');
            header('Location: ' . $baseUrl . '/index.php?page=monitoring_form&id=' . (int) $id_duplikat);
            exit;
        }

        $simpan = mysqli_prepare($koneksi,
            'INSERT INTO healthy_logs (user_id, log_date, water_glasses, sleep_hours, activity_minutes)
             VALUES (?, ?, ?, ?, ?)');
        $air = (int) $air_input;
        $tidur = (float) $tidur_input;
        $aktivitas = (int) $aktivitas_input;
        mysqli_stmt_bind_param($simpan, 'isidi', $id_user, $tanggal, $air, $tidur, $aktivitas);
        mysqli_stmt_execute($simpan);
        mysqli_stmt_close($simpan);
    }
} catch (mysqli_sql_exception $error) {
    if ($error->getCode() === 1062) {
        pesan_sekali('Tanggal tersebut sudah memiliki catatan. Perbarui catatan yang ada.');
    } else {
        error_log('Penyimpanan monitoring gagal.');
        pesan_sekali('Catatan belum dapat disimpan. Coba lagi nanti.');
    }
    header('Location: ' . $kembali); exit;
} catch (Throwable $error) {
    error_log('Penyimpanan monitoring gagal.');
    pesan_sekali('Catatan belum dapat disimpan. Coba lagi nanti.');
    header('Location: ' . $kembali); exit;
}

pesan_sekali($id_catatan ? 'Catatan berhasil diperbarui.' : 'Catatan berhasil disimpan.', 'success');
redirect_ke('monitoring');
