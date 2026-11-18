<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
        </main>
        <?php if ($layout_type === 'public') : ?>
            <footer class="site-footer">
                <div class="footer-grid">
                    <section class="footer-section footer-brand" aria-labelledby="footer-brand-title">
                        <a class="wordmark" href="index.php?page=home" aria-label="Beranda — Informasi Pangan dan Kesehatan">
                            <span class="wordmark-mark" aria-hidden="true"></span>
                            <span id="footer-brand-title">Informasi Pangan<br><span class="wordmark-light">&amp; Kesehatan</span></span>
                        </a>
                        <p>Ruang edukasi untuk mengenal pangan, gizi, dan kesehatan sehari-hari.</p>
                    </section>
                    <nav class="footer-section footer-links" aria-label="Navigasi footer">
                        <h2>Navigasi</h2>
                        <a href="index.php?page=home">Beranda</a>
                        <a href="index.php?page=artikel">Artikel</a>
                        <a href="index.php?page=pangan">Pangan</a>
                        <a href="index.php?page=about">Tentang Kami</a>
                    </nav>
                    <section class="footer-section footer-info" aria-labelledby="footer-info-title">
                        <h2 id="footer-info-title">Fokus informasi</h2>
                        <p><strong>SDG 2</strong> — Zero Hunger</p>
                        <p><strong>SDG 3</strong> — Good Health and Well-being</p>
                        <p>Konten bersifat edukatif, bukan pengganti diagnosis atau saran tenaga kesehatan profesional.</p>
                    </section>
                </div>
                <div class="footer-bottom">
                    <p>&copy; <?= date('Y') ?> Informasi Pangan &amp; Kesehatan</p>
                </div>
            </footer>
        <?php endif; ?>
    </div>
    <?php if ($layout_type !== 'login') { require __DIR__ . '/bottom_nav.php'; } ?>
</div>
</body>
</html>
