<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<?php if ($artikel): ?>
    <article class="reading-page">
        <a class="back-link" href="index.php?page=artikel"><span aria-hidden="true">←</span> Kembali ke artikel</a>
        <header class="reading-header">
            <p class="category-label"><?= e($artikel['category'] ?? '') ?></p>
            <h1><?= e($artikel['title'] ?? '') ?></h1>
            <p class="reading-meta"><?= e($artikel['author']) ?> · <?= e(date('d/m/Y', strtotime($artikel['created_at']))) ?></p>
        </header>
        <?php if (!empty($artikel['cover_url'])): ?><img class="reading-cover" src="<?= e($artikel['cover_url']) ?>" alt="Sampul <?= e($artikel['title'] ?? 'artikel') ?>">
        <?php else: ?><div class="image-fallback article-fallback reading-fallback" role="img" aria-label="Sampul artikel tidak tersedia"><span>ARTIKEL</span><span aria-hidden="true">+</span></div><?php endif; ?>
        <?php if ($artikel['content_html'] !== false) : ?>
            <?php // HTML dibangun ulang oleh allowlist server sebelum mencapai template. ?>
            <div class="article-body"><?= $artikel['content_html'] ?></div>
        <?php else : ?>
            <p class="form-status is-error" role="status">Isi artikel ini belum dapat ditampilkan dengan aman. Konten format lama perlu ditinjau.</p>
        <?php endif; ?>
    </article>
<?php else: ?>
    <section class="empty-state detail-empty" aria-labelledby="detail-empty-title">
        <span class="empty-mark" aria-hidden="true"><span></span></span>
        <h1 id="detail-empty-title">Artikel belum tersedia</h1>
        <p>Artikel yang diminta tidak tersedia. Silakan kembali ke daftar artikel.</p>
        <a class="button button-primary" href="index.php?page=artikel">Kembali ke artikel</a>
    </section>
<?php endif; ?>
