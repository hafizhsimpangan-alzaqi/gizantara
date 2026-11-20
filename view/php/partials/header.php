<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<div class="app-shell layout-<?= e($layout_type) ?>">
    <?php if ($layout_type === 'private') { require __DIR__ . '/sidebar.php'; } ?>
    <?php if ($layout_type === 'private') : ?><button class="sidebar-backdrop" type="button" data-sidebar-close aria-label="Tutup menu"></button><?php endif; ?>
    <div class="page-column">
        <header class="site-header">
            <?php if ($layout_type === 'private') : ?><button class="sidebar-toggle" type="button" aria-label="Buka atau tutup menu" aria-controls="private-sidebar" aria-expanded="true" data-sidebar-toggle><span aria-hidden="true">☰</span></button><?php endif; ?>
            <div class="header-identity">
                <a class="wordmark" href="index.php?page=home" aria-label="Beranda — Gizantara, Gizi dan Kesehatan untuk Nusantara">
                    <span class="wordmark-mark" aria-hidden="true"></span>
                    <span>Gizantara<br><span class="wordmark-light">Gizi &amp; Kesehatan untuk Nusantara</span></span>
                </a>
            </div>
            <?php if ($layout_type === 'public') : ?>
                <nav class="public-nav" aria-label="Navigasi utama">
                    <a href="index.php?page=home" <?= $active_page === 'home' ? 'aria-current="page"' : '' ?>>Beranda</a>
                    <a href="index.php?page=artikel" <?= $active_page === 'artikel' ? 'aria-current="page"' : '' ?>>Artikel</a>
                    <a href="index.php?page=pangan" <?= $active_page === 'pangan' ? 'aria-current="page"' : '' ?>>Pangan</a>
                    <a href="index.php?page=about" <?= $active_page === 'about' ? 'aria-current="page"' : '' ?>>Tentang Kami</a>
                </nav>
                <div class="header-actions">
                    <?php if (pengguna_login()) : ?>
                        <a class="header-login" href="index.php?page=dashboard">Dashboard</a>
                    <?php else : ?>
                        <a class="header-login" href="index.php?page=login">Masuk</a>
                        <a class="button button-primary header-register" href="index.php?page=register">Daftar</a>
                    <?php endif; ?>
                </div>
            <?php elseif ($layout_type === 'private') : ?>
                <span class="private-page-label"><?= e($page_title) ?></span>
                <a class="header-login" href="index.php?page=profil" aria-label="Profil Saya, <?= e($_SESSION['username'] ?? '') ?>"><?= e($_SESSION['username'] ?? '') ?></a>
            <?php else : ?>
                <a class="header-login" href="index.php?page=home">Kembali ke beranda <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
        </header>
        <main id="main-content" class="page-content">
