<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
$menu_lainnya = [
    ['artikel_kelola', 'Manajemen Artikel', 'Tulis dan kelola artikel edukatif.', 'M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm8 0v5h5M8 12h8M8 16h8'],
    ['pangan_kelola', 'Katalog Pangan', 'Kelola informasi bahan pangan.', 'M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3'],
    ['kalkulator', 'Kalkulator Gizi', 'Lihat estimasi komposisi dari satu bahan.', 'M5 3h14v18H5zM8 7h2m4 0h2M8 11h2m4 0h2M8 15h2m4 0h2'],
    ['monitoring', 'Monitoring Pola Hidup', 'Buka catatan pribadi.', 'M4 19V5m0 14h16M7 15l4-5 3 2 5-7'],
    ['profil', 'Profil Saya', 'Ubah username atau password akun sendiri.', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4 21v-2a8 8 0 0 1 16 0v2'],
];
if (($user_role ?? '') === 'admin') { array_unshift($menu_lainnya, ['users', 'Manajemen User', 'Kelola akun User.', 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m14-10a4 4 0 1 0 0-8m4 18v-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z']); }
?>
<section class="page-intro private-intro"><p class="eyebrow"><span class="eyebrow-line"></span> MENU SEKUNDER</p><h1>Lainnya</h1><p class="page-lead">Masuk sebagai <?= e($_SESSION['username'] ?? '') ?>. Akses menu kerja yang tidak ditampilkan pada navigasi utama mobile.</p></section>
<nav class="secondary-menu" aria-label="Menu lainnya">
    <?php foreach ($menu_lainnya as [$route, $label, $description, $icon]) : ?>
        <a class="secondary-menu-item" href="index.php?page=<?= e($route) ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= e($icon) ?>"/></svg><span><strong><?= e($label) ?></strong><small><?= e($description) ?></small></span><span aria-hidden="true">↗</span></a>
    <?php endforeach; ?>
</nav>
<form class="logout-form" action="index.php?page=logout" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button button-secondary" type="submit">Keluar akun</button></form>
