<?php
function e($nilai) {
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function redirect_ke($halaman) {
    global $baseUrl;
    header('Location: ' . $baseUrl . '/index.php?page=' . urlencode($halaman));
    exit;
}

function pesan_sekali($teks, $jenis = 'error') {
    $_SESSION['pesan_sekali'] = ['teks' => $teks, 'jenis' => $jenis];
}

function ambil_pesan() {
    $pesan = $_SESSION['pesan_sekali'] ?? null;
    unset($_SESSION['pesan_sekali']);
    return $pesan;
}
