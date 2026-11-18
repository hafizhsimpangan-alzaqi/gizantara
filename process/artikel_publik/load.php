
<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../common/quill.php';
require_once __DIR__ . '/../common/upload.php';

// Ambil parameter pencarian dari URL
$q = is_string($_GET['q'] ?? null)
    ? trim($_GET['q'])
    : '';

// Ambil parameter kategori dari URL
$kategori = is_string($_GET['kategori'] ?? null)
    ? $_GET['kategori']
    : '';

// Batasi panjang kata kunci pencarian
$q = mb_substr($q, 0, 120, 'UTF-8');

// Validasi kategori
$kategori_valid = [
    '',
    'SDG 2',
    'SDG 3',
    'SDG 2 & 3'
];

if (!in_array($kategori, $kategori_valid, true)) {
    $kategori = '__invalid__';
}

// Siapkan pencarian berdasarkan judul
$cari = '%' . $q . '%';

// Siapkan daftar artikel
$daftar_artikel = [];

try {
    // Koneksi database
    $koneksi = koneksi_database();

    // Query artikel publik
    $sql = "SELECT
                a.id,
                a.title,
                a.category,
                a.cover_image,
                a.body_delta,
                a.created_at,
                u.username AS author
            FROM articles a
            JOIN users u ON u.id = a.user_id
            WHERE a.title LIKE ?
              AND (? = '' OR a.category = ?)
            ORDER BY a.created_at DESC, a.id DESC";

    // Prepared statement
    $cek = mysqli_prepare($koneksi, $sql);

    mysqli_stmt_bind_param(
        $cek,
        'sss',
        $cari,
        $kategori,
        $kategori
    );

    mysqli_stmt_execute($cek);

    // Ambil seluruh artikel
    $hasil = mysqli_stmt_get_result($cek);
    $daftar_artikel = mysqli_fetch_all(
        $hasil,
        MYSQLI_ASSOC
    );

    mysqli_stmt_close($cek);

    // Siapkan ringkasan dan URL gambar
    foreach ($daftar_artikel as &$item) {
        $item['summary'] = ringkasan_html_artikel(
            $item['body_delta']
        );

        $item['cover_url'] = path_sampul_artikel(
            $item['cover_image']
        );
    }

    unset($item);

} catch (Throwable $error) {
    error_log('Daftar artikel publik gagal dimuat.');

    http_response_code(500);
    exit('Artikel belum dapat ditampilkan.');
}
