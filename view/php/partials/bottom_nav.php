<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
if ($layout_type === 'private' || pengguna_login()) :
    $active_nav = $layout_type === 'private' && $active_page !== 'dashboard' ? 'lainnya' : $active_page;
    $menu_mobile = [
        ['home', 'Beranda', 'm3 10.5 9-7 9 7.5V21h-6v-6H9v6H3z'],
        ['dashboard', 'Dashboard', 'M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM13 13h8v8h-8z'],
        ['artikel', 'Artikel', 'M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm8 0v5h5M8 12h8M8 16h8'],
        ['pangan', 'Pangan', 'M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3'],
        ['lainnya', 'Lainnya', 'M5 12h.01M12 12h.01M19 12h.01'],
    ];
?>
    <nav class="bottom-nav" aria-label="Navigasi area pengguna">
        <?php foreach ($menu_mobile as [$route, $label, $path]) : ?>
            <a href="index.php?page=<?= e($route) ?>" aria-label="<?= e($label) ?>" <?= $active_nav === $route ? 'aria-current="page"' : '' ?> class="<?= $active_nav === $route ? 'is-active' : '' ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= e($path) ?>"/></svg>
            </a>
        <?php endforeach; ?>
    </nav>
<?php else : ?>
    <nav class="bottom-nav" aria-label="Navigasi utama">
        <a href="index.php?page=home" aria-label="Beranda" <?= $active_page === 'home' ? 'aria-current="page"' : '' ?> class="<?= $active_page === 'home' ? 'is-active' : '' ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg></a>
        <a href="index.php?page=artikel" aria-label="Artikel" <?= $active_page === 'artikel' ? 'aria-current="page"' : '' ?> class="<?= $active_page === 'artikel' ? 'is-active' : '' ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v5h5M8 12h8M8 16h8"/></svg></a>
        <a href="index.php?page=pangan" aria-label="Katalog pangan" <?= $active_page === 'pangan' ? 'aria-current="page"' : '' ?> class="<?= $active_page === 'pangan' ? 'is-active' : '' ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3M9 12v5m6-5v5"/></svg></a>
        <a href="index.php?page=about" aria-label="Tentang Kami" <?= $active_page === 'about' ? 'aria-current="page"' : '' ?> class="<?= $active_page === 'about' ? 'is-active' : '' ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg></a>
        <a href="index.php?page=login" aria-label="Masuk" <?= $active_page === 'login' ? 'aria-current="page"' : '' ?> class="<?= $active_page === 'login' ? 'is-active' : '' ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 21v-2a7 7 0 0 1 14 0v2z"/></svg></a>
    </nav>
<?php endif; ?>
