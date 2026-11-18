<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<string, mixed>|null $pangan_form
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <a class="back-link" href="index.php?page=pangan_kelola">← Kembali ke katalog</a>
    <p class="eyebrow">FORM BAHAN PANGAN</p>
    <h1><?= $pangan_form ? 'Edit' : 'Tambah' ?> bahan pangan</h1>
    <p class="page-lead">Semua nilai gizi dicatat per 100 gram bagian yang dapat dimakan.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<form class="form-card form-stack" action="index.php?page=pangan_save" method="post" enctype="multipart/form-data" data-live-upload>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($pangan_form['id'] ?? '') ?>">

    <div class="form-row">
        <div class="form-field">
            <label for="food-name">Nama bahan pangan</label>
            <input id="food-name" name="name" maxlength="120" required aria-describedby="food-name-error" value="<?= e($pangan_form['name'] ?? '') ?>">
            <small class="field-error" id="food-name-error" hidden></small>
        </div>

        <div class="form-field">
            <label for="food-condition">Kondisi bahan</label>
            <input id="food-condition" name="condition" maxlength="80" placeholder="Contoh: mentah, direbus, tanpa kulit" required aria-describedby="food-condition-error" value="<?= e($pangan_form['food_state'] ?? '') ?>">
            <small class="field-error" id="food-condition-error" hidden></small>
        </div>
    </div>

    <div class="form-field">
        <label for="food-description">Deskripsi</label>
        <textarea id="food-description" name="description" rows="4" required aria-describedby="food-description-error"><?= e($pangan_form['description'] ?? '') ?></textarea>
        <small class="field-error" id="food-description-error" hidden></small>
    </div>

    <div class="form-field">
        <label for="food-image">Gambar bahan <span class="label-hint">Opsional · JPEG, PNG, atau WebP · maks. 2 MB</span></label>
        <input id="food-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-preview aria-describedby="food-image-error">
        <small class="field-error" id="food-image-error" hidden></small>
        <div class="cover-preview" data-preview-wrap <?= empty($pangan_form['image_url']) ? 'hidden' : '' ?>>
            <img data-preview-image src="<?= e($pangan_form['image_url'] ?? '') ?>" alt="Pratinjau gambar bahan pangan">
            <p data-preview-caption><?= !empty($pangan_form['image_url']) ? 'Gambar saat ini; tetap dipakai jika tidak ada file baru.' : '' ?></p>
        </div>
    </div>

    <fieldset class="nutrition-fields">
        <legend>Informasi gizi per 100 gram bagian dapat dimakan</legend>
        <div class="field-grid nutrition-input-grid">
            <?php foreach (['energy_kcal' => 'Energi (kkal)', 'protein_g' => 'Protein (g)', 'fat_g' => 'Lemak (g)', 'carbohydrate_g' => 'Karbohidrat (g)'] as $kolom => $label) : ?>
                <div class="form-field">
                    <label for="<?= e($kolom) ?>"><?= e($label) ?></label>
                    <input id="<?= e($kolom) ?>" name="<?= e($kolom) ?>" type="number" min="0" max="99999999.99" step="0.01" required aria-describedby="<?= e($kolom) ?>-error" value="<?= e($pangan_form[$kolom] ?? '') ?>">
                    <small class="field-error" id="<?= e($kolom) ?>-error" hidden></small>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <div class="form-field">
        <label for="food-source">Sumber informasi gizi</label>
        <input id="food-source" name="source" maxlength="255" placeholder="Contoh: Tabel Komposisi Pangan Indonesia (TKPI)" required aria-describedby="food-source-error" value="<?= e($pangan_form['source_name'] ?? '') ?>">
        <small class="field-error" id="food-source-error" hidden></small>
    </div>

    <div class="form-actions">
        <button class="button button-primary" type="submit"><?= $pangan_form ? 'Simpan perubahan' : 'Simpan bahan' ?></button>
        <a class="button button-secondary" href="index.php?page=pangan_kelola">Batal</a>
    </div>
    <p class="form-status" role="status" aria-live="polite"></p>
</form>

<script src="view/js/form.js" defer></script>
