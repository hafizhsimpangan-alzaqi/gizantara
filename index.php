<?php
define('APP_ENTRY', true);
require_once __DIR__ . '/process/common/bootstrap.php';

$routes = [
    'home' => ['home.php', 'Beranda'],
    'about' => ['about.php', 'Tentang Kami'],
    'artikel' => ['artikel_publik.php', 'Artikel'],
    'artikel_detail' => ['artikel_detail.php', 'Detail Artikel'],
    'pangan' => ['pangan_publik.php', 'Katalog Pangan'],
    'pangan_detail' => ['pangan_detail.php', 'Detail Pangan'],
    'login' => ['login.php', 'Masuk'],
    'register' => ['register.php', 'Daftar'],
    'dashboard' => ['dashboard.php', 'Dashboard', 'private'],
    'users' => ['users.php', 'Manajemen User', 'admin'],
    'admins' => ['admins.php', 'Manajemen Admin', 'super_admin'],
    'moderasi_artikel' => ['moderasi_artikel.php', 'Moderasi Artikel', 'admin'],
    'moderasi_pangan' => ['moderasi_pangan.php', 'Moderasi Pangan', 'admin'],
    'profil' => ['profil.php', 'Profil Saya', 'private'],
    'artikel_kelola' => ['artikel_kelola.php', 'Manajemen Artikel', 'private'],
    'artikel_form' => ['artikel_form.php', 'Form Artikel', 'private'],
    'pangan_kelola' => ['pangan_kelola.php', 'Manajemen Pangan', 'private'],
    'pangan_form' => ['pangan_form.php', 'Form Pangan', 'private'],
    'kalkulator' => ['kalkulator.php', 'Kalkulator Gizi', 'private'],
    'monitoring' => ['monitoring.php', 'Monitoring', 'private'],
    'monitoring_form' => ['monitoring_form.php', 'Form Monitoring', 'private'],
    'lainnya' => ['lainnya.php', 'Lainnya', 'private'],
];

$page = $_GET['page'] ?? 'home';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $aksi = [
        'login' => 'auth/login.php',
        'register' => 'auth/register.php',
        'logout' => 'auth/logout.php',
        'user_create' => 'auth/create_user.php',
        'user_delete' => 'auth/delete_user.php',
        'user_ban' => 'auth/ban_user.php',
        'user_unban' => 'auth/ban_user.php',
        'admin_create' => 'auth/create_admin.php',
        'admin_delete' => 'auth/delete_admin.php',
        'admin_ban' => 'auth/ban_user.php',
        'admin_unban' => 'auth/ban_user.php',
        'moderasi_artikel_action' => 'moderasi/action.php',
        'moderasi_pangan_action' => 'moderasi/action.php',
        'profil_username' => 'auth/update_profile.php',
        'profil_password' => 'auth/update_profile.php',
        'artikel_save' => 'artikel/save.php',
        'artikel_delete' => 'artikel/delete.php',
        'pangan_save' => 'pangan/save.php',
        'pangan_delete' => 'pangan/delete.php',
        'monitoring_save' => 'monitoring/save.php',
        'monitoring_delete' => 'monitoring/delete.php',
    ];
    if (is_string($page) && isset($aksi[$page])) {
        require __DIR__ . '/process/' . $aksi[$page];
        exit;
    }
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
$artikel_terbaru = [];
$pangan_terbaru = [];
$daftar_artikel = [];
$daftar_pangan = [];
$artikel = null;
$pangan = null;
$q = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$kategori = isset($_GET['kategori']) && is_string($_GET['kategori']) ? $_GET['kategori'] : '';
$pesan_404 = 'Halaman yang kamu cari tidak ditemukan.';

if (!is_string($page) || !isset($routes[$page])) {
    http_response_code(404);
    $page = '404';
    $page_title = 'Halaman tidak ditemukan';
    $template = '404.php';
    $active_page = '';
    $layout_type = 'public';
} else {
    $akses = $routes[$page][2] ?? 'public';
    if ($akses === 'private') wajib_login();
    if ($akses === 'admin') wajib_admin();
    if ($akses === 'super_admin') wajib_super_admin();
    $page_title = $routes[$page][1];
    $template = $routes[$page][0];
    $active_page = in_array($page, ['artikel', 'artikel_detail'], true) ? 'artikel'
        : (in_array($page, ['pangan', 'pangan_detail'], true) ? 'pangan' : $page);
    $layout_type = $akses === 'public'
        ? (in_array($page, ['login', 'register'], true) ? 'login' : 'public')
        : 'private';
    $user_role = role_pengguna();

    if (in_array($page, ['login', 'register'], true) && pengguna_login()) redirect_ke('dashboard');
    if ($akses !== 'public') {
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
    }
    if ($page === 'dashboard') require __DIR__ . '/process/dashboard/load.php';
    if ($page === 'users') require __DIR__ . '/process/auth/load_users.php';
    if ($page === 'admins') require __DIR__ . '/process/auth/load_admins.php';
    if (in_array($page, ['moderasi_artikel', 'moderasi_pangan'], true)) require __DIR__ . '/process/moderasi/load.php';
    if ($page === 'profil') require __DIR__ . '/process/auth/load_profile.php';
    if ($page === 'artikel_kelola') require __DIR__ . '/process/artikel/load.php';
    if ($page === 'artikel_form') require __DIR__ . '/process/artikel/load_form.php';
    if ($page === 'artikel') require __DIR__ . '/process/artikel_publik/load.php';
    if ($page === 'artikel_detail') require __DIR__ . '/process/artikel_publik/load_detail.php';
    if ($page === 'home') require __DIR__ . '/process/home/load.php';
    if ($page === 'pangan_kelola') require __DIR__ . '/process/pangan/load.php';
    if ($page === 'pangan_form') require __DIR__ . '/process/pangan/load_form.php';
    if ($page === 'pangan') require __DIR__ . '/process/pangan_publik/load.php';
    if ($page === 'pangan_detail') require __DIR__ . '/process/pangan_publik/load_detail.php';
    if ($page === 'kalkulator') require __DIR__ . '/process/kalkulator/load.php';
    if ($page === 'monitoring') require __DIR__ . '/process/monitoring/load.php';
    if ($page === 'monitoring_form') require __DIR__ . '/process/monitoring/load_form.php';
}

$pesan = ambil_pesan();
$register_form = ['username' => '', 'email' => ''];
$register_errors = [];
if ($page === 'register') {
    $register_state = $_SESSION['register_state'] ?? [];
    unset($_SESSION['register_state']);
    if (is_array($register_state)) {
        $register_form = is_array($register_state['values'] ?? null)
            ? array_merge($register_form, $register_state['values']) : $register_form;
        $register_errors = is_array($register_state['errors'] ?? null)
            ? $register_state['errors'] : [];
    }
}

require __DIR__ . '/view/php/partials/head.php';
require __DIR__ . '/view/php/partials/header.php';
require __DIR__ . '/view/php/' . $template;
require __DIR__ . '/view/php/partials/footer.php';
