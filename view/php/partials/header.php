<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<div class="app-shell layout-<?= e($layout_type) ?>">
    <?php if ($layout_type === 'private') { require __DIR__ . '/sidebar.php'; } ?>
    <div class="page-column">
        <header class="site-header">
            <div class="header-identity">
                <a class="wordmark" href="index.php?page=home" aria-label="Beranda — Platform Informasi Pangan, Gizi, dan Kesehatan">
                    <span class="wordmark-mark" aria-hidden="true"></span>
                    <span>Informasi Pangan<br><span class="wordmark-light">&amp; Kesehatan</span></span>
                </a>
                <span class="header-context">SDG 2 <span aria-hidden="true">/</span> SDG 3</span>
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
                <a class="header-login" href="index.php?page=home">Lihat situs <span aria-hidden="true">↗</span></a>
            <?php else : ?>
                <a class="header-login" href="index.php?page=home">Kembali ke beranda <span aria-hidden="true">↗</span></a>
            <?php endif; ?>
        </header>
        <main id="main-content" class="page-content">
