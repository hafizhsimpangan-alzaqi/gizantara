<?php

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> DATA PANGAN</p>
    <h1>Manajemen Katalog</h1>
    <p class="page-lead">Kelola informasi bahan pangan, nilai gizi, dan sumbernya.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert" data-flash data-kind="<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<section class="private-toolbar">
    <div>
        <h2>Daftar bahan pangan</h2>
        <p><?= count($daftar_kelola_pangan) ?> bahan · nilai gizi per 100 gram bagian yang dapat dimakan.</p>
    </div>
    <a class="button button-primary" href="index.php?page=pangan_form">Tambah bahan</a>
</section>

<?php if (!$daftar_kelola_pangan) : ?>
    <div class="empty-state">
        <span class="empty-mark empty-mark-green" aria-hidden="true"></span>
        <h3>Katalog belum berisi data</h3>
        <p>Tambahkan bahan pangan dengan nilai gizi per 100 g dan sumber yang jelas.</p>
        <a class="button button-primary" href="index.php?page=pangan_form">Tambah bahan</a>
    </div>
<?php else : ?>
    <div class="user-table-wrap">
        <table class="user-table food-manage-table">
            <thead>
                <tr>
                    <th scope="col">Bahan</th>
                    <th scope="col">Kondisi</th>
                    <th scope="col">Status</th>
                    <th scope="col">Gizi / 100 g</th>
                    <th scope="col">Pembuat</th>
                    <th scope="col">Dibuat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_kelola_pangan as $item) : ?>
                    <?php
                    $st = $item['moderation_status'] ?? 'approved';
                    $st_label = $st === 'approved' ? 'Disetujui' : ($st === 'rejected' ? 'Ditolak' : 'Menunggu');
                    $st_class = $st === 'approved' ? 'badge-success' : ($st === 'rejected' ? 'badge-danger' : 'badge-warning');
                    ?>
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
                            <span class="badge-tag <?= $st_class ?>"><?= e($st_label) ?></span>
                            <?php if ($st === 'rejected' && !empty($item['rejection_reason'])) : ?>
                                <small class="moderation-reason"><?= e($item['rejection_reason']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="food-energy-val"><?= e($item['energy_kcal']) ?> kkal</strong>
                            <span class="food-macro-summary">P <?= e($item['protein_g']) ?>g · L <?= e($item['fat_g']) ?>g · K <?= e($item['carbohydrate_g']) ?>g</span>
                        </td>
                        <td><?= e($item['author']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($item['created_at']))) ?></td>
                        <td>
                            <div class="record-actions">
                                <a class="button button-secondary" href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>">Detail</a>
                                <a class="button button-secondary" href="index.php?page=pangan_form&amp;id=<?= e($item['id']) ?>">Edit</a>
                                <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="food-delete-dialog" data-delete-id="<?= e($item['id']) ?>" data-delete-name="<?= e($item['name']) ?>">Hapus</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="user-card-list">
        <?php foreach ($daftar_kelola_pangan as $item) : ?>
            <?php
            $st = $item['moderation_status'] ?? 'approved';
            $st_label = $st === 'approved' ? 'Disetujui' : ($st === 'rejected' ? 'Ditolak' : 'Menunggu');
            $st_class = $st === 'approved' ? 'badge-success' : ($st === 'rejected' ? 'badge-danger' : 'badge-warning');
            ?>
            <article class="user-card article-manage-card food-manage-card">
                <div class="card-manage-header">
                    <?php if (!empty($item['image_url'])) : ?>
                        <img class="card-manage-thumb" src="<?= e($item['image_url']) ?>" alt="" width="64" height="48">
                    <?php else : ?>
                        <span class="card-manage-thumb managed-cover-fallback managed-food-fallback" aria-hidden="true">+</span>
                    <?php endif; ?>
                    <div class="card-manage-title-wrap">
                        <p class="category-label"><?= e($item['food_state']) ?> · <span class="badge-tag <?= $st_class ?>"><?= e($st_label) ?></span></p>
                        <h3><a href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>"><?= e($item['name']) ?></a></h3>
                        <small><?= e($item['energy_kcal']) ?> kkal · P <?= e($item['protein_g']) ?>g · L <?= e($item['fat_g']) ?>g · K <?= e($item['carbohydrate_g']) ?>g / 100 g</small>
                        <small><?= e($item['author']) ?> · <?= e(date('d/m/Y', strtotime($item['created_at']))) ?></small>
                        <?php if ($st === 'rejected' && !empty($item['rejection_reason'])) : ?>
                            <small class="moderation-reason">Alasan penolakan: <?= e($item['rejection_reason']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="record-actions">
                    <a class="button button-secondary" href="index.php?page=pangan_detail&amp;id=<?= e($item['id']) ?>">Detail</a>
                    <a class="button button-secondary" href="index.php?page=pangan_form&amp;id=<?= e($item['id']) ?>">Edit</a>
                    <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="food-delete-dialog" data-delete-id="<?= e($item['id']) ?>" data-delete-name="<?= e($item['name']) ?>">Hapus</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<dialog class="confirm-dialog" id="food-delete-dialog" aria-labelledby="food-delete-title">
    <h2 id="food-delete-title">Hapus bahan pangan?</h2>
    <p><strong data-delete-name></strong> akan dihapus dan tidak dapat dipulihkan.</p>
    <form action="index.php?page=pangan_delete" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <input type="hidden" name="confirm_delete" value="1">
        <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
        <button class="button button-danger" type="submit">Ya, Hapus</button>
    </form>
</dialog>
<script src="view/js/form.js" defer></script>
