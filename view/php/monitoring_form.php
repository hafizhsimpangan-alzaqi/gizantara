<?php


if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <a class="back-link" href="index.php?page=monitoring">← Kembali ke monitoring</a>
    <p class="eyebrow">CATATAN HARIAN</p>
    <h1><?= $catatan_form ? 'Edit catatan' : 'Catat kebiasaan' ?></h1>
    <p class="page-lead">Catatan bersifat pribadi dan hanya dapat dilihat oleh akun ini.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert" data-flash data-kind="<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<form class="form-card form-stack" action="index.php?page=monitoring_save" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($catatan_form['id'] ?? '') ?>">

    <div class="field-grid monitoring-form-grid">
        <div class="form-field">
            <label for="log-date">Tanggal pencatatan</label>
            <input id="log-date" name="log_date" type="date" required aria-describedby="log-date-error" value="<?= e($catatan_form['log_date'] ?? '') ?>">
            <small class="field-error" id="log-date-error" hidden></small>
        </div>

        <div class="form-field">
            <label for="water-glasses">Air minum <span class="label-hint">jumlah gelas · bilangan bulat</span></label>
            <input id="water-glasses" name="water_glasses" type="number" min="0" max="65535" step="1" inputmode="numeric" placeholder="Contoh: 8" required aria-describedby="water-glasses-error" value="<?= e($catatan_form['water_glasses'] ?? '') ?>">
            <small class="field-error" id="water-glasses-error" hidden></small>
        </div>

        <div class="form-field">
            <label for="sleep-hours">Durasi tidur <span class="label-hint">jam · 0–24</span></label>
            <input id="sleep-hours" name="sleep_hours" type="number" min="0" max="24" step="0.01" placeholder="Contoh: 7.5" required aria-describedby="sleep-hours-error" value="<?= e($catatan_form['sleep_hours'] ?? '') ?>">
            <small class="field-error" id="sleep-hours-error" hidden></small>
        </div>

        <div class="form-field">
            <label for="activity-minutes">Aktivitas fisik <span class="label-hint">menit · 0–1.440</span></label>
            <input id="activity-minutes" name="activity_minutes" type="number" min="0" max="1440" step="1" inputmode="numeric" placeholder="Contoh: 30" required aria-describedby="activity-minutes-error" value="<?= e($catatan_form['activity_minutes'] ?? '') ?>">
            <small class="field-error" id="activity-minutes-error" hidden></small>
        </div>
    </div>

    <p class="form-note">Catatan ini hanya membantu merekam kebiasaan harian (tanpa asumsi volume per gelas). Sistem tidak membuat diagnosis atau kategori sehat/tidak sehat.</p>

    <div class="form-actions">
        <button class="button button-primary" type="submit"><?= $catatan_form ? 'Simpan perubahan' : 'Simpan catatan' ?></button>
        <a class="button button-secondary" href="index.php?page=monitoring">Batal</a>
    </div>
</form>

<script src="view/js/form.js" defer></script>
