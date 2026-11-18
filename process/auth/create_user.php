<?php
require_once __DIR__ . '/../common/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
wajib_admin();
periksa_csrf();

$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$konfirmasi = $_POST['confirm_password'] ?? null;
if (!is_string($username) || trim($username) === '' || !mb_check_encoding($username, 'UTF-8') || mb_strlen(trim($username), 'UTF-8') > 50 || strpos($username, '@') !== false) {
    pesan_sekali('Username wajib diisi, maksimal 50 karakter, dan tidak boleh memuat @.');
    redirect_ke('users');
}
if (!is_string($email) || strlen(trim($email)) > 254 || filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL) === false) {
    pesan_sekali('Email wajib diisi dengan format yang valid.');
    redirect_ke('users');
}
if (!is_string($password) || strlen($password) < 8 || strlen($password) > 4096) {
    pesan_sekali('Password awal minimal 8 karakter.');
    redirect_ke('users');
}
if (!is_string($konfirmasi) || $password !== $konfirmasi) {
    pesan_sekali('Konfirmasi password belum sama.');
    redirect_ke('users');
}
$username = trim($username);
$email = strtolower(trim($email));

try {
    $koneksi = koneksi_database();
    $periksa = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    mysqli_stmt_bind_param($periksa, 'ss', $username, $email);
    mysqli_stmt_execute($periksa);
    mysqli_stmt_store_result($periksa);
    $duplikat = mysqli_stmt_num_rows($periksa) > 0;
    mysqli_stmt_close($periksa);
    if ($duplikat) {
        pesan_sekali('Username atau email sudah digunakan.');
        redirect_ke('users');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $simpan = mysqli_prepare($koneksi, "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')");
    mysqli_stmt_bind_param($simpan, 'sss', $username, $email, $hash);
    mysqli_stmt_execute($simpan);
    mysqli_stmt_close($simpan);
} catch (mysqli_sql_exception $error) {
    if ($error->getCode() === 1062) {
        pesan_sekali('Username atau email sudah digunakan.');
    } else {
        error_log('Pembuatan User gagal.');
        pesan_sekali('Akun User belum dapat dibuat. Coba lagi nanti.');
    }
    redirect_ke('users');
} catch (Throwable $error) {
    error_log('Pembuatan User gagal.');
    pesan_sekali('Akun User belum dapat dibuat. Coba lagi nanti.');
    redirect_ke('users');
}

pesan_sekali('Akun User berhasil dibuat.', 'success');
redirect_ke('users');
