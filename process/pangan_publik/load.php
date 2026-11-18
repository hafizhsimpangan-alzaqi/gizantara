
<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../common/upload.php';

// Ambil kata kunci pencarian dari URL
$q = is_string($_GET['q'] ?? null)
    ? trim($_GET['q'])
    : '';

// Batasi panjang kata kunci
$q = mb_substr($q, 0, 120, 'UTF-8');

// Siapkan daftar pangan
$daftar_pangan = [];

try {
    // Siapkan kata kunci SQL LIKE
    $cari = '%' . $q . '%';

    // Koneksi database
    $koneksi = koneksi_database();

    // Query pencarian pangan
    $sql = "SELECT
                id,
                name,
                food_state,
                image,
                energy_kcal,
                description
            FROM foods
            WHERE name LIKE ?
            ORDER BY created_at DESC, id DESC";

    // Prepared statement
    $cek = mysqli_prepare($koneksi, $sql);

    mysqli_stmt_bind_param($cek, 's', $cari);
    mysqli_stmt_execute($cek);

    // Ambil hasil query
    $hasil = mysqli_stmt_get_result($cek);
    $daftar_pangan = mysqli_fetch_all(
        $hasil,
        MYSQLI_ASSOC
    );

    mysqli_stmt_close($cek);

    // Tambahkan URL gambar pada setiap pangan
    foreach ($daftar_pangan as &$item) {
        $item['image_url'] = path_gambar_pangan(
            $item['image']
        );
    }

    unset($item);

} catch (Throwable $error) {
    error_log('Katalog publik gagal dimuat.');

    http_response_code(500);
    exit('Katalog belum dapat ditampilkan.');
}
