
<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/quill.php';
require_once __DIR__ . '/../common/upload.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

wajib_login();
periksa_csrf();

// Validasi ID artikel
$id_artikel = null;

if (($_POST['id'] ?? '') !== '') {
    $id_artikel = filter_var(
        $_POST['id'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if (!$id_artikel) {
        http_response_code(404);
        exit('Artikel tidak ditemukan.');
    }
}

$kembali = $baseUrl . '/index.php?page=artikel_form'
    . ($id_artikel ? '&id=' . $id_artikel : '');

// Ambil input form
$judul = is_string($_POST['title'] ?? null)
    ? trim($_POST['title'])
    : '';

$kategori = $_POST['category'] ?? null;

$konten = sanitasi_html_artikel($_POST['konten'] ?? null);

// Validasi judul
if (
    $judul === '' ||
    !mb_check_encoding($judul, 'UTF-8') ||
    mb_strlen($judul, 'UTF-8') > 180
) {
    pesan_sekali('Judul artikel wajib diisi dan maksimal 180 karakter.');
    header('Location: ' . $kembali);
    exit;
}

// Validasi kategori
if (
    !is_string($kategori) ||
    !in_array($kategori, ['SDG 2', 'SDG 3', 'SDG 2 & 3'], true)
) {
    pesan_sekali('Pilih kategori SDG yang tersedia.');
    header('Location: ' . $kembali);
    exit;
}

// Validasi konten HTML
if ($konten === false) {
    pesan_sekali('Isi artikel wajib berisi teks dan format yang aman.');
    header('Location: ' . $kembali);
    exit;
}

// Inisialisasi sebelum try agar semua variabel selalu terdefinisi
$sampul_baru = null;
$sampul_lama = null;
$koneksi = null;
$transaksi = false;

try {
    $koneksi = koneksi_database();

    mysqli_begin_transaction($koneksi);
    $transaksi = true;

    // Jika mengedit artikel
    if ($id_artikel) {
        $cek = mysqli_prepare(
            $koneksi,
            'SELECT user_id, cover_image, body_delta
             FROM articles
             WHERE id = ?
             FOR UPDATE'
        );

        mysqli_stmt_bind_param($cek, 'i', $id_artikel);
        mysqli_stmt_execute($cek);

        mysqli_stmt_bind_result(
            $cek,
            $pemilik,
            $sampul_lama,
            $konten_lama
        );

        $ditemukan = mysqli_stmt_fetch($cek);
        mysqli_stmt_close($cek);

        if (!$ditemukan) {
            mysqli_rollback($koneksi);
            http_response_code(404);
            exit('Artikel tidak ditemukan.');
        }

        if (!boleh_ubah_konten($pemilik)) {
            mysqli_rollback($koneksi);
            http_response_code(403);
            exit('Akses ditolak.');
        }

        if (artikel_delta_lama($konten_lama)) {
            mysqli_rollback($koneksi);
            http_response_code(409);
            exit('Artikel format lama perlu dikonversi sebelum diedit.');
        }
    }

    // Upload sampul baru jika ada
    $sampul_baru = simpan_sampul_artikel(
        $_FILES['cover'] ?? null
    );

    // Pertahankan sampul lama jika tidak ada upload baru
    $sampul = $sampul_baru ?? $sampul_lama;

    if ($id_artikel) {

        // UPDATE artikel
        if (adalah_admin()) {
            $id_moderator = id_user_login();
            $simpan = mysqli_prepare(
                $koneksi,
                "UPDATE articles
                 SET title = ?,
                     category = ?,
                     body_delta = ?,
                     cover_image = ?,
                     moderation_status = 'approved',
                     moderated_at = NOW(),
                     moderated_by = ?,
                     rejection_reason = NULL
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $simpan,
                'ssssii',
                $judul,
                $kategori,
                $konten,
                $sampul,
                $id_moderator,
                $id_artikel
            );

        } else {
            $id_user = id_user_login();

            $simpan = mysqli_prepare(
                $koneksi,
                "UPDATE articles
                 SET title = ?,
                     category = ?,
                     body_delta = ?,
                     cover_image = ?,
                     moderation_status = 'pending',
                     moderated_at = NULL,
                     moderated_by = NULL,
                     rejection_reason = NULL
                 WHERE id = ? AND user_id = ?"
            );

            mysqli_stmt_bind_param(
                $simpan,
                'ssssii',
                $judul,
                $kategori,
                $konten,
                $sampul,
                $id_artikel,
                $id_user
            );
        }

    } else {

        // INSERT artikel
        $id_user = id_user_login();

        // Kolom body_delta dipertahankan,
        // tetapi nilainya adalah HTML tersanitasi.
        if (adalah_admin()) {
            $simpan = mysqli_prepare(
                $koneksi,
                "INSERT INTO articles
                 (user_id, title, category, body_delta, cover_image, moderation_status, moderated_at, moderated_by)
                 VALUES (?, ?, ?, ?, ?, 'approved', NOW(), ?)"
            );
            mysqli_stmt_bind_param(
                $simpan,
                'issssi',
                $id_user,
                $judul,
                $kategori,
                $konten,
                $sampul,
                $id_user
            );
        } else {
            $simpan = mysqli_prepare(
                $koneksi,
                "INSERT INTO articles
                 (user_id, title, category, body_delta, cover_image, moderation_status)
                 VALUES (?, ?, ?, ?, ?, 'pending')"
            );
            mysqli_stmt_bind_param(
                $simpan,
                'issss',
                $id_user,
                $judul,
                $kategori,
                $konten,
                $sampul
            );
        }
    }

    // Jalankan query
    mysqli_stmt_execute($simpan);
    mysqli_stmt_close($simpan);

    // Simpan transaksi
    mysqli_commit($koneksi);
    $transaksi = false;

} catch (Throwable $error) {

    // Rollback hanya jika transaksi dan koneksi tersedia
    if ($transaksi && $koneksi instanceof mysqli) {
        try {
            mysqli_rollback($koneksi);
        } catch (Throwable $rollback_error) {
            error_log('Rollback penyimpanan artikel gagal.');
        }
    }

    // Hapus sampul baru jika penyimpanan gagal
    if (
        $sampul_baru !== null &&
        path_sampul_artikel($sampul_baru)
    ) {
        $lokasi_sampul = dirname(__DIR__, 2) . '/' . $sampul_baru;
        if (is_file($lokasi_sampul)) {
            unlink($lokasi_sampul);
        }
    }

    if (
        $error instanceof RuntimeException &&
        !($error instanceof mysqli_sql_exception)
    ) {
        pesan_sekali($error->getMessage());
    } else {
        error_log('Penyimpanan artikel gagal.');
        pesan_sekali(
            'Artikel belum dapat disimpan. Coba lagi nanti.'
        );
    }

    header('Location: ' . $kembali);
    exit;
}

// Hapus sampul lama setelah UPDATE berhasil
if ($sampul_baru !== null && $sampul_lama !== null) {
    try {
        hapus_sampul_artikel($sampul_lama, $koneksi);
    } catch (Throwable $error) {
        error_log('Sampul lama belum dapat dibersihkan.');
    }
}

// Pesan sukses
pesan_sekali(
    $id_artikel
        ? 'Artikel berhasil diperbarui.'
        : 'Artikel berhasil disimpan.',
    'success'
);

redirect_ke('artikel_kelola');