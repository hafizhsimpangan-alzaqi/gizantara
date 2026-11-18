<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
wajib_login();
periksa_csrf();

$is_username = $page === 'profil_username';
$form = $is_username ? 'username' : 'password';

function profil_gagal($form, $field, $message, $username = '') {
    $_SESSION['profile_state'] = [
        'form' => $form,
        'errors' => [$field => $message],
        'username' => $form === 'username' ? $username : ''
    ];
    redirect_ke('profil');
}

$current_password = $_POST['current_password'] ?? null;
if (!is_string($current_password) || $current_password === '' || strlen($current_password) > 4096) {
    profil_gagal($form, 'current_password', 'Password saat ini wajib diisi.');
}

if ($is_username) {
    $new_username = $_POST['username'] ?? null;
    if (!is_string($new_username) || trim($new_username) === '' || !mb_check_encoding($new_username, 'UTF-8')
        || mb_strlen(trim($new_username), 'UTF-8') > 50 || strpos($new_username, '@') !== false) {
        profil_gagal($form, 'username', 'Username wajib diisi, maksimal 50 karakter, dan tidak boleh memuat @.');
    }
    $new_username = trim($new_username);
} else {
    $new_password = $_POST['new_password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;
    if (!is_string($new_password) || strlen($new_password) < 8 || strlen($new_password) > 4096) {
        profil_gagal($form, 'new_password', 'Password baru minimal 8 karakter.');
    }
    if (!is_string($confirm_password) || $new_password !== $confirm_password) {
        profil_gagal($form, 'confirm_password', 'Konfirmasi password belum sama.');
    }
}

try {
    $koneksi = koneksi_database();
    $id_user = id_user_login();
    $periksa = mysqli_prepare($koneksi, 'SELECT username, password_hash FROM users WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($periksa, 'i', $id_user);
    mysqli_stmt_execute($periksa);
    mysqli_stmt_bind_result($periksa, $username_lama, $hash_lama);
    $ditemukan = mysqli_stmt_fetch($periksa);
    mysqli_stmt_close($periksa);
    if (!$ditemukan || !is_string($hash_lama)) {
        $_SESSION = [];
        redirect_ke('login');
    }
    if (!password_verify($current_password, $hash_lama)) {
        profil_gagal($form, 'current_password', 'Password saat ini tidak sesuai.', $new_username ?? '');
    }

    if ($is_username) {
        if ($new_username === $username_lama) {
            pesan_sekali('Username tetap sama.', 'success');
            redirect_ke('profil');
        }
        $cek_nama = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($cek_nama, 'si', $new_username, $id_user);
        mysqli_stmt_execute($cek_nama);
        mysqli_stmt_store_result($cek_nama);
        $duplikat = mysqli_stmt_num_rows($cek_nama) > 0;
        mysqli_stmt_close($cek_nama);
        if ($duplikat) profil_gagal($form, 'username', 'Username sudah digunakan.', $new_username);
        $ubah = mysqli_prepare($koneksi, 'UPDATE users SET username = ? WHERE id = ?');
        mysqli_stmt_bind_param($ubah, 'si', $new_username, $id_user);
        mysqli_stmt_execute($ubah);
        $berubah = mysqli_stmt_affected_rows($ubah) === 1;
        mysqli_stmt_close($ubah);
        if (!$berubah) throw new RuntimeException('Akun tidak berubah.');
        $_SESSION['username'] = $new_username;
        pesan_sekali('Username berhasil diperbarui.', 'success');
        redirect_ke('profil');
    }

    if (password_verify($new_password, $hash_lama)) {
        profil_gagal($form, 'new_password', 'Password baru harus berbeda dari password saat ini.');
    }
    $hash_baru = password_hash($new_password, PASSWORD_DEFAULT);
    $ubah = mysqli_prepare($koneksi, 'UPDATE users SET password_hash = ? WHERE id = ?');
    mysqli_stmt_bind_param($ubah, 'si', $hash_baru, $id_user);
    mysqli_stmt_execute($ubah);
    $berubah = mysqli_stmt_affected_rows($ubah) === 1;
    mysqli_stmt_close($ubah);
    if (!$berubah) throw new RuntimeException('Akun tidak berubah.');
    $_SESSION = [];
    session_regenerate_id(true);
    pesan_sekali('Password berhasil diubah. Silakan masuk kembali.', 'success');
    redirect_ke('login');
} catch (mysqli_sql_exception $error) {
    if ($error->getCode() === 1062 && $is_username) {
        profil_gagal($form, 'username', 'Username sudah digunakan.', $new_username);
    }
    error_log('Perubahan Profil gagal.');
    pesan_sekali('Profil belum dapat diperbarui. Coba lagi nanti.');
    redirect_ke('profil');
} catch (Throwable $error) {
    error_log('Perubahan Profil gagal.');
    pesan_sekali('Profil belum dapat diperbarui. Coba lagi nanti.');
    redirect_ke('profil');
}
