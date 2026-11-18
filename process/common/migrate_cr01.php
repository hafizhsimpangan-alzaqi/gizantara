<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Akses ditolak.\n");
}

require_once __DIR__ . '/bootstrap.php';
$backup = $argv[1] ?? '/tmp/gizantara_cr01_backup_20261118.sql';
if (!is_file($backup) || (fileperms($backup) & 0777) !== 0600
    || strpos((string) file_get_contents($backup), 'CREATE TABLE `users`') === false) {
    exit("Backup CR-01 tidak tersedia atau tidak valid; migrasi dibatalkan.\n");
}

$koneksi = koneksi_database();
if (($env['DB_NAME'] ?? '') !== 'sdgs_web') {
    exit("Database bukan sdgs_web; migrasi dibatalkan.\n");
}

$kolom = mysqli_query($koneksi, "SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email'");
$email_ada = mysqli_num_rows($kolom) > 0;
if ($email_ada) {
    $definisi = mysqli_fetch_assoc($kolom);
    if ($definisi['DATA_TYPE'] !== 'varchar' || (int) $definisi['CHARACTER_MAXIMUM_LENGTH'] !== 254 || $definisi['IS_NULLABLE'] !== 'YES') {
        exit("Definisi kolom email berbeda dari CR-01; migrasi dibatalkan.\n");
    }
} else {
    mysqli_query($koneksi, 'ALTER TABLE users ADD COLUMN email VARCHAR(254) NULL AFTER username');
}

$index = mysqli_query($koneksi, "SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND NON_UNIQUE = 0 GROUP BY INDEX_NAME HAVING COUNT(*) = 1 AND MAX(COLUMN_NAME) = 'email'");
$index_unik = mysqli_num_rows($index) > 0;
if (!$index_unik) {
    $duplikat = mysqli_query($koneksi, 'SELECT email FROM users WHERE email IS NOT NULL GROUP BY email HAVING COUNT(*) > 1 LIMIT 1');
    if (mysqli_num_rows($duplikat) > 0) {
        exit("Email duplikat ditemukan; index unik tidak dibuat. Migrasi perlu ditinjau.\n");
    }
    mysqli_query($koneksi, 'ALTER TABLE users ADD UNIQUE KEY uq_users_email (email)');
}

$jumlah = mysqli_fetch_assoc(mysqli_query($koneksi, 'SELECT COUNT(*) AS jumlah FROM users'))['jumlah'];
echo 'Migrasi CR-01 selesai/tersedia; akun dipertahankan: ' . $jumlah . ".\n";
