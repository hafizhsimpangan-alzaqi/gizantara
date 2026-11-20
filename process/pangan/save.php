
<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/upload.php';

// Hanya menerima POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

wajib_login();
periksa_csrf();

// Validasi ID pangan
$id_pangan = null;

if (($_POST['id'] ?? '') !== '') {
    $id_pangan = filter_var(
        $_POST['id'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if (!$id_pangan) {
        http_response_code(404);
        exit('Bahan pangan tidak ditemukan.');
    }
}

$kembali = $baseUrl . '/index.php?page=pangan_form'
    . ($id_pangan ? '&id=' . $id_pangan : '');

// Ambil input form
$nama = is_string($_POST['name'] ?? null)
    ? trim($_POST['name'])
    : '';

$deskripsi = is_string($_POST['description'] ?? null)
    ? trim($_POST['description'])
    : '';

$kondisi = is_string($_POST['condition'] ?? null)
    ? trim($_POST['condition'])
    : '';

$sumber = is_string($_POST['source'] ?? null)
    ? trim($_POST['source'])
    : '';

// Validasi teks
if (
    $nama === '' ||
    mb_strlen($nama) > 120 ||
    $deskripsi === '' ||
    strlen($deskripsi) > 65535 ||
    $kondisi === '' ||
    mb_strlen($kondisi) > 80 ||
    $sumber === '' ||
    mb_strlen($sumber) > 255 ||
    !mb_check_encoding($nama . $deskripsi . $kondisi . $sumber, 'UTF-8')
) {
    pesan_sekali(
        'Lengkapi nama, deskripsi, kondisi, dan sumber sesuai batas kolom.'
    );

    header('Location: ' . $kembali);
    exit;
}

// Validasi nilai gizi
$gizi = [];

foreach (
    ['energy_kcal', 'protein_g', 'fat_g', 'carbohydrate_g'] as $kolom
) {
    $nilai = $_POST[$kolom] ?? null;

    if (
        !is_string($nilai) ||
        !preg_match(
            '/^(?:0|[1-9][0-9]{0,7})(?:\.[0-9]{1,2})?$/',
            $nilai
        )
    ) {
        pesan_sekali(
            'Nilai gizi harus angka tidak negatif dengan maksimal dua desimal.'
        );

        header('Location: ' . $kembali);
        exit;
    }

    $gizi[$kolom] = $nilai;
}

// Inisialisasi variabel sebelum try
$gambar_baru = null;
$gambar_lama = null;
$koneksi = null;
$transaksi = false;

try {
    $koneksi = koneksi_database();

    mysqli_begin_transaction($koneksi);
    $transaksi = true;

    // Jika sedang mengedit pangan
    if ($id_pangan) {
        $cek = mysqli_prepare(
            $koneksi,
            'SELECT user_id, image
             FROM foods
             WHERE id = ?
             FOR UPDATE'
        );

        mysqli_stmt_bind_param($cek, 'i', $id_pangan);
        mysqli_stmt_execute($cek);

        mysqli_stmt_bind_result(
            $cek,
            $pemilik,
            $gambar_lama
        );

        $ada = mysqli_stmt_fetch($cek);
        mysqli_stmt_close($cek);

        if (!$ada) {
            mysqli_rollback($koneksi);
            http_response_code(404);
            exit('Bahan pangan tidak ditemukan.');
        }

        if (!boleh_ubah_konten($pemilik)) {
            mysqli_rollback($koneksi);
            http_response_code(403);
            exit('Akses ditolak.');
        }
    }

    // Simpan gambar baru jika ada upload
    $gambar_baru = simpan_gambar_pangan(
        $_FILES['image'] ?? null
    );

    // Jika tidak ada gambar baru, gunakan gambar lama
    $gambar = $gambar_baru ?? $gambar_lama;

    // Ambil nilai gizi
    $energi = $gizi['energy_kcal'];
    $protein = $gizi['protein_g'];
    $lemak = $gizi['fat_g'];
    $karbohidrat = $gizi['carbohydrate_g'];

    if ($id_pangan) {

        // UPDATE pangan
        if (adalah_admin()) {
            $id_moderator = id_user_login();
            $simpan = mysqli_prepare(
                $koneksi,
                "UPDATE foods
                 SET name = ?,
                     description = ?,
                     food_state = ?,
                     image = ?,
                     energy_kcal = ?,
                     protein_g = ?,
                     fat_g = ?,
                     carbohydrate_g = ?,
                     source_name = ?,
                     moderation_status = 'approved',
                     moderated_at = NOW(),
                     moderated_by = ?,
                     rejection_reason = NULL
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $simpan,
                'sssssssssii',
                $nama,
                $deskripsi,
                $kondisi,
                $gambar,
                $energi,
                $protein,
                $lemak,
                $karbohidrat,
                $sumber,
                $id_moderator,
                $id_pangan
            );

        } else {
            $id_user = id_user_login();
            $simpan = mysqli_prepare(
                $koneksi,
                "UPDATE foods
                 SET name = ?,
                     description = ?,
                     food_state = ?,
                     image = ?,
                     energy_kcal = ?,
                     protein_g = ?,
                     fat_g = ?,
                     carbohydrate_g = ?,
                     source_name = ?,
                     moderation_status = 'pending',
                     moderated_at = NULL,
                     moderated_by = NULL,
                     rejection_reason = NULL
                 WHERE id = ? AND user_id = ?"
            );

            mysqli_stmt_bind_param(
                $simpan,
                'sssssssssii',
                $nama,
                $deskripsi,
                $kondisi,
                $gambar,
                $energi,
                $protein,
                $lemak,
                $karbohidrat,
                $sumber,
                $id_pangan,
                $id_user
            );
        }

    } else {

        // INSERT pangan baru
        $id_user = id_user_login();

        if (adalah_admin()) {
            $simpan = mysqli_prepare(
                $koneksi,
                "INSERT INTO foods
                 (
                    user_id,
                    name,
                    description,
                    food_state,
                    image,
                    energy_kcal,
                    protein_g,
                    fat_g,
                    carbohydrate_g,
                    source_name,
                    moderation_status,
                    moderated_at,
                    moderated_by
                 )
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', NOW(), ?)"
            );

            mysqli_stmt_bind_param(
                $simpan,
                'isssssssssi',
                $id_user,
                $nama,
                $deskripsi,
                $kondisi,
                $gambar,
                $energi,
                $protein,
                $lemak,
                $karbohidrat,
                $sumber,
                $id_user
            );
        } else {
            $simpan = mysqli_prepare(
                $koneksi,
                "INSERT INTO foods
                 (
                    user_id,
                    name,
                    description,
                    food_state,
                    image,
                    energy_kcal,
                    protein_g,
                    fat_g,
                    carbohydrate_g,
                    source_name,
                    moderation_status
                 )
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
            );

            mysqli_stmt_bind_param(
                $simpan,
                'isssssssss',
                $id_user,
                $nama,
                $deskripsi,
                $kondisi,
                $gambar,
                $energi,
                $protein,
                $lemak,
                $karbohidrat,
                $sumber
            );
        }
    }

    // Jalankan query
    mysqli_stmt_execute($simpan);
    mysqli_stmt_close($simpan);

    // Commit transaksi
    mysqli_commit($koneksi);
    $transaksi = false;

} catch (Throwable $error) {

    // Rollback jika koneksi dan transaksi tersedia
    if ($transaksi && $koneksi instanceof mysqli) {
        try {
            mysqli_rollback($koneksi);
        } catch (Throwable $rollback_error) {
            error_log('Rollback penyimpanan pangan gagal.');
        }
    }

    // Bersihkan gambar baru jika penyimpanan gagal
    if (
        $gambar_baru !== null &&
        path_gambar_pangan($gambar_baru)
    ) {
        $lokasi_gambar = dirname(__DIR__, 2)
            . '/' . $gambar_baru;

        if (is_file($lokasi_gambar)) {
            unlink($lokasi_gambar);
        }
    }

    if (
        $error instanceof RuntimeException &&
        !($error instanceof mysqli_sql_exception)
    ) {
        pesan_sekali($error->getMessage());
    } else {
        error_log('Penyimpanan pangan gagal.');
        pesan_sekali('Bahan pangan belum dapat disimpan.');
    }

    header('Location: ' . $kembali);
    exit;
}

// Hapus gambar lama hanya setelah UPDATE berhasil
if ($gambar_baru !== null && $gambar_lama !== null) {
    try {
        hapus_gambar_pangan($gambar_lama, $koneksi);
    } catch (Throwable $error) {
        error_log('Gambar pangan lama belum dapat dibersihkan.');
    }
}

// Pesan sukses
pesan_sekali(
    $id_pangan
        ? 'Bahan pangan berhasil diperbarui.'
        : 'Bahan pangan berhasil disimpan.',
    'success'
);

redirect_ke('pangan_kelola');