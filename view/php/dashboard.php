<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var bool $dashboard_admin
 * @var int $jumlah_artikel
 * @var int $jumlah_pangan
 * @var int $jumlah_user
 * @var int $jumlah_catatan
 * @var array<string, mixed>|null $catatan_terakhir
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}

$dashboard_cards = $dashboard_admin
    ? [
        ['Artikel', $jumlah_artikel, 'Seluruh artikel terdaftar', 'artikel_kelola'],
        ['Bahan pangan', $jumlah_pangan, 'Seluruh bahan pangan terdaftar', 'pangan_kelola'],
        ['User', $jumlah_user, 'Akun User terdaftar', 'users'],
        ['Catatan pribadi', $jumlah_catatan, 'Monitoring milik akun ini', 'monitoring'],
    ]
    : [
        ['Artikel saya', $jumlah_artikel, 'Artikel milik akun ini', 'artikel_kelola'],
        ['Bahan pangan saya', $jumlah_pangan, 'Bahan milik akun ini', 'pangan_kelola'],
        ['Catatan pribadi', $jumlah_catatan, 'Riwayat monitoring milik akun ini', 'monitoring'],
    ];

$tindakan_cepat = [
    ['artikel_form', 'Tulis Artikel Baru', 'Buat artikel edukasi pangan atau kesehatan.', 'M12 20h9M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z'],
    ['pangan_form', 'Tambah Bahan Pangan', 'Catat komposisi gizi per 100 g bersumber.', 'M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3'],
    ['kalkulator', 'Kalkulator Gizi', 'Hitung estimasi gizi dari satu bahan pangan.', 'M5 3h14v18H5zM8 7h2m4 0h2M8 11h2m4 0h2M8 15h2m4 0h2'],
    ['monitoring_form', 'Catat Pola Hidup', 'Simpan catatan air, tidur, dan aktivitas.', 'M4 19V5m0 14h16M7 15l4-5 3 2 5-7'],
];
if ($dashboard_admin) {
    $tindakan_cepat[] = ['users', 'Kelola Akun User', 'Buat atau kelola akun User.', 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m14-10a4 4 0 1 0 0-8m4 18v-2a4 4 0 0 0-3-3.87M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'];
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> <?= $dashboard_admin ? 'RUANG ADMIN' : 'RUANG USER' ?></p>
    <h1>Dashboard</h1>
    <p class="page-lead">Ringkasan data <?= $dashboard_admin ? 'platform dan catatan pribadi akun ini' : 'milik akun ini' ?>.</p>
</section>

<section class="metric-grid" aria-label="Ringkasan dashboard">
    <?php foreach ($dashboard_cards as [$label, $value, $description, $route]) : ?>
        <a class="metric-card metric-link" href="index.php?page=<?= e($route) ?>">
            <p class="eyebrow"><?= e($label) ?></p>
            <p class="metric-value"><?= e(number_format((int) $value, 0, ',', '.')) ?></p>
            <p><?= e($description) ?></p>
        </a>
    <?php endforeach; ?>
</section>

<section class="dashboard-workspace private-section" aria-label="Monitoring dan tindakan cepat">
    <section class="card dashboard-panel" aria-labelledby="monitor-summary-title">
        <div class="section-heading dashboard-panel-heading">
            <div>
                <p class="eyebrow">CATATAN PRIBADI</p>
                <h2 id="monitor-summary-title">Monitoring pola hidup</h2>
            </div>
            <a class="text-link" href="index.php?page=monitoring">Riwayat <span aria-hidden="true">↗</span></a>
        </div>
        <?php if ($catatan_terakhir) : ?>
            <p class="dashboard-panel-meta">
                Catatan terakhir: <strong><?= e(date('d/m/Y', strtotime($catatan_terakhir['tanggal']))) ?></strong>
            </p>
            <dl class="monitoring-values">
                <div>
                    <dt>Air minum</dt>
                    <dd><?= e($catatan_terakhir['air']) ?> gelas</dd>
                </div>
                <div>
                    <dt>Durasi tidur</dt>
                    <dd><?= e($catatan_terakhir['tidur']) ?> jam</dd>
                </div>
                <div>
                    <dt>Aktivitas fisik</dt>
                    <dd><?= e($catatan_terakhir['aktivitas']) ?> menit</dd>
                </div>
            </dl>
            <p class="form-note dashboard-panel-note">Hanya Anda yang dapat melihat catatan ini. Informasi disajikan apa adanya tanpa penilaian medis.</p>
            <div class="record-actions">
                <a class="button button-primary" href="index.php?page=monitoring_form">Catat hari ini</a>
                <a class="button button-secondary" href="index.php?page=monitoring">Buka monitoring</a>
            </div>
        <?php else : ?>
            <div class="empty-state dashboard-empty">
                <span class="empty-mark empty-mark-green" aria-hidden="true"></span>
                <h3>Belum ada catatan pola hidup</h3>
                <p>Catatan pribadi Anda akan tampil di sini setelah Anda menambahkannya.</p>
                <a class="button button-primary" href="index.php?page=monitoring_form">Catat kebiasaan</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="card dashboard-panel" aria-labelledby="quick-actions-title">
        <div class="section-heading dashboard-panel-heading">
            <div>
                <p class="eyebrow">AKSES CEPAT</p>
                <h2 id="quick-actions-title">Tindakan cepat</h2>
            </div>
        </div>
        <div class="quick-actions-grid">
            <?php foreach ($tindakan_cepat as [$route, $label, $desc, $icon]) : ?>
                <a class="quick-action-link" href="index.php?page=<?= e($route) ?>">
                    <span class="quick-action-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="<?= e($icon) ?>"/></svg>
                    </span>
                    <span class="quick-action-copy">
                        <strong><?= e($label) ?></strong>
                        <small><?= e($desc) ?></small>
                    </span>
                    <span class="quick-action-arrow" aria-hidden="true">↗</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</section>
