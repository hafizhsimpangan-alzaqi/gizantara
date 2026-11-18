<?php
function koneksi_database() {
    global $env, $koneksi_database;

    if (isset($koneksi_database) && $koneksi_database instanceof mysqli) {
        return $koneksi_database;
    }

    $port = filter_var($env['DB_PORT'] ?? null, FILTER_VALIDATE_INT);
    if (empty($env['DB_HOST']) || empty($env['DB_NAME']) || empty($env['DB_USER'])
        || !array_key_exists('DB_PASS', $env) || !$port || $port > 65535) {
        http_response_code(500);
        echo 'Konfigurasi database belum lengkap.';
        exit(1);
    }

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $koneksi_database = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'] ?? '', $env['DB_NAME'], $port);
        if (!$koneksi_database || !mysqli_set_charset($koneksi_database, 'utf8mb4')) {
            throw new RuntimeException('Koneksi atau charset gagal.');
        }
        return $koneksi_database;
    } catch (Throwable $error) {
        error_log('Koneksi database gagal.');
        http_response_code(500);
        echo 'Koneksi database gagal.';
        exit(1);
    }
}

function tutup_database() {
    global $koneksi_database;
    if (isset($koneksi_database) && $koneksi_database instanceof mysqli) {
        mysqli_close($koneksi_database);
        $koneksi_database = null;
    }
}
