<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Akses ditolak.');
}

require_once __DIR__ . '/bootstrap.php';
$koneksi = koneksi_database();
$hasil = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'admin' LIMIT 1");
if (mysqli_num_rows($hasil) > 0) {
    exit("Akun Admin sudah tersedia; tidak dibuat ulang.\n");
}

$username = 'admin';

$periksa = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE username = ? LIMIT 1');
mysqli_stmt_bind_param($periksa, 's', $username);
mysqli_stmt_execute($periksa);
mysqli_stmt_store_result($periksa);
if (mysqli_stmt_num_rows($periksa) > 0) {
    exit("Username sudah digunakan; tidak ada akun yang dibuat.\n");
}

$password = bin2hex(random_bytes(16));
$hash = password_hash($password, PASSWORD_DEFAULT);
$simpan = mysqli_prepare($koneksi, "INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
mysqli_stmt_bind_param($simpan, 'ss', $username, $hash);
mysqli_stmt_execute($simpan);
echo "Akun Admin awal dibuat. Password awal: $password\n";
