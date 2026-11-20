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
        ['Artikel', $jumlah_artikel, $jumlah_pending_artikel > 0 ? $jumlah_pending_artikel . ' menunggu moderasi' : 'Seluruh artikel terdaftar', 'artikel_kelola'],
        ['Bahan pangan', $jumlah_pangan, $jumlah_pending_pangan > 0 ? $jumlah_pending_pangan . ' menunggu moderasi' : 'Seluruh bahan pangan terdaftar', 'pangan_kelola'],
        ['Antrean moderasi', $jumlah_pending_artikel + $jumlah_pending_pangan, 'Artikel: ' . $jumlah_pending_artikel . ' · Pangan: ' . $jumlah_pending_pangan, $jumlah_pending_artikel >= $jumlah_pending_pangan ? 'moderasi_artikel' : 'moderasi_pangan'],
        ['User', $jumlah_user, 'Akun User terdaftar', 'users'],
        ['Catatan pribadi', $jumlah_catatan, 'Monitoring milik akun ini', 'monitoring'],
    ]
    : [
        ['Artikel saya', $jumlah_artikel, ($status_artikel['approved'] ?? 0) . ' disetujui · ' . ($status_artikel['pending'] ?? 0) . ' menunggu · ' . ($status_artikel['rejected'] ?? 0) . ' ditolak', 'artikel_kelola'],
        ['Bahan pangan saya', $jumlah_pangan, ($status_pangan['approved'] ?? 0) . ' disetujui · ' . ($status_pangan['pending'] ?? 0) . ' menunggu · ' . ($status_pangan['rejected'] ?? 0) . ' ditolak', 'pangan_kelola'],
        ['Catatan pribadi', $jumlah_catatan, 'Riwayat monitoring milik akun ini', 'monitoring'],
    ];
if (!empty($dashboard_super_admin)) {
    array_splice($dashboard_cards, 3, 0, [['Admin', $jumlah_admin ?? 0, 'Akun Admin terdaftar', 'admins']]);
}

$tindakan_cepat = [
    ['artikel_form', 'Tulis Artikel Baru', 'Buat artikel edukasi pangan atau kesehatan.', 'M12 20h9M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z'],
    ['pangan_form', 'Tambah Bahan Pangan', 'Catat komposisi gizi per 100 g bersumber.', 'M4 9h16l-1.5 11h-13zM3 6h18M8 6l2-3m6 3-2-3'],
    ['kalkulator', 'Kalkulator Gizi', 'Hitung estimasi gizi dari satu bahan pangan.', 'M5 3h14v18H5zM8 7h2m4 0h2M8 11h2m4 0h2M8 15h2m4 0h2'],
    ['monitoring_form', 'Catat Pola Hidup', 'Simpan catatan air, tidur, dan aktivitas.', 'M4 19V5m0 14h16M7 15l4-5 3 2 5-7'],
];
if ($dashboard_admin) {
    $tindakan_cepat[] = ['moderasi_artikel', 'Moderasi Artikel', 'Tinjau artikel User sebelum terbit.', 'M9 12l2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'];
    $tindakan_cepat[] = ['moderasi_pangan', 'Moderasi Pangan', 'Tinjau bahan pangan User sebelum terbit.', 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2'];
    $tindakan_cepat[] = ['users', 'Kelola Akun User', 'Buat, blokir, atau hapus akun User.', 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m14-10a4 4 0 1 0 0-8m4 18v-2a4 4 0 0 0-3-3.87M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'];
}
if (!empty($dashboard_super_admin)) {
    $tindakan_cepat[] = ['admins', 'Kelola Akun Admin', 'Buat, blokir, atau hapus akun Admin.', 'M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z'];
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> <?= !empty($dashboard_super_admin) ? 'RUANG SUPER ADMIN' : ($dashboard_admin ? 'RUANG ADMIN' : 'RUANG USER') ?></p>
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
