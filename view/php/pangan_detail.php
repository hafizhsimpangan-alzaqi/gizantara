<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<?php if ($pangan): ?>
    <article class="food-detail">
        <a class="back-link" href="index.php?page=pangan"><span aria-hidden="true">←</span> Kembali ke katalog</a>
        <div class="food-detail-layout">
            <div>
                <?php if (!empty($pangan['image_url'])): ?><img class="food-detail-image" src="<?= e($pangan['image_url']) ?>" alt="<?= e($pangan['name'] ?? 'Bahan pangan') ?>">
                <?php else: ?><div class="image-fallback food-fallback food-detail-image" role="img" aria-label="Foto bahan pangan tidak tersedia"><span>FOTO<br>BAHAN</span><span aria-hidden="true">+</span></div><?php endif; ?>
            </div>
            <div class="food-detail-copy">
                <p class="eyebrow"><span class="eyebrow-line"></span> KATALOG BAHAN PANGAN</p>
                <h1><?= e($pangan['name'] ?? '') ?></h1>
                <p class="food-state"><?= e($pangan['food_state'] ?? '') ?></p>
                <p class="nutrition-basis">Nilai gizi per 100 gram bagian yang dapat dimakan</p>
                <dl class="nutrition-grid">
                    <div><dt>Energi</dt><dd><?= e($pangan['energy_kcal'] ?? '') ?> <span>kkal</span></dd></div>
                    <div><dt>Protein</dt><dd><?= e($pangan['protein_g'] ?? '') ?> <span>g</span></dd></div>
                    <div><dt>Lemak</dt><dd><?= e($pangan['fat_g'] ?? '') ?> <span>g</span></dd></div>
                    <div><dt>Karbohidrat</dt><dd><?= e($pangan['carbohydrate_g'] ?? '') ?> <span>g</span></dd></div>
                </dl>
            </div>
        </div>
        <div class="food-detail-more">
            <div><h2>Tentang bahan ini</h2><p class="food-description"><?= e($pangan['description'] ?? '') ?></p></div>
            <p class="source-note"><strong>Sumber informasi gizi</strong><br><?= e($pangan['source_name'] ?? '') ?></p>
        </div>
    </article>
<?php else: ?>
    <section class="empty-state detail-empty" aria-labelledby="detail-empty-title">
        <span class="empty-mark empty-mark-green" aria-hidden="true"><span></span></span>
        <h1 id="detail-empty-title">Bahan pangan belum tersedia</h1>
        <p>Detail dan nilai gizi hanya akan ditampilkan saat data bersumber tersedia.</p>
        <a class="button button-primary" href="index.php?page=pangan">Kembali ke katalog</a>
    </section>
<?php endif; ?>
