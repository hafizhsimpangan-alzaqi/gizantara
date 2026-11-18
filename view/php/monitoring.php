<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<int, array<string, mixed>> $daftar_catatan
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}

$total_catatan = count($daftar_catatan);
$catatan_terbaru = $daftar_catatan[0] ?? null;
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> CATATAN PRIBADI</p>
    <h1>Monitoring Pola Hidup</h1>
    <p class="page-lead">Catat kebiasaan harian untuk refleksi pribadi. Informasi ini disajikan apa adanya dan bukan diagnosis atau penilaian kesehatan.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<section class="monitoring-workspace" aria-label="Ringkasan pribadi dan riwayat monitoring">
    <section class="card monitoring-summary-panel" aria-labelledby="monitor-private-summary">
        <p class="eyebrow">RINGKASAN PRIBADI</p>
        <h2 id="monitor-private-summary">Catatan akun ini</h2>
        <div class="monitoring-stat-box">
            <span class="monitoring-stat-value"><?= e(number_format($total_catatan, 0, ',', '.')) ?></span>
            <span class="monitoring-stat-label">catatan harian tersimpan</span>
        </div>

        <?php if ($catatan_terbaru) : ?>
            <div class="monitoring-latest-box">
                <p class="monitoring-latest-date">
                    Terakhir dicatat: <time datetime="<?= e($catatan_terbaru['log_date']) ?>"><strong><?= e(date('d/m/Y', strtotime($catatan_terbaru['log_date']))) ?></strong></time>
                </p>
                <dl class="monitoring-summary-list">
                    <div>
                        <dt>Air minum</dt>
                        <dd><?= e($catatan_terbaru['water_glasses']) ?> gelas</dd>
                    </div>
                    <div>
                        <dt>Durasi tidur</dt>
                        <dd><?= e($catatan_terbaru['sleep_hours']) ?> jam</dd>
                    </div>
                    <div>
                        <dt>Aktivitas fisik</dt>
                        <dd><?= e($catatan_terbaru['activity_minutes']) ?> menit</dd>
                    </div>
                </dl>
                <a class="text-link" href="index.php?page=monitoring_form&amp;id=<?= e($catatan_terbaru['id']) ?>">Edit catatan terakhir <span aria-hidden="true">↗</span></a>
            </div>
        <?php else : ?>
            <p class="form-note">Belum ada rekaman kebiasaan pada akun ini.</p>
        <?php endif; ?>

        <p class="source-note">Catatan bersifat pribadi dan tidak dapat diakses oleh akun lain termasuk Admin.</p>
        <a class="button button-primary monitoring-cta-btn" href="index.php?page=monitoring_form">Tambah catatan</a>
    </section>

    <section class="monitoring-history-panel" aria-labelledby="monitor-history-title">
        <div class="private-toolbar monitoring-history-toolbar">
            <div>
                <h2 id="monitor-history-title">Riwayat catatan</h2>
                <p>Satu catatan per tanggal milik akun yang sedang login.</p>
            </div>
            <a class="button button-secondary" href="index.php?page=monitoring_form">Catat baru</a>
        </div>

        <?php if (!$daftar_catatan) : ?>
            <div class="empty-state">
                <span class="empty-mark empty-mark-green" aria-hidden="true"></span>
                <h3>Belum ada catatan pola hidup</h3>
                <p>Tambahkan catatan pertama untuk mulai menyimpan riwayat pribadi konsumsi air, tidur, dan aktivitas fisik.</p>
                <a class="button button-primary" href="index.php?page=monitoring_form">Tambah catatan pertama</a>
            </div>
        <?php else : ?>
            <div class="user-table-wrap">
                <table class="user-table monitoring-table">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Air minum</th>
                            <th scope="col">Durasi tidur</th>
                            <th scope="col">Aktivitas fisik</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftar_catatan as $catatan) : ?>
                            <tr>
                                <td>
                                    <time datetime="<?= e($catatan['log_date']) ?>"><strong><?= e(date('d/m/Y', strtotime($catatan['log_date']))) ?></strong></time>
                                </td>
                                <td><?= e($catatan['water_glasses']) ?> gelas</td>
                                <td><?= e($catatan['sleep_hours']) ?> jam</td>
                                <td><?= e($catatan['activity_minutes']) ?> menit</td>
                                <td>
                                    <div class="record-actions">
                                        <a class="button button-secondary" href="index.php?page=monitoring_form&amp;id=<?= e($catatan['id']) ?>">Edit</a>
                                        <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="monitor-delete-dialog" data-delete-id="<?= e($catatan['id']) ?>" data-delete-name="catatan tanggal <?= e(date('d/m/Y', strtotime($catatan['log_date']))) ?>">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="user-card-list monitoring-list">
                <?php foreach ($daftar_catatan as $catatan) : ?>
                    <article class="monitoring-card">
                        <div class="monitoring-card-heading">
                            <h3><time datetime="<?= e($catatan['log_date']) ?>"><?= e(date('d/m/Y', strtotime($catatan['log_date']))) ?></time></h3>
                            <div class="record-actions">
                                <a class="button button-secondary" href="index.php?page=monitoring_form&amp;id=<?= e($catatan['id']) ?>">Edit</a>
                                <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="monitor-delete-dialog" data-delete-id="<?= e($catatan['id']) ?>" data-delete-name="catatan tanggal <?= e(date('d/m/Y', strtotime($catatan['log_date']))) ?>">Hapus</button>
                            </div>
                        </div>
                        <dl class="monitoring-values">
                            <div>
                                <dt>Air minum</dt>
                                <dd><?= e($catatan['water_glasses']) ?> gelas</dd>
                            </div>
                            <div>
                                <dt>Durasi tidur</dt>
                                <dd><?= e($catatan['sleep_hours']) ?> jam</dd>
                            </div>
                            <div>
                                <dt>Aktivitas fisik</dt>
                                <dd><?= e($catatan['activity_minutes']) ?> menit</dd>
                            </div>
                        </dl>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>

<dialog class="confirm-dialog" id="monitor-delete-dialog" aria-labelledby="monitor-delete-title">
    <h2 id="monitor-delete-title">Hapus catatan?</h2>
    <p><strong data-delete-name></strong> akan dihapus dan tidak dapat dipulihkan.</p>
    <form action="index.php?page=monitoring_delete" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <input type="hidden" name="confirm_delete" value="1">
        <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
        <button class="button button-danger" type="submit">Ya, Hapus Catatan</button>
    </form>
</dialog>
<script src="view/js/form.js" defer></script>
