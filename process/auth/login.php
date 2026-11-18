
<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/login_rate_limit.php';

// Hanya menerima POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

periksa_csrf();

// Ambil input login
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

// Validasi input kosong
if (
    !is_string($username) ||
    !is_string($password) ||
    trim($username) === '' ||
    $password === ''
) {
    pesan_sekali('Username atau email dan password wajib diisi.');
    redirect_ke('login');
}

$username = trim($username);

// Validasi panjang input
if (strlen($username) > 254 || strlen($password) > 4096) {
    pesan_sekali('Username atau password salah.');
    redirect_ke('login');
}

// Pemeriksaan rate limiting
$ip_client = $_SERVER['REMOTE_ADDR'] ?? '';

$batas_login = login_rate_limit_begin($username, $ip_client);

if ($batas_login === false) {
    error_log('Penyimpanan rate limit Login tidak tersedia.');
    http_response_code(503);
    exit('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
}

if (!$batas_login['allowed']) {
    if ($batas_login['reason'] === 'ip') {
        http_response_code(429);
        header('Retry-After: ' . $batas_login['retry_after']);
        exit('Terlalu banyak percobaan masuk. Coba lagi beberapa menit.');
    }
    pesan_sekali('Username atau password salah.');
    redirect_ke('login');
}

$token_rate_limit = $batas_login['token'];

// Inisialisasi variabel hasil query
$id_user = null;
$hash = null;
$role = null;
$username_akun = null;
$ditemukan = false;
$rate_limit_account_id = null;

try {
    $koneksi = koneksi_database();

    // Identifier bisa cocok ke username maupun email; dua akun yang cocok ditolak.
    $periksa = mysqli_prepare(
        $koneksi,
        'SELECT id, password_hash, role, username
         FROM users
         WHERE username = ? OR email = ?
         LIMIT 2'
    );

    mysqli_stmt_bind_param($periksa, 'ss', $username, $username);
    mysqli_stmt_execute($periksa);
    mysqli_stmt_store_result($periksa);
    if (mysqli_stmt_num_rows($periksa) === 1) {
        mysqli_stmt_bind_result($periksa, $id_user, $hash, $role, $username_akun);
        $ditemukan = mysqli_stmt_fetch($periksa);
    }

    mysqli_stmt_close($periksa);

} catch (Throwable $error) {

    // Batalkan reservasi rate limit jika query gagal
    if (!login_rate_limit_finish(
        $username,
        $ip_client,
        $token_rate_limit,
        'neutral'
    )) {
        error_log('Pelepasan reservasi rate limit Login gagal.');
        http_response_code(503);
        exit('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
    }

    error_log('Pemeriksaan login gagal.');

    pesan_sekali('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
    redirect_ke('login');
}

if ($ditemukan) {
    $rate_akun = login_rate_limit_rekey($username, $ip_client, $token_rate_limit, (int) $id_user);
    if ($rate_akun === false) {
        error_log('Pembaruan bucket rate limit Login gagal.');
        http_response_code(503);
        exit('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
    }
    if (!$rate_akun['allowed']) {
        pesan_sekali('Username atau password salah.');
        redirect_ke('login');
    }
    $rate_limit_account_id = (int) $id_user;
}

// Verifikasi username dan password
$login_valid = false;

if ($ditemukan && is_string($hash)) {
    $login_valid = password_verify($password, $hash);
}

// Jika login gagal
if (!$login_valid) {

    if (!login_rate_limit_finish(
        $username,
        $ip_client,
        $token_rate_limit,
        'failed',
        null,
        $rate_limit_account_id
    )) {
        error_log('Pencatatan gagal Login tidak tersedia.');
        http_response_code(503);
        exit('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
    }

    pesan_sekali('Username atau password salah.');
    redirect_ke('login');
}

// Jika login berhasil
if (!login_rate_limit_finish(
    $username,
    $ip_client,
    $token_rate_limit,
    'success',
    null,
    $rate_limit_account_id
)) {
    error_log('Reset penghitung Login berhasil gagal.');
    http_response_code(503);
    exit('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
}

// Validasi identitas dan role dari database
if (
    !is_numeric($id_user) ||
    !in_array($role, ['admin', 'user'], true)
) {
    error_log('Data identitas atau role pengguna tidak valid.');
    http_response_code(503);
    exit('Layanan masuk sedang tidak tersedia. Coba lagi nanti.');
}

// Regenerasi session setelah login
session_regenerate_id(true);

// Hapus token CSRF session sebelumnya
unset($_SESSION['csrf_token']);

// Simpan identitas pengguna
$_SESSION['user_id'] = (int) $id_user;
$_SESSION['username'] = $username_akun;
$_SESSION['role'] = $role;

// Arahkan ke Dashboard
redirect_ke('dashboard');
