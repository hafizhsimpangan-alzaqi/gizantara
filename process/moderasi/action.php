<?php
require_once __DIR__ . '/../common/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
wajib_admin();
periksa_csrf();

$aksi_page = $_GET['page'] ?? '';
$is_pangan = $aksi_page === 'moderasi_pangan_action';
$content_type = $is_pangan ? 'food' : 'article';
$tabel = $is_pangan ? 'foods' : 'articles';
$redirect_page = $is_pangan ? 'moderasi_pangan' : 'moderasi_artikel';

$id_konten = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$keputusan = $_POST['decision'] ?? '';
$alasan = is_string($_POST['reason'] ?? null) ? trim($_POST['reason']) : '';

if (!$id_konten || !in_array($keputusan, ['approved', 'rejected'], true)) {
    pesan_sekali('Permintaan moderasi tidak valid.');
    redirect_ke($redirect_page);
}

if ($keputusan === 'rejected') {
    if ($alasan === '' || !mb_check_encoding($alasan, 'UTF-8') || mb_strlen($alasan, 'UTF-8') > 500) {
        pesan_sekali('Alasan penolakan wajib diisi dan maksimal 500 karakter.');
        redirect_ke($redirect_page);
    }
} else {
    if ($alasan !== '' && (!mb_check_encoding($alasan, 'UTF-8') || mb_strlen($alasan, 'UTF-8') > 500)) {
        pesan_sekali('Catatan persetujuan maksimal 500 karakter.');
        redirect_ke($redirect_page);
    }
    $alasan = $alasan === '' ? null : $alasan;
}

$koneksi = koneksi_database();
$transaksi = false;

try {
    mysqli_begin_transaction($koneksi);
    $transaksi = true;

    $cek = mysqli_prepare($koneksi, "SELECT moderation_status FROM $tabel WHERE id = ? LIMIT 1 FOR UPDATE");
    mysqli_stmt_bind_param($cek, 'i', $id_konten);
    mysqli_stmt_execute($cek);
    mysqli_stmt_bind_result($cek, $status_saat_ini);
    $ditemukan = mysqli_stmt_fetch($cek);
    mysqli_stmt_close($cek);

    if (!$ditemukan) {
        mysqli_rollback($koneksi);
        pesan_sekali('Konten tidak ditemukan.');
        redirect_ke($redirect_page);
    }

    if ($status_saat_ini !== 'pending') {
        mysqli_rollback($koneksi);
        pesan_sekali('Konten ini sudah dimoderasi sebelumnya (' . $status_saat_ini . '). Muat ulang antrean untuk melihat status terbaru.');
        redirect_ke($redirect_page);
    }

    $id_moderator = id_user_login();
    $alasan_tabel = $keputusan === 'rejected' ? $alasan : null;
    $ubah = mysqli_prepare(
        $koneksi,
        "UPDATE $tabel
         SET moderation_status = ?, moderated_at = NOW(), moderated_by = ?, rejection_reason = ?
         WHERE id = ? AND moderation_status = 'pending'"
    );
    mysqli_stmt_bind_param($ubah, 'sisi', $keputusan, $id_moderator, $alasan_tabel, $id_konten);
    mysqli_stmt_execute($ubah);
    $terubah = mysqli_stmt_affected_rows($ubah) === 1;
    mysqli_stmt_close($ubah);

    if (!$terubah) {
        mysqli_rollback($koneksi);
        pesan_sekali('Konten tidak dapat dimoderasi karena statusnya baru saja berubah.');
        redirect_ke($redirect_page);
    }

    $log = mysqli_prepare(
        $koneksi,
        'INSERT INTO moderation_logs (content_type, content_id, action, moderator_id, reason) VALUES (?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($log, 'sisis', $content_type, $id_konten, $keputusan, $id_moderator, $alasan);
    mysqli_stmt_execute($log);
    mysqli_stmt_close($log);

    mysqli_commit($koneksi);
    $transaksi = false;
} catch (Throwable $error) {
    if ($transaksi) {
        mysqli_rollback($koneksi);
    }
    error_log('Moderasi konten gagal.');
    pesan_sekali('Moderasi konten belum dapat diproses. Coba lagi nanti.');
    redirect_ke($redirect_page);
}

$label = $is_pangan ? 'Bahan pangan' : 'Artikel';
pesan_sekali(
    $keputusan === 'approved' ? "$label disetujui dan kini tampil publik." : "$label ditolak dengan alasan tercatat.",
    'success'
);
redirect_ke($redirect_page);
