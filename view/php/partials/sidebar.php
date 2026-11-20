<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
$active_nav = in_array($active_page, ['artikel_form'], true) ? 'artikel_kelola'
    : (in_array($active_page, ['pangan_form'], true) ? 'pangan_kelola'
    : (in_array($active_page, ['monitoring_form'], true) ? 'monitoring' : $active_page));
$menu_private = [
    ['dashboard', 'Dashboard', 'M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM13 13h8v8h-8z'],
    ['artikel_kelola', 'Manajemen Artikel', 'M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm8 0v5h5M8 12h8M8 16h8'],
    ['pangan_kelola', 'Katalog Pangan', 'M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3'],
    ['kalkulator', 'Kalkulator Gizi', 'M5 3h14v18H5zM8 7h2m4 0h2M8 11h2m4 0h2M8 15h2m4 0h2'],
    ['monitoring', 'Monitoring', 'M4 19V5m0 14h16M7 15l4-5 3 2 5-7'],
    ['profil', 'Profil Saya', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4 21v-2a8 8 0 0 1 16 0v2'],
    ['lainnya', 'Lainnya', 'M5 12h.01M12 12h.01M19 12h.01'],
];
if (in_array($user_role ?? '', ['admin', 'super_admin'], true)) {
    $menu_admin = [
        ['users', 'Manajemen User', 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m14-10a4 4 0 1 0 0-8m4 18v-2a4 4 0 0 0-3-3.87M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
        ['moderasi_artikel', 'Moderasi Artikel', 'M9 12l2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ['moderasi_pangan', 'Moderasi Pangan', 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4'],
    ];
    if (($user_role ?? '') === 'super_admin') {
        array_unshift($menu_admin, ['admins', 'Manajemen Admin', 'M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z']);
    }
    array_splice($menu_private, 2, 0, $menu_admin);
}
?>
<aside class="sidebar" id="private-sidebar" aria-label="Navigasi area pengguna">
    <a class="sidebar-brand" href="index.php?page=dashboard" aria-label="Dashboard">
        <span class="brand-square" aria-hidden="true">G</span>
        <span class="sidebar-brand-name">Gizantara<small>Gizi &amp; Kesehatan untuk Nusantara</small></span>
    </a>
    <button class="sidebar-close" type="button" data-sidebar-close aria-label="Tutup menu">&times;</button>
    <nav class="sidebar-nav" aria-label="Menu privat">
        <?php foreach ($menu_private as [$route, $label, $path]) : ?>
            <a class="nav-link <?= $active_nav === $route ? 'is-active' : '' ?>" href="index.php?page=<?= e($route) ?>" aria-label="<?= e($label) ?>" <?= $active_nav === $route ? 'aria-current="page"' : '' ?>>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= e($path) ?>"/></svg><span><?= e($label) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <p class="sidebar-note">Masuk sebagai <?= e($_SESSION['username'] ?? '') ?>.</p>
    <form class="logout-form" action="index.php?page=logout" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="nav-link" type="submit" aria-label="Keluar akun"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4M15 16l4-4-4-4m4 4H9"/></svg><span>Keluar</span></button>
    </form>
</aside>
