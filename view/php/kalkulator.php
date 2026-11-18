<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<int, array<string, mixed>> $opsi_pangan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> ESTIMASI KOMPOSISI</p>
    <h1>Kalkulator Gizi</h1>
    <p class="page-lead">Hitung estimasi empat zat gizi dari satu bahan pangan dan berat yang dimasukkan.</p>
</section>

<?php if (!$opsi_pangan) : ?>
    <div class="empty-state">
        <span class="empty-mark" aria-hidden="true"></span>
        <h2>Belum ada bahan pangan</h2>
        <p>Kalkulator tersedia setelah katalog bahan pangan memiliki minimal satu data bersumber.</p>
        <a class="button button-primary" href="index.php?page=pangan_form">Tambah bahan pangan</a>
    </div>
<?php else : ?>
    <section class="calculator-layout" aria-label="Area kerja kalkulator gizi">
        <form class="form-card form-stack" id="calculator-form" novalidate>
            <div class="form-field">
                <label for="calculator-food">Bahan pangan</label>
                <select id="calculator-food" required aria-describedby="calculator-food-error">
                    <option value="">Pilih bahan pangan</option>
                    <?php foreach ($opsi_pangan as $item) : ?>
                        <option value="<?= e($item['id']) ?>" data-energy="<?= e($item['energy_kcal']) ?>" data-protein="<?= e($item['protein_g']) ?>" data-fat="<?= e($item['fat_g']) ?>" data-carbohydrate="<?= e($item['carbohydrate_g']) ?>" data-source="<?= e($item['source_name']) ?>"><?= e($item['name']) ?> · <?= e($item['food_state']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-error" id="calculator-food-error" hidden></small>
                <p class="form-note">Data gizi per 100 g berasal dari katalog bahan pangan.</p>
            </div>

            <div class="form-field">
                <label for="calculator-weight">Berat bahan <span class="label-hint">gram (0,01 – 10.000 g)</span></label>
                <input id="calculator-weight" type="number" min="0.01" max="10000" step="any" placeholder="Contoh: 150" required aria-describedby="calculator-weight-error">
                <small class="field-error" id="calculator-weight-error" hidden></small>
            </div>

            <div class="form-actions">
                <button class="button button-primary" type="submit">Hitung estimasi</button>
                <button class="button button-secondary" type="reset">Reset</button>
            </div>
            <p class="form-status" id="calculator-status" role="status" aria-live="polite"></p>
        </form>

        <section class="calculator-result" aria-labelledby="calculator-result-title" aria-live="polite">
            <p class="eyebrow">HASIL PERHITUNGAN</p>
            <h2 id="calculator-result-title">Pilih bahan dan berat</h2>
            <dl class="nutrition-grid">
                <div>
                    <dt>Energi</dt>
                    <dd><span data-result="energy">—</span> <span>kkal</span></dd>
                </div>
                <div>
                    <dt>Protein</dt>
                    <dd><span data-result="protein">—</span> <span>g</span></dd>
                </div>
                <div>
                    <dt>Lemak</dt>
                    <dd><span data-result="fat">—</span> <span>g</span></dd>
                </div>
                <div>
                    <dt>Karbohidrat</dt>
                    <dd><span data-result="carbohydrate">—</span> <span>g</span></dd>
                </div>
            </dl>
            <p class="source-note" id="calculator-source">Sumber data akan ditampilkan setelah perhitungan.</p>
            <p class="source-note">Rumus: (berat ÷ 100) × nilai per 100 g. Hasil bersifat estimasi dan bukan diagnosis atau saran medis.</p>
        </section>
    </section>
<?php endif; ?>

<script src="view/js/kalkulator.js" defer></script>
