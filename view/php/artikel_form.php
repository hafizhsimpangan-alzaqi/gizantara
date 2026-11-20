<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<string, mixed>|null $artikel_form
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <a class="back-link" href="index.php?page=artikel_kelola">← Kembali ke artikel</a>
    <p class="eyebrow">EDITOR ARTIKEL</p>
    <h1><?= $artikel_form ? 'Edit artikel' : 'Tulis artikel' ?></h1>
    <p class="page-lead">Susun artikel pangan dan kesehatan dengan format yang mudah dibaca.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert" data-flash data-kind="<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<form class="form-card form-stack" id="article-form" action="index.php?page=artikel_save" method="post" enctype="multipart/form-data" data-live-upload>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($artikel_form['id'] ?? '') ?>">

    <div class="form-row form-row-title-cat">
        <div class="form-field">
            <label for="article-title">Judul artikel</label>
            <input id="article-title" name="title" maxlength="180" required aria-describedby="article-title-error" value="<?= e($artikel_form['title'] ?? '') ?>">
            <small class="field-error" id="article-title-error" hidden></small>
        </div>

        <div class="form-field">
            <label for="article-category">Kategori SDG</label>
            <select id="article-category" name="category" required aria-describedby="article-category-error">
                <option value="">Pilih kategori</option>
                <?php foreach (['SDG 2', 'SDG 3', 'SDG 2 & 3'] as $pilihan) : ?>
                    <option value="<?= e($pilihan) ?>" <?= ($artikel_form['category'] ?? '') === $pilihan ? 'selected' : '' ?>><?= e($pilihan) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="article-category-error" hidden></small>
        </div>
    </div>

    <div class="form-field">
        <label for="article-cover">Gambar sampul <span class="label-hint">Opsional · JPEG, PNG, atau WebP · maks. 2 MB</span></label>
        <input id="article-cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp" data-image-preview aria-describedby="article-cover-error">
        <small class="field-error" id="article-cover-error" hidden></small>
        <div class="cover-preview" data-preview-wrap <?= empty($artikel_form['cover_url']) ? 'hidden' : '' ?>>
            <img data-preview-image <?= !empty($artikel_form['cover_url']) ? 'src="' . e($artikel_form['cover_url']) . '"' : '' ?> alt="Pratinjau gambar sampul artikel">
            <p data-preview-caption><?= !empty($artikel_form['cover_url']) ? 'Sampul saat ini. Tanpa file baru, sampul ini tetap dipakai.' : '' ?></p>
        </div>
    </div>

    <div class="form-field">
        <p class="field-label" id="article-editor-label">Isi artikel</p>
        <div class="quill-editor" id="article-editor"></div>
        <input type="hidden" id="article-html" name="konten">
        <small class="field-error" id="article-editor-error" hidden></small>
        <p class="form-note">Toolbar mendukung heading, tebal, miring, garis bawah, daftar, dan tautan. Gambar inline tidak didukung.</p>
    </div>

    <div class="form-actions">
        <button class="button button-primary" type="submit"><?= $artikel_form ? 'Simpan perubahan' : 'Simpan artikel' ?></button>
        <a class="button button-secondary" href="index.php?page=artikel_kelola">Batal</a>
    </div>
    <p class="form-status" role="status" aria-live="polite"></p>
</form>

<?php if ($artikel_form) : ?>
    <script type="application/json" id="article-initial-html"><?= json_encode($artikel_form['body_delta'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php endif; ?>
<script src="view/js/vendor/quill.js" defer></script>
<script src="view/js/form.js" defer></script>
<script src="view/js/artikel_editor.js" defer></script>
