<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Akses ditolak.');
}

require_once __DIR__ . '/bootstrap.php';

$opts = getopt('', ['username:', 'email:']);
$username = is_string($opts['username'] ?? null) ? trim($opts['username']) : (string) getenv('GIZANTARA_SA_USER');
$email = is_string($opts['email'] ?? null) ? strtolower(trim($opts['email'])) : strtolower(trim((string) getenv('GIZANTARA_SA_EMAIL')));
$password = (string) getenv('GIZANTARA_SA_PASS');

if ($username === '' && defined('STDIN')) {
    fwrite(STDOUT, 'Username Super Admin: ');
    $username = trim((string) fgets(STDIN));
}
if ($email === '' && defined('STDIN')) {
    fwrite(STDOUT, 'Email Super Admin: ');
    $email = strtolower(trim((string) fgets(STDIN)));
}
if ($password === '' && defined('STDIN')) {
    $is_tty = function_exists('stream_isatty') && stream_isatty(STDIN);
    fwrite(STDOUT, 'Password Super Admin (min 8 karakter): ');
    if ($is_tty) {
        system('stty -echo');
    }
    $password = rtrim((string) fgets(STDIN), "\r\n");
    if ($is_tty) {
        system('stty echo');
        fwrite(STDOUT, "\n");
    }
}

if ($username === '' || !mb_check_encoding($username, 'UTF-8') || mb_strlen($username, 'UTF-8') > 50 || strpos($username, '@') !== false) {
    fwrite(STDERR, "Error: Username wajib diisi, maksimal 50 karakter, dan tidak boleh memuat @.\n");
    exit(1);
}
if ($email === '' || strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    fwrite(STDERR, "Error: Email wajib diisi dengan format yang valid.\n");
    exit(1);
}
if ($password === '' || strlen($password) < 8 || strlen($password) > 4096) {
    fwrite(STDERR, "Error: Password wajib minimal 8 karakter.\n");
    exit(1);
}

$koneksi = koneksi_database();
$transaksi = false;

try {
    mysqli_begin_transaction($koneksi);
    $transaksi = true;

    $hitung = mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE role = 'super_admin' FOR UPDATE");
    $jumlah = (int) mysqli_fetch_row($hitung)[0];
    if ($jumlah >= 2) {
        mysqli_rollback($koneksi);
        fwrite(STDERR, "Ditolak: Batas maksimal 2 akun Super Admin resmi sudah tercapai.\n");
        exit(2);
    }

    $cek = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    mysqli_stmt_bind_param($cek, 'ss', $username, $email);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);
    $duplikat = mysqli_stmt_num_rows($cek) > 0;
    mysqli_stmt_close($cek);
    if ($duplikat) {
        mysqli_rollback($koneksi);
        fwrite(STDERR, "Error: Username atau email sudah digunakan.\n");
        exit(1);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $simpan = mysqli_prepare($koneksi, "INSERT INTO users (username, email, password_hash, role, status) VALUES (?, ?, ?, 'super_admin', 'active')");
    mysqli_stmt_bind_param($simpan, 'sss', $username, $email, $hash);
    mysqli_stmt_execute($simpan);
    mysqli_stmt_close($simpan);

    mysqli_commit($koneksi);
    $transaksi = false;
    fwrite(STDOUT, "Akun Super Admin '$username' berhasil dibuat (" . ($jumlah + 1) . "/2).\n");
} catch (Throwable $error) {
    if ($transaksi) {
        mysqli_rollback($koneksi);
    }
    fwrite(STDERR, "Error: Provisioning Super Admin gagal.\n");
    exit(1);
}
