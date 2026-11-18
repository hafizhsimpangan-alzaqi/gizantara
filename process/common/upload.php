<?php
function path_gambar($nilai, $jenis) {
    if (!in_array($jenis, ['artikel', 'pangan'], true) || !is_string($nilai)
        || !preg_match('#^view/uploads/' . $jenis . '/[a-f0-9]{32}\.(jpg|png|webp)$#', $nilai)) return null;
    return is_file(dirname(__DIR__, 2) . '/' . $nilai) ? $nilai : null;
}

function simpan_gambar($file, $jenis) {
    if ($file === null || ($file['error'] ?? null) === UPLOAD_ERR_NO_FILE) return null;
    if (!in_array($jenis, ['artikel', 'pangan'], true) || !is_array($file)
        || ($file['error'] ?? null) !== UPLOAD_ERR_OK || !isset($file['size'], $file['tmp_name'])
        || $file['size'] < 1 || $file['size'] > 2 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Gambar gagal diunggah atau melebihi 2 MB.');
    }
    $format = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $gambar = getimagesize($file['tmp_name']);
    if (!isset($format[$mime]) || $gambar === false || ($gambar['mime'] ?? '') !== $mime) {
        throw new RuntimeException('Gambar harus berupa JPEG, PNG, atau WebP yang valid.');
    }
    $folder = dirname(__DIR__, 2) . '/view/uploads/' . $jenis;
    if (!is_dir($folder) || !is_writable($folder)) throw new RuntimeException('Folder gambar belum siap digunakan.');
    $path = 'view/uploads/' . $jenis . '/' . bin2hex(random_bytes(16)) . '.' . $format[$mime];
    if (!move_uploaded_file($file['tmp_name'], dirname(__DIR__, 2) . '/' . $path)) {
        throw new RuntimeException('Gambar belum dapat disimpan.');
    }
    return $path;
}

function hapus_gambar($path, $koneksi, $jenis) {
    if (!path_gambar($path, $jenis)) return;
    $sql = $jenis === 'artikel' ? 'SELECT COUNT(*) FROM articles WHERE cover_image=?' : 'SELECT COUNT(*) FROM foods WHERE image=?';
    $cek = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($cek, 's', $path);
    mysqli_stmt_execute($cek);
    mysqli_stmt_bind_result($cek, $jumlah);
    mysqli_stmt_fetch($cek);
    mysqli_stmt_close($cek);
    if ((int) $jumlah === 0) unlink(dirname(__DIR__, 2) . '/' . $path);
}

function path_sampul_artikel($nilai) { return path_gambar($nilai, 'artikel'); }
function simpan_sampul_artikel($file) { return simpan_gambar($file, 'artikel'); }
function hapus_sampul_artikel($path, $koneksi) { hapus_gambar($path, $koneksi, 'artikel'); }
function path_gambar_pangan($nilai) { return path_gambar($nilai, 'pangan'); }
function simpan_gambar_pangan($file) { return simpan_gambar($file, 'pangan'); }
function hapus_gambar_pangan($path, $koneksi) { hapus_gambar($path, $koneksi, 'pangan'); }
