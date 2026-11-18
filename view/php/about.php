<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<article class="about-page">
    <section class="about-hero" aria-labelledby="about-title">
        <div class="about-hero-copy">
            <p class="eyebrow"><span class="eyebrow-line"></span> TENTANG PLATFORM</p>
            <h1 id="about-title">Mengenal pangan, memahami kesehatan.</h1>
            <p class="hero-description">Platform informasi pangan, gizi, dan kesehatan untuk membantu masyarakat memahami pilihan dan kebiasaan sehari-hari.</p>
        </div>
        <div class="about-art" role="img" aria-label="Aksen geometris untuk SDG 2 dan SDG 3">
            <span class="about-art-label">SDG 02 <span aria-hidden="true">/</span> SDG 03</span>
            <span class="about-art-circle" aria-hidden="true"></span>
            <span class="about-art-square" aria-hidden="true"></span>
            <span class="about-art-line" aria-hidden="true"></span>
        </div>
    </section>

    <section class="about-section about-overview" aria-labelledby="about-overview-title">
        <div>
            <p class="eyebrow">TENTANG PLATFORM</p>
            <h2 id="about-overview-title">Informasi yang dekat dengan keseharian.</h2>
        </div>
        <p>Website ini menyediakan artikel edukatif tentang pangan dan kesehatan serta katalog bahan pangan dengan informasi nilai gizi. Platform juga memiliki fitur pendukung untuk memahami komposisi pangan dan mencatat kebiasaan hidup sehat.</p>
    </section>

    <section class="about-section" aria-labelledby="about-benefits-title">
        <div class="section-heading">
            <div><p class="eyebrow">TUJUAN DAN MANFAAT</p><h2 id="about-benefits-title">Membantu memahami, selangkah demi selangkah.</h2></div>
        </div>
        <ul class="about-benefit-list">
            <li><span>01</span><p>Mempermudah akses informasi pangan dan gizi.</p></li>
            <li><span>02</span><p>Mendukung pemahaman mengenai kebiasaan hidup sehat.</p></li>
            <li><span>03</span><p>Membantu pengguna memahami komposisi bahan pangan.</p></li>
            <li><span>04</span><p>Menyajikan informasi edukatif secara terstruktur.</p></li>
        </ul>
    </section>

    <section class="about-section" aria-labelledby="about-sdgs-title">
        <div class="section-heading">
            <div><p class="eyebrow">KONTRIBUSI TERHADAP SDGs</p><h2 id="about-sdgs-title">Berbagi pengetahuan untuk tujuan bersama.</h2></div>
        </div>
        <div class="about-sdg-grid">
            <article class="about-sdg-card about-sdg-food">
                <span class="about-sdg-number" aria-hidden="true">02</span>
                <div><p class="eyebrow">SDG 2</p><h3>Zero Hunger</h3><p>Informasi bahan pangan dan komposisi gizi dapat mendukung pemahaman tentang pangan yang tersedia dan dikonsumsi.</p></div>
            </article>
            <article class="about-sdg-card about-sdg-health">
                <span class="about-sdg-number" aria-hidden="true">03</span>
                <div><p class="eyebrow">SDG 3</p><h3>Good Health and Well-being</h3><p>Artikel edukatif dan pencatatan kebiasaan sehari-hari berkaitan dengan wawasan kesehatan dan kesejahteraan.</p></div>
            </article>
        </div>
    </section>

    <section class="about-section" aria-labelledby="about-features-title">
        <div class="section-heading">
            <div><p class="eyebrow">FITUR PLATFORM</p><h2 id="about-features-title">Dirancang untuk belajar dan refleksi.</h2></div>
        </div>
        <div class="about-feature-grid">
            <a class="about-feature-card" href="index.php?page=artikel"><span>01</span><h3>Artikel Pangan dan Kesehatan</h3><p>Bacaan edukatif yang dapat dijelajahi publik.</p><span class="feature-link">Jelajahi artikel <span aria-hidden="true">↗</span></span></a>
            <a class="about-feature-card" href="index.php?page=pangan"><span>02</span><h3>Katalog Bahan Pangan</h3><p>Informasi bahan pangan dan komposisi gizinya.</p><span class="feature-link">Buka katalog <span aria-hidden="true">↗</span></span></a>
            <article class="about-feature-card"><span>03</span><h3>Kalkulator Gizi</h3><p>Fitur pendukung untuk memahami komposisi gizi.</p><span class="feature-access">Tersedia setelah masuk</span></article>
            <article class="about-feature-card"><span>04</span><h3>Monitoring Pola Hidup Sehat</h3><p>Fitur untuk membantu mencatat kebiasaan sehari-hari.</p><span class="feature-access">Tersedia setelah masuk</span></article>
        </div>
    </section>

    <aside class="about-disclaimer" aria-label="Informasi penting">
        <span class="about-disclaimer-mark" aria-hidden="true">i</span>
        <p>Informasi pada website bersifat edukatif dan tidak menggantikan diagnosis atau saran dari tenaga kesehatan profesional.</p>
    </aside>
</article>
