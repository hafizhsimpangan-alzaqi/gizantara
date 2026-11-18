<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<int, array<string, mixed>> $artikel_terbaru
 * @var array<int, array<string, mixed>> $pangan_terbaru
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="hero" aria-labelledby="hero-title">
    <div class="hero-copy">
        <p class="eyebrow"><span class="eyebrow-line"></span> Pangan yang baik. Hidup yang sehat.</p>
        <h1 id="hero-title">Kenali pangan.<br><span class="headline-accent">Rawat kesehatan.</span></h1>
        <p class="hero-description">Jelajahi informasi pangan dan kesehatan untuk mendukung pilihan sehari-hari yang lebih sadar.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="index.php?page=artikel">Jelajahi artikel <span aria-hidden="true">↗</span></a>
            <a class="button button-secondary" href="index.php?page=pangan">Lihat katalog pangan</a>
        </div>
        <p class="hero-disclaimer">Informasi edukatif — bukan pengganti saran tenaga kesehatan.</p>
    </div>
    <div class="hero-food">
        <img class="hero-food-img" src="view/uploads/hero-food.webp" alt="Sepiring hidangan gado-gado bernutrisi dengan sayuran segar, telur rebus, tahu, dan saus kacang bergizi seimbang" width="960" height="638" loading="eager" decoding="async">
        <span class="hero-food-caption">Pangan bernutrisi · SDG 2 &amp; SDG 3</span>
    </div>
</section>

<section class="sdg-strip" aria-label="Fokus platform">
    <article class="sdg-card sdg-food">
        <span class="sdg-number">02</span>
        <div><p class="eyebrow">SDG 2</p><h2>Tanpa kelaparan</h2><p>Akses informasi pangan dan komposisi gizi yang bersumber.</p></div>
        <span class="sdg-shape" aria-hidden="true"></span>
    </article>
    <article class="sdg-card sdg-health">
        <span class="sdg-number">03</span>
        <div><p class="eyebrow">SDG 3</p><h2>Kehidupan sehat</h2><p>Artikel kesehatan untuk menambah wawasan dan pemahaman.</p></div>
        <span class="sdg-shape" aria-hidden="true"></span>
    </article>
</section>

<section class="content-section" aria-labelledby="latest-articles">
    <div class="section-heading">
        <div><p class="eyebrow">BACA &amp; PELAJARI</p><h2 id="latest-articles">Artikel terbaru</h2></div>
        <a class="text-link" href="index.php?page=artikel">Semua artikel <span aria-hidden="true">↗</span></a>
    </div>
    <?php if (count($artikel_terbaru) > 0): ?>
        <div class="content-grid article-grid">
            <?php foreach (array_slice($artikel_terbaru, 0, 3) as $item): ?>
                <article class="article-card">
                    <a class="card-image-link" href="index.php?page=artikel_detail&amp;id=<?= e($item['id'] ?? '') ?>">
                        <?php if (!empty($item['cover_url'])): ?><img class="card-image" src="<?= e($item['cover_url']) ?>" alt="Sampul <?= e($item['title'] ?? 'artikel') ?>">
                        <?php else: ?><span class="image-fallback article-fallback" aria-label="Sampul artikel tidak tersedia"><span>ARTIKEL</span><span aria-hidden="true">+</span></span><?php endif; ?>
                    </a>
                    <p class="category-label"><?= e($item['category'] ?? 'Artikel') ?></p>
                    <h3><a href="index.php?page=artikel_detail&amp;id=<?= e($item['id'] ?? '') ?>"><?= e($item['title'] ?? '') ?></a></h3>
                    <p class="card-summary"><?= e($item['summary']) ?></p>
                    <p class="card-meta"><?= e(date('d/m/Y', strtotime($item['created_at']))) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span class="empty-mark" aria-hidden="true"><span></span></span>
            <h3>Belum ada artikel</h3>
            <p>Artikel akan ditampilkan di sini setelah tersedia.</p>
            <a class="text-link" href="index.php?page=artikel">Kunjungi halaman artikel <span aria-hidden="true">→</span></a>
        </div>
    <?php endif; ?>
</section>

<section class="content-section food-section" aria-labelledby="latest-foods">
    <div class="section-heading">
        <div><p class="eyebrow">BAHAN &amp; KOMPOSISI</p><h2 id="latest-foods">Jelajahi bahan pangan</h2></div>
        <a class="text-link" href="index.php?page=pangan">Lihat katalog <span aria-hidden="true">↗</span></a>
    </div>
    <?php if (count($pangan_terbaru) > 0): ?>
        <div class="content-grid food-grid">
            <?php foreach (array_slice($pangan_terbaru, 0, 4) as $item): ?>
                <article class="food-card">
                    <a class="card-image-link" href="index.php?page=pangan_detail&amp;id=<?= e($item['id'] ?? '') ?>">
                        <?php if (!empty($item['image_url'])): ?><img class="card-image" src="<?= e($item['image_url']) ?>" alt="<?= e($item['name'] ?? 'Bahan pangan') ?>">
                        <?php else: ?><span class="image-fallback food-fallback" aria-label="Foto bahan pangan tidak tersedia"><span>FOTO<br>BAHAN</span><span aria-hidden="true">+</span></span><?php endif; ?>
                    </a>
                    <div class="food-card-copy"><h3><a href="index.php?page=pangan_detail&amp;id=<?= e($item['id'] ?? '') ?>"><?= e($item['name'] ?? '') ?></a></h3>
                    <?php if (isset($item['energy_kcal'])): ?><p><?= e($item['energy_kcal']) ?> kkal <span>per 100 g</span></p><?php endif; ?></div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state empty-state-compact">
            <span class="empty-mark empty-mark-green" aria-hidden="true"><span></span></span>
            <h3>Katalog masih kosong</h3>
            <p>Informasi bahan pangan akan muncul di sini saat tersedia.</p>
            <a class="text-link" href="index.php?page=pangan">Jelajahi katalog <span aria-hidden="true">→</span></a>
        </div>
    <?php endif; ?>
</section>
