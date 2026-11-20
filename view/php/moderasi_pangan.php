<?php
/** Data disiapkan index.php dan process/moderasi/load.php sebelum template dirender.
 * @var array<int, array<string, mixed>> $antrean_moderasi
 * @var array<int, array<string, mixed>> $riwayat_moderasi
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> MODERASI KONTEN</p>
    <h1>Moderasi Bahan Pangan</h1>
    <p class="page-lead">Tinjau data bahan pangan kiriman User sebelum tampil di katalog publik dan kalkulator gizi.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert" data-flash data-kind="<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<section class="private-toolbar">
    <div>
        <h2>Antrean menunggu tinjauan</h2>
        <p><?= count($antrean_moderasi) ?> bahan pangan menunggu keputusan.</p>
    </div>
    <a class="button button-secondary" href="index.php?page=pangan_kelola">Kelola semua bahan pangan</a>
</section>

<?php if (!$antrean_moderasi) : ?>
    <div class="empty-state">
        <span class="empty-mark empty-mark-green" aria-hidden="true"></span>
        <h3>Tidak ada bahan pangan menunggu moderasi</h3>
        <p>Semua kiriman bahan pangan User sudah ditinjau.</p>
    </div>
<?php else : ?>
    <div class="user-table-wrap">
        <table class="user-table food-manage-table">
            <thead>
                <tr>
                    <th scope="col">Bahan</th>
                    <th scope="col">Kondisi</th>
                    <th scope="col">Gizi / 100 g</th>
                    <th scope="col">Sumber</th>
                    <th scope="col">Pembuat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($antrean_moderasi as $item) : ?>
                    <tr>
                        <td>
                            <div class="managed-food-title">
                                <?php if (!empty($item['image_url'])) : ?>
                                    <img src="<?= e($item['image_url']) ?>" alt="" width="52" height="39">
                                <?php else : ?>
                                    <span class="managed-cover-fallback managed-food-fallback" aria-hidden="true">+</span>
                                <?php endif; ?>
                                <a href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>"><?= e($item['name']) ?></a>
                            </div>
                        </td>
                        <td><span class="badge-tag badge-neutral"><?= e($item['food_state']) ?></span></td>
                        <td>
                            <strong class="food-energy-val"><?= e($item['energy_kcal']) ?> kkal</strong>
                            <span class="food-macro-summary">P <?= e($item['protein_g']) ?>g · L <?= e($item['fat_g']) ?>g · K <?= e($item['carbohydrate_g']) ?>g</span>
                        </td>
                        <td><?= e($item['source_name']) ?></td>
                        <td>
                            <?= e($item['author']) ?>
                            <?php if (($item['author_status'] ?? 'active') === 'banned') : ?>
                                <span class="badge-tag badge-danger">Diblokir</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="record-actions">
                                <a class="button button-secondary" href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>">Pratinjau</a>
                                <form action="index.php?page=moderasi_pangan_action" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                                    <input type="hidden" name="decision" value="approved">
                                    <button class="button button-primary" type="submit">Setujui</button>
                                </form>
                                <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="food-reject-dialog" data-delete-id="<?= e($item['id']) ?>" data-delete-name="<?= e($item['name']) ?>">Tolak</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="user-card-list">
        <?php foreach ($antrean_moderasi as $item) : ?>
            <article class="user-card article-manage-card food-manage-card">
                <div class="card-manage-header">
                    <?php if (!empty($item['image_url'])) : ?>
                        <img class="card-manage-thumb" src="<?= e($item['image_url']) ?>" alt="" width="64" height="48">
                    <?php else : ?>
                        <span class="card-manage-thumb managed-cover-fallback managed-food-fallback" aria-hidden="true">+</span>
                    <?php endif; ?>
                    <div class="card-manage-title-wrap">
                        <p class="category-label"><?= e($item['food_state']) ?> · <span class="badge-tag badge-warning">Menunggu</span></p>
                        <h3><a href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>"><?= e($item['name']) ?></a></h3>
                        <small><?= e($item['energy_kcal']) ?> kkal · P <?= e($item['protein_g']) ?>g · L <?= e($item['fat_g']) ?>g · K <?= e($item['carbohydrate_g']) ?>g</small>
                        <small><?= e($item['author']) ?> · Sumber: <?= e($item['source_name']) ?></small>
                    </div>
                </div>
                <div class="record-actions">
                    <a class="button button-secondary" href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>">Pratinjau</a>
                    <form action="index.php?page=moderasi_pangan_action" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                        <input type="hidden" name="decision" value="approved">
                        <button class="button button-primary" type="submit">Setujui</button>
                    </form>
                    <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="food-reject-dialog" data-delete-id="<?= e($item['id']) ?>" data-delete-name="<?= e($item['name']) ?>">Tolak</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section class="moderation-history" aria-labelledby="riwayat-moderasi-pangan">
    <h2 id="riwayat-moderasi-pangan">Riwayat Moderasi Terbaru</h2>
    <?php if (!$riwayat_moderasi) : ?>
        <p class="form-note">Belum ada riwayat moderasi bahan pangan.</p>
    <?php else : ?>
        <div class="user-table-wrap">
            <table class="user-table">
                <thead>
                    <tr>
                        <th scope="col">Waktu</th>
                        <th scope="col">Bahan Pangan</th>
                        <th scope="col">Keputusan</th>
                        <th scope="col">Moderator</th>
                        <th scope="col">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat_moderasi as $log) : ?>
                        <tr>
                            <td><?= e(date('d/m/Y H:i', strtotime($log['created_at']))) ?></td>
                            <td><?= e($log['content_title']) ?></td>
                            <td><span class="badge-tag <?= $log['action'] === 'approved' ? 'badge-success' : 'badge-danger' ?>"><?= $log['action'] === 'approved' ? 'Disetujui' : 'Ditolak' ?></span></td>
                            <td><?= e($log['moderator_name']) ?></td>
                            <td><?= !empty($log['reason']) ? e($log['reason']) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<dialog class="confirm-dialog" id="food-reject-dialog" aria-labelledby="reject-food-title">
    <h2 id="reject-food-title">Tolak bahan pangan?</h2>
    <p>Bahan pangan <strong data-delete-name></strong> tidak akan tampil publik. Pembuat akan melihat alasan penolakan untuk memperbaiki datanya.</p>
    <form action="index.php?page=moderasi_pangan_action" method="post" class="form-stack">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <input type="hidden" name="decision" value="rejected">
        <div class="form-field">
            <label for="food-reject-reason">Alasan penolakan</label>
            <textarea id="food-reject-reason" name="reason" rows="3" maxlength="500" required placeholder="Jelaskan data gizi atau sumber yang perlu diperbaiki"></textarea>
        </div>
        <div class="record-actions">
            <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
            <button class="button button-danger" type="submit">Tolak Bahan Pangan</button>
        </div>
    </form>
</dialog>
<script src="view/js/form.js" defer></script>
