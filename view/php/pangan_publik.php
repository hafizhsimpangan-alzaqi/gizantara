<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var string $q
 * @var array<int, array<string, mixed>> $daftar_pangan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro" aria-labelledby="page-title">
    <p class="eyebrow"><span class="eyebrow-line"></span> KENALI BAHAN PANGAN</p>
    <h1 id="page-title">Dari bahan,<br><span class="headline-accent">jadi pemahaman.</span></h1>
    <p class="page-lead">Jelajahi informasi komposisi pangan dengan kondisi bahan dan sumber yang jelas.</p>
</section>

<form class="search-panel search-panel-single" method="get" action="index.php">
    <input type="hidden" name="page" value="pangan">
    <div class="search-field">
        <label for="cari-pangan">Cari berdasarkan nama bahan</label>
        <div class="search-control">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
            <input id="cari-pangan" type="search" name="q" value="<?= e($q) ?>" placeholder="Contoh: nama bahan pangan">
            <button class="button button-primary" type="submit">Cari</button>
        </div>
    </div>
</form>
<p class="search-note"><?= count($daftar_pangan) ?> bahan pangan ditemukan.</p>

<?php if (count($daftar_pangan) > 0): ?>
    <div class="content-grid food-grid listing-grid">
        <?php foreach ($daftar_pangan as $item): ?>
            <article class="food-card">
                <a class="card-image-link" href="index.php?page=pangan_detail&amp;id=<?= e($item['id'] ?? '') ?>">
                <?php if (!empty($item['image_url'])): ?><img class="card-image" src="<?= e($item['image_url']) ?>" alt="<?= e($item['name'] ?? 'Bahan pangan') ?>">
                    <?php else: ?><span class="image-fallback food-fallback" aria-label="Foto bahan pangan tidak tersedia"><span>FOTO<br>BAHAN</span><span aria-hidden="true">+</span></span><?php endif; ?>
                </a>
                <div class="food-card-copy"><h2><a href="index.php?page=pangan_detail&amp;id=<?= e($item['id'] ?? '') ?>"><?= e($item['name'] ?? '') ?></a></h2><p class="card-meta"><?= e($item['food_state']) ?></p>
                <?php if (isset($item['energy_kcal'])): ?><p><?= e($item['energy_kcal']) ?> kkal <span>per 100 g</span></p><?php endif; ?></div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state listing-empty">
        <span class="empty-mark empty-mark-green" aria-hidden="true"><span></span></span>
        <h2><?= $q !== '' ? 'Bahan pangan belum ditemukan' : 'Katalog masih kosong' ?></h2>
        <p><?= $q !== '' ? 'Belum ada data yang cocok. Coba nama lain.' : 'Informasi bahan pangan akan muncul di sini setelah data tersedia.' ?></p>
        <?php if ($q !== ''): ?><a class="button button-secondary" href="index.php?page=pangan">Hapus pencarian</a><?php endif; ?>
    </div>
<?php endif; ?>
