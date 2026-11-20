<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
$menu_lainnya = [
    ['artikel_kelola', 'Manajemen Artikel', 'Tulis dan kelola artikel edukatif.', 'M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm8 0v5h5M8 12h8M8 16h8'],
    ['pangan_kelola', 'Katalog Pangan', 'Kelola informasi bahan pangan.', 'M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3'],
    ['kalkulator', 'Kalkulator Gizi', 'Lihat estimasi komposisi dari satu bahan.', 'M5 3h14v18H5zM8 7h2m4 0h2M8 11h2m4 0h2M8 15h2m4 0h2'],
    ['monitoring', 'Monitoring Pola Hidup', 'Buka catatan pribadi.', 'M4 19V5m0 14h16M7 15l4-5 3 2 5-7'],
    ['profil', 'Profil Saya', 'Ubah username atau password akun sendiri.', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4 21v-2a8 8 0 0 1 16 0v2'],
];
if (in_array($user_role ?? '', ['admin', 'super_admin'], true)) {
    array_unshift(
        $menu_lainnya,
        ['users', 'Manajemen User', 'Kelola dan blokir akun User.', 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m14-10a4 4 0 1 0 0-8m4 18v-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
        ['moderasi_artikel', 'Moderasi Artikel', 'Tinjau artikel User sebelum terbit.', 'M9 12l2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ['moderasi_pangan', 'Moderasi Pangan', 'Tinjau bahan pangan User sebelum terbit.', 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4']
    );
}
if (($user_role ?? '') === 'super_admin') {
    array_unshift(
        $menu_lainnya,
        ['admins', 'Manajemen Admin', 'Kelola akun Admin platform.', 'M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z']
    );
}
?>
<section class="page-intro private-intro"><p class="eyebrow"><span class="eyebrow-line"></span> MENU SEKUNDER</p><h1>Lainnya</h1><p class="page-lead">Masuk sebagai <?= e($_SESSION['username'] ?? '') ?>. Akses menu kerja yang tidak ditampilkan pada navigasi utama mobile.</p></section>
<nav class="secondary-menu" aria-label="Menu lainnya">
    <?php foreach ($menu_lainnya as [$route, $label, $description, $icon]) : ?>
        <a class="secondary-menu-item" href="index.php?page=<?= e($route) ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= e($icon) ?>"/></svg><span><strong><?= e($label) ?></strong><small><?= e($description) ?></small></span><span aria-hidden="true">↗</span></a>
    <?php endforeach; ?>
</nav>
<form class="logout-form" action="index.php?page=logout" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button button-secondary" type="submit">Keluar akun</button></form>
