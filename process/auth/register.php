<?php
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/login_rate_limit.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
periksa_csrf();
if (pengguna_login()) {
    http_response_code(403);
    exit('Akses ditolak.');
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$konfirmasi = $_POST['confirm_password'] ?? '';
$username = is_string($username) ? trim($username) : '';
$email = is_string($email) ? strtolower(trim($email)) : '';
$password = is_string($password) ? $password : '';
$konfirmasi = is_string($konfirmasi) ? $konfirmasi : '';
$ip_client = $_SERVER['REMOTE_ADDR'] ?? '';
$rate_id = 'register:' . substr($username !== '' ? $username : $email, 0, 254);
$batas = login_rate_limit_begin($rate_id, $ip_client);
if ($batas === false) {
    error_log('Penyimpanan rate limit Registrasi tidak tersedia.');
    http_response_code(503);
    exit('Pendaftaran sedang tidak tersedia. Coba lagi nanti.');
}
if (!$batas['allowed']) {
    http_response_code(429);
    header('Retry-After: ' . $batas['retry_after']);
    exit('Terlalu banyak percobaan pendaftaran. Coba lagi beberapa menit.');
}
$token_rate_limit = $batas['token'];

$errors = [];
if ($username === '' || !mb_check_encoding($username, 'UTF-8') || mb_strlen($username, 'UTF-8') > 50 || strpos($username, '@') !== false) {
    $errors['username'] = 'Username wajib diisi, maksimal 50 karakter, dan tidak boleh memuat @.';
}
if ($email === '' || strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errors['email'] = 'Masukkan alamat email yang valid (maksimal 254 karakter).';
}
if (strlen($password) < 8 || strlen($password) > 4096) {
    $errors['password'] = 'Password minimal 8 karakter.';
}
if ($konfirmasi === '' || $password !== $konfirmasi) {
    $errors['confirm_password'] = 'Konfirmasi password belum sama.';
}

$koneksi = null;
if (!$errors) {
    try {
        $koneksi = koneksi_database();
        $periksa = mysqli_prepare($koneksi, 'SELECT username, email FROM users WHERE username = ? OR email = ? LIMIT 2');
        mysqli_stmt_bind_param($periksa, 'ss', $username, $email);
        mysqli_stmt_execute($periksa);
        mysqli_stmt_bind_result($periksa, $username_db, $email_db);
        while (mysqli_stmt_fetch($periksa)) {
            if (strcasecmp((string) $username_db, $username) === 0) {
                $errors['username'] = 'Username sudah digunakan.';
            }
            if (is_string($email_db) && strcasecmp($email_db, $email) === 0) {
                $errors['email'] = 'Email sudah digunakan.';
            }
        }
        mysqli_stmt_close($periksa);
    } catch (Throwable $error) {
        if (!login_rate_limit_finish($rate_id, $ip_client, $token_rate_limit, 'neutral')) {
            http_response_code(503);
            exit('Pendaftaran sedang tidak tersedia. Coba lagi nanti.');
        }
        error_log('Pemeriksaan keunikan Registrasi gagal.');
        pesan_sekali('Pendaftaran belum dapat diproses. Coba lagi nanti.');
        redirect_ke('register');
    }
}

if ($errors) {
    if (!login_rate_limit_finish($rate_id, $ip_client, $token_rate_limit, 'failed')) {
        http_response_code(503);
        exit('Pendaftaran sedang tidak tersedia. Coba lagi nanti.');
    }
    $_SESSION['register_state'] = [
        'values' => ['username' => $username, 'email' => $email],
        'errors' => $errors,
    ];
    pesan_sekali('Periksa kembali data yang ditandai.');
    redirect_ke('register');
}

try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $simpan = mysqli_prepare($koneksi, "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')");
    mysqli_stmt_bind_param($simpan, 'sss', $username, $email, $hash);
    mysqli_stmt_execute($simpan);
    mysqli_stmt_close($simpan);
} catch (mysqli_sql_exception $error) {
    if (!login_rate_limit_finish($rate_id, $ip_client, $token_rate_limit, 'failed')) {
        http_response_code(503);
        exit('Pendaftaran sedang tidak tersedia. Coba lagi nanti.');
    }
    if ($error->getCode() === 1062) {
        $_SESSION['register_state'] = [
            'values' => ['username' => $username, 'email' => $email],
            'errors' => ['email' => 'Username atau email sudah digunakan.'],
        ];
        pesan_sekali('Username atau email sudah digunakan.');
    } else {
        error_log('Pembuatan akun Registrasi gagal.');
        pesan_sekali('Pendaftaran belum dapat diproses. Coba lagi nanti.');
    }
    redirect_ke('register');
} catch (Throwable $error) {
    if (!login_rate_limit_finish($rate_id, $ip_client, $token_rate_limit, 'neutral')) {
        http_response_code(503);
        exit('Pendaftaran sedang tidak tersedia. Coba lagi nanti.');
    }
    error_log('Pembuatan akun Registrasi gagal.');
    pesan_sekali('Pendaftaran belum dapat diproses. Coba lagi nanti.');
    redirect_ke('register');
}

if (!login_rate_limit_finish($rate_id, $ip_client, $token_rate_limit, 'success')) {
    error_log('Reset rate limit Registrasi setelah berhasil gagal.');
}
pesan_sekali('Pendaftaran berhasil. Silakan masuk menggunakan username atau email.', 'success');
redirect_ke('login');
