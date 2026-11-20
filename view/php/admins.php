<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<int, array<string, mixed>> $daftar_admins
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> KHUSUS SUPER ADMIN</p>
    <h1>Manajemen Admin</h1>
    <p class="page-lead">Buat akun Admin dan kelola status Admin platform. Akun Super Admin tidak dikelola dari halaman ini.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert" data-flash data-kind="<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<section class="private-toolbar">
    <div>
        <h2>Akun Admin</h2>
        <p><?= count($daftar_admins) ?> akun Admin terdaftar.</p>
    </div>
    <a class="button button-primary" href="#form-admin">Tambah Admin</a>
</section>

<?php if (!$daftar_admins) : ?>
    <div class="empty-state">
        <span class="empty-mark" aria-hidden="true"></span>
        <h3>Belum ada akun Admin</h3>
        <p>Buat akun Admin pertama melalui formulir di bawah ini.</p>
        <a class="button button-primary" href="#form-admin">Tambah Admin</a>
    </div>
<?php else : ?>
    <div class="user-table-wrap">
        <table class="user-table">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Dibuat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_admins as $nomor => $admin) : ?>
                    <?php $is_banned = ($admin['status'] ?? 'active') === 'banned'; ?>
                    <tr>
                        <td><?= $nomor + 1 ?></td>
                        <td><strong><?= e($admin['username']) ?></strong></td>
                        <td><?= $admin['email'] ? e($admin['email']) : 'Belum diisi' ?></td>
                        <td>
                            <span class="badge-tag <?= $is_banned ? 'badge-danger' : 'badge-success' ?>"><?= $is_banned ? 'Diblokir' : 'Aktif' ?></span>
                            <?php if ($is_banned && !empty($admin['ban_reason'])) : ?>
                                <small class="moderation-reason"><?= e($admin['ban_reason']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e(date('d/m/Y', strtotime($admin['created_at']))) ?></td>
                        <td>
                            <div class="record-actions">
                                <?php if ($is_banned) : ?>
                                    <button class="button button-secondary" type="button" data-open-confirm="admin-unban-dialog" data-user-id="<?= e($admin['id']) ?>" data-user-name="<?= e($admin['username']) ?>">Aktifkan</button>
                                <?php else : ?>
                                    <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="admin-ban-dialog" data-user-id="<?= e($admin['id']) ?>" data-user-name="<?= e($admin['username']) ?>">Blokir</button>
                                <?php endif; ?>
                                <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="admin-delete-dialog" data-user-id="<?= e($admin['id']) ?>" data-user-name="<?= e($admin['username']) ?>">Hapus</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="user-card-list">
        <?php foreach ($daftar_admins as $admin) : ?>
            <?php $is_banned = ($admin['status'] ?? 'active') === 'banned'; ?>
            <article class="user-card user-manage-card">
                <div class="user-card-header">
                    <div>
                        <strong><?= e($admin['username']) ?></strong>
                        <small><?= $admin['email'] ? e($admin['email']) : 'Email belum diisi' ?></small>
                        <small>Dibuat <?= e(date('d/m/Y', strtotime($admin['created_at']))) ?></small>
                        <?php if ($is_banned && !empty($admin['ban_reason'])) : ?>
                            <small class="moderation-reason">Alasan blokir: <?= e($admin['ban_reason']) ?></small>
                        <?php endif; ?>
                    </div>
                    <span class="badge-tag <?= $is_banned ? 'badge-danger' : 'badge-success' ?>"><?= $is_banned ? 'Diblokir' : 'Aktif' ?></span>
                </div>
                <div class="record-actions">
                    <?php if ($is_banned) : ?>
                        <button class="button button-secondary" type="button" data-open-confirm="admin-unban-dialog" data-user-id="<?= e($admin['id']) ?>" data-user-name="<?= e($admin['username']) ?>">Aktifkan</button>
                    <?php else : ?>
                        <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="admin-ban-dialog" data-user-id="<?= e($admin['id']) ?>" data-user-name="<?= e($admin['username']) ?>">Blokir</button>
                    <?php endif; ?>
                    <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="admin-delete-dialog" data-user-id="<?= e($admin['id']) ?>" data-user-name="<?= e($admin['username']) ?>">Hapus</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<dialog class="confirm-dialog" id="admin-ban-dialog" aria-labelledby="ban-admin-title">
    <h2 id="ban-admin-title">Blokir akun Admin?</h2>
    <p>Akun Admin <strong data-delete-name></strong> tidak akan dapat masuk atau melakukan moderasi sampai blokir dibuka. Konten miliknya tidak dihapus.</p>
    <form action="index.php?page=admin_ban" method="post" class="form-stack">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <div class="form-field">
            <label for="admin-ban-reason">Alasan pemblokiran</label>
            <input id="admin-ban-reason" name="ban_reason" maxlength="255" required placeholder="Contoh: Pelanggaran kewenangan moderasi">
        </div>
        <div class="record-actions">
            <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
            <button class="button button-danger" type="submit">Ya, Blokir Admin</button>
        </div>
    </form>
</dialog>

<dialog class="confirm-dialog" id="admin-unban-dialog" aria-labelledby="unban-admin-title">
    <h2 id="unban-admin-title">Buka blokir akun Admin?</h2>
    <p>Akun Admin <strong data-delete-name></strong> akan diaktifkan kembali.</p>
    <form action="index.php?page=admin_unban" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
        <button class="button button-primary" type="submit">Ya, Aktifkan Admin</button>
    </form>
</dialog>

<dialog class="confirm-dialog" id="admin-delete-dialog" aria-labelledby="delete-admin-title">
    <h2 id="delete-admin-title">Hapus akun Admin?</h2>
    <p>Akun Admin <strong data-delete-name></strong> akan dihapus permanen. Admin yang masih memiliki artikel atau bahan pangan tidak dapat dihapus.</p>
    <form action="index.php?page=admin_delete" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <input type="hidden" name="confirm_delete" value="1">
        <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
        <button class="button button-danger" type="submit">Ya, Hapus Admin</button>
    </form>
</dialog>

<form id="form-admin" class="form-card form-stack" action="index.php?page=admin_create" method="post">
    <h2>Tambah Admin</h2>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="form-field">
        <label for="admin-username">Username <span class="label-hint">maks. 50 karakter, tanpa @</span></label>
        <input id="admin-username" name="username" maxlength="50" autocomplete="off" required>
    </div>
    <div class="form-field">
        <label for="admin-email">Email</label>
        <input id="admin-email" name="email" type="email" maxlength="254" autocomplete="off" required>
    </div>
    <div class="form-field">
        <label for="admin-password">Password awal <span class="label-hint">min. 8 karakter</span></label>
        <input id="admin-password" name="password" type="password" minlength="8" autocomplete="new-password" required>
    </div>
    <div class="form-field">
        <label for="admin-confirm-password">Konfirmasi password awal</label>
        <input id="admin-confirm-password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
    </div>
    <div class="form-actions"><button class="button button-primary" type="submit">Simpan Admin</button></div>
</form>
<script src="view/js/form.js" defer></script>
