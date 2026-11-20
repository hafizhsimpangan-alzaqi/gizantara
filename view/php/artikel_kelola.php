<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var string $user_role
 * @var array<int, array<string, mixed>> $daftar_kelola
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> KONTEN EDUKATIF</p>
    <h1>Manajemen Artikel</h1>
    <p class="page-lead"><?= in_array($user_role ?? '', ['admin', 'super_admin'], true) ? 'Kelola seluruh artikel pangan dan kesehatan.' : 'Kelola artikel pangan dan kesehatan yang Anda tulis. Artikel User memerlukan persetujuan Admin sebelum tampil publik.' ?></p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert" data-flash data-kind="<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<section class="private-toolbar">
    <div>
        <h2>Daftar artikel</h2>
        <p><?= count($daftar_kelola) ?> artikel tersedia.</p>
    </div>
    <a class="button button-primary" href="index.php?page=artikel_form">Tulis artikel</a>
</section>

<?php if (!$daftar_kelola) : ?>
    <div class="empty-state">
        <span class="empty-mark" aria-hidden="true"></span>
        <h3>Belum ada artikel</h3>
        <p>Artikel yang Anda tulis akan ditinjau Admin terlebih dahulu sebelum tampil di halaman publik.</p>
        <a class="button button-primary" href="index.php?page=artikel_form">Tulis artikel</a>
    </div>
<?php else : ?>
    <div class="user-table-wrap">
        <table class="user-table article-manage-table">
            <thead>
                <tr>
                    <th scope="col">Artikel</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Status</th>
                    <th scope="col">Penulis</th>
                    <th scope="col">Dibuat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_kelola as $item) : ?>
                    <?php
                    $st = $item['moderation_status'] ?? 'approved';
                    $st_label = $st === 'approved' ? 'Disetujui' : ($st === 'rejected' ? 'Ditolak' : 'Menunggu');
                    $st_class = $st === 'approved' ? 'badge-success' : ($st === 'rejected' ? 'badge-danger' : 'badge-warning');
                    ?>
                    <tr>
                        <td>
                            <div class="managed-article-title">
                                <?php if (!empty($item['cover_url'])) : ?>
                                    <img src="<?= e($item['cover_url']) ?>" alt="" width="48" height="36">
                                <?php else : ?>
                                    <span class="managed-cover-fallback" aria-hidden="true">+</span>
                                <?php endif; ?>
                                <a href="index.php?page=artikel_detail&amp;id=<?= e($item['id']) ?>"><?= e($item['title']) ?></a>
                            </div>
                        </td>
                        <td><span class="badge-tag"><?= e($item['category']) ?></span></td>
                        <td>
                            <span class="badge-tag <?= $st_class ?>"><?= e($st_label) ?></span>
                            <?php if ($st === 'rejected' && !empty($item['rejection_reason'])) : ?>
                                <small class="moderation-reason"><?= e($item['rejection_reason']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($item['author']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($item['created_at']))) ?></td>
                        <td>
                            <div class="record-actions">
                                <a class="button button-secondary" href="index.php?page=artikel_detail&amp;id=<?= e($item['id']) ?>">Detail</a>
                                <a class="button button-secondary" href="index.php?page=artikel_form&amp;id=<?= e($item['id']) ?>">Edit</a>
                                <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="article-delete-dialog" data-delete-id="<?= e($item['id']) ?>" data-delete-name="<?= e($item['title']) ?>">Hapus</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="user-card-list">
        <?php foreach ($daftar_kelola as $item) : ?>
            <?php
            $st = $item['moderation_status'] ?? 'approved';
            $st_label = $st === 'approved' ? 'Disetujui' : ($st === 'rejected' ? 'Ditolak' : 'Menunggu');
            $st_class = $st === 'approved' ? 'badge-success' : ($st === 'rejected' ? 'badge-danger' : 'badge-warning');
            ?>
            <article class="user-card article-manage-card">
                <div class="card-manage-header">
                    <?php if (!empty($item['cover_url'])) : ?>
                        <img class="card-manage-thumb" src="<?= e($item['cover_url']) ?>" alt="" width="64" height="48">
                    <?php else : ?>
                        <span class="card-manage-thumb managed-cover-fallback" aria-hidden="true">+</span>
                    <?php endif; ?>
                    <div class="card-manage-title-wrap">
                        <p class="category-label"><?= e($item['category']) ?> · <span class="badge-tag <?= $st_class ?>"><?= e($st_label) ?></span></p>
                        <h3><a href="index.php?page=artikel_detail&amp;id=<?= e($item['id']) ?>"><?= e($item['title']) ?></a></h3>
                        <small><?= e($item['author']) ?> · <?= e(date('d/m/Y', strtotime($item['created_at']))) ?></small>
                        <?php if ($st === 'rejected' && !empty($item['rejection_reason'])) : ?>
                            <small class="moderation-reason">Alasan penolakan: <?= e($item['rejection_reason']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="record-actions">
                    <a class="button button-secondary" href="index.php?page=artikel_detail&amp;id=<?= e($item['id']) ?>">Detail</a>
                    <a class="button button-secondary" href="index.php?page=artikel_form&amp;id=<?= e($item['id']) ?>">Edit</a>
                    <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="article-delete-dialog" data-delete-id="<?= e($item['id']) ?>" data-delete-name="<?= e($item['title']) ?>">Hapus</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<dialog class="confirm-dialog" id="article-delete-dialog" aria-labelledby="delete-article-title">
    <h2 id="delete-article-title">Hapus artikel?</h2>
    <p>Artikel <strong data-delete-name></strong> akan dihapus dan tidak dapat dipulihkan.</p>
    <form action="index.php?page=artikel_delete" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <input type="hidden" name="confirm_delete" value="1">
        <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
        <button class="button button-danger" type="submit">Ya, Hapus Artikel</button>
    </form>
</dialog>
<script src="view/js/form.js" defer></script>
