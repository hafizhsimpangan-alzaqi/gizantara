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
        <p class="eyebrow">GIZANTARA · GIZI &amp; KESEHATAN UNTUK NUSANTARA</p>
        <h1 id="hero-title">Kenali Pangan, Pahami Gizi, <span class="headline-accent">Bangun Kebiasaan Sehat.</span></h1>
        <p class="hero-description">Gizantara menyatukan bacaan edukatif, informasi gizi bahan pangan, dan alat untuk memahami kebiasaan sehari-hari.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="index.php?page=pangan">Jelajahi katalog pangan <span aria-hidden="true">↗</span></a>
            <a class="button button-secondary" href="index.php?page=about">Kenali tujuan kami</a>
        </div>
        <p class="hero-disclaimer">Informasi edukatif — bukan pengganti saran tenaga kesehatan.</p>
    </div>
    <div class="hero-food">
        <img class="hero-food-img" src="view/uploads/hero-food.webp" alt="Sepiring hidangan gado-gado bernutrisi dengan sayuran segar, telur rebus, tahu, dan saus kacang bergizi seimbang" width="960" height="638" loading="eager" decoding="async">
        <span class="hero-food-caption">Pangan sehari-hari · SDG 2 &amp; SDG 3</span>
    </div>
</section>

<section class="sdg-strip" aria-label="Fokus platform">
    <article class="sdg-card sdg-food">
        <span class="sdg-number">02</span>
        <div><p class="eyebrow">SDG 2</p><h2>Tanpa kelaparan</h2><p>Tujuan global ini mencakup ketahanan pangan dan perbaikan gizi. Di sini, Anda dapat mengenal bahan pangan serta komposisi gizinya.</p><a class="sdg-source" href="https://sdgs.un.org/goals/goal2" rel="noopener noreferrer">Sumber: PBB ↗</a></div>
        <span class="sdg-shape" aria-hidden="true"></span>
    </article>
    <article class="sdg-card sdg-health">
        <span class="sdg-number">03</span>
        <div><p class="eyebrow">SDG 3</p><h2>Kehidupan sehat dan sejahtera</h2><p>Tujuan global ini mendorong kehidupan sehat dan kesejahteraan. Artikel serta catatan kebiasaan membantu Anda belajar dan berefleksi.</p><a class="sdg-source" href="https://sdgs.un.org/goals/goal3" rel="noopener noreferrer">Sumber: PBB ↗</a></div>
        <span class="sdg-shape" aria-hidden="true"></span>
    </article>
</section>

<section class="wellness-connection content-section" aria-labelledby="connection-title">
    <div class="connection-copy"><p class="eyebrow">PANGAN &amp; KESEHATAN</p><h2 id="connection-title">Pengetahuan pangan membantu memahami pilihan sehari-hari.</h2><p>SDG 2 menyoroti akses pangan dan perbaikan gizi; SDG 3 menyoroti kesehatan dan kesejahteraan. Gizantara menghubungkan keduanya melalui informasi bahan pangan, artikel edukatif, dan ruang pencatatan kebiasaan. Platform ini tidak mengukur dampak terhadap target SDG.</p></div>
    <div class="connection-steps" role="group" aria-label="Cara menggunakan Gizantara"><span>01 <strong>Kenali bahan pangan</strong></span><span>02 <strong>Pahami komposisi gizi</strong></span><span>03 <strong>Catat kebiasaan sehat</strong></span></div>
</section>

<section class="content-section" aria-labelledby="features-title">
    <div class="section-heading"><div><p class="eyebrow">FITUR GIZANTARA</p><h2 id="features-title">Temukan yang Anda butuhkan</h2></div></div>
    <div class="feature-grid">
        <a class="feature-card" href="index.php?page=artikel"><span aria-hidden="true">↗</span><h3>Artikel Edukasi</h3><p>Jelajahi bacaan pangan, gizi, dan kesehatan.</p></a>
        <a class="feature-card" href="index.php?page=pangan"><span aria-hidden="true">◯</span><h3>Katalog Pangan</h3><p>Lihat komposisi gizi bahan pangan per 100 gram.</p></a>
        <a class="feature-card" href="index.php?page=<?= pengguna_login() ? 'kalkulator' : 'login' ?>"><span aria-hidden="true">＋</span><h3>Kalkulator Gizi</h3><p>Perkirakan kandungan gizi satu bahan sesuai beratnya. Perlu masuk.</p></a>
        <a class="feature-card" href="index.php?page=<?= pengguna_login() ? 'monitoring' : 'login' ?>"><span aria-hidden="true">✓</span><h3>Monitoring Pola Hidup</h3><p>Catat air minum, tidur, dan aktivitas fisik pribadi. Perlu masuk.</p></a>
    </div>
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
<section class="home-cta content-section" aria-labelledby="home-cta-title"><div><p class="eyebrow">MULAI MENJELAJAH</p><h2 id="home-cta-title">Informasi yang lebih mudah dipahami, pilihan yang lebih sadar.</h2><p>Mulai dari bacaan singkat atau cari bahan pangan yang ingin Anda kenali.</p></div><div class="hero-actions"><a class="button button-primary" href="index.php?page=artikel">Baca artikel</a><a class="button button-secondary" href="index.php?page=pangan">Buka katalog</a></div></section>
