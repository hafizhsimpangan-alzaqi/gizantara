
<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var string $q
 * @var string $kategori
 * @var array<int, array<string, mixed>> $daftar_artikel
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}

?>

<section class="page-intro" aria-labelledby="page-title">
    <p class="eyebrow">
        <span class="eyebrow-line"></span>
        WAWASAN &amp; EDUKASI
    </p>

    <h1 id="page-title">
        Artikel <span class="headline-accent">kesehatan</span>
        <br>&amp; gizi.
    </h1>

    <p class="page-lead">
        Bacaan seputar pangan, gizi, dan kesehatan untuk memperluas wawasan.
    </p>
</section>

<form class="search-panel" method="get" action="index.php">
    <input type="hidden" name="page" value="artikel">

    <div class="search-field">
        <label for="cari-artikel">
            Cari artikel berdasarkan judul
        </label>

        <div class="search-control">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="10.8" cy="10.8" r="6.8"/>
                <path d="m16 16 5 5"/>
            </svg>

            <input
                id="cari-artikel"
                type="search"
                name="q"
                value="<?= e($q) ?>"
                placeholder="Contoh: pangan beragam"
            >

            <button class="button button-primary" type="submit">
                Cari
            </button>
        </div>
    </div>

    <div class="filter-field">
        <label for="filter-kategori">Kategori</label>

        <select id="filter-kategori" name="kategori">
            <option value="">Semua kategori</option>

            <option value="SDG 2"
                <?= $kategori === 'SDG 2' ? 'selected' : '' ?>>
                SDG 2
            </option>

            <option value="SDG 3"
                <?= $kategori === 'SDG 3' ? 'selected' : '' ?>>
                SDG 3
            </option>

            <option value="SDG 2 &amp; 3"
                <?= $kategori === 'SDG 2 & 3' ? 'selected' : '' ?>>
                SDG 2 &amp; 3
            </option>
        </select>
    </div>
</form>

<p class="search-note">
    <?= count($daftar_artikel) ?> artikel ditemukan.
</p>

<?php if (count($daftar_artikel) > 0): ?>

    <div class="content-grid article-grid listing-grid">

        <?php foreach ($daftar_artikel as $item): ?>

            <article class="article-card">

                <a
                    class="card-image-link"
                    href="index.php?page=artikel_detail&amp;id=<?= e($item['id'] ?? '') ?>"
                >
                    <?php if (!empty($item['cover_url'])): ?>

                        <img
                            class="card-image"
                            src="<?= e($item['cover_url']) ?>"
                            alt="Sampul <?= e($item['title'] ?? 'artikel') ?>"
                        >

                    <?php else: ?>

                        <span
                            class="image-fallback article-fallback"
                            aria-label="Sampul artikel tidak tersedia"
                        >
                            <span>ARTIKEL</span>
                            <span aria-hidden="true">+</span>
                        </span>

                    <?php endif; ?>
                </a>

                <p class="category-label">
                    <?= e($item['category'] ?? 'Artikel') ?>
                </p>

                <h2>
                    <a href="index.php?page=artikel_detail&amp;id=<?= e($item['id'] ?? '') ?>">
                        <?= e($item['title'] ?? '') ?>
                    </a>
                </h2>

                <p class="card-summary">
                    <?= e($item['summary'] ?? '') ?>
                </p>

                <p class="card-meta">
                    <?= e($item['author'] ?? '') ?>
                    ·
                    <?= e(date('d/m/Y', strtotime($item['created_at']))) ?>
                </p>

            </article>

        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="empty-state listing-empty">
        <span class="empty-mark" aria-hidden="true">
            <span></span>
        </span>

        <h2>
            <?= $q !== '' || $kategori !== ''
                ? 'Belum ada hasil pencarian'
                : 'Belum ada artikel tersedia' ?>
        </h2>

        <p>
            <?= $q !== '' || $kategori !== ''
                ? 'Coba ubah kata kunci atau kategori.'
                : 'Artikel akan muncul di halaman ini setelah disimpan.' ?>
        </p>

        <?php if ($q !== '' || $kategori !== ''): ?>
            <a
                class="button button-secondary"
                href="index.php?page=artikel"
            >
                Hapus pencarian
            </a>
        <?php endif; ?>

    </div>

<?php endif; ?>
