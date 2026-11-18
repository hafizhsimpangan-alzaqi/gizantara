<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array<int, array<string, mixed>> $daftar_users
 * @var array{jenis: string, teks: string}|null $pesan
 */

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> KHUSUS ADMIN</p>
    <h1>Manajemen User</h1>
    <p class="page-lead">Buat akun User dan kelola akun yang sudah tersedia. Password awal minimal 8 karakter.</p>
</section>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<section class="private-toolbar">
    <div>
        <h2>Akun User</h2>
        <p><?= count($daftar_users) ?> akun terdaftar.</p>
    </div>
    <a class="button button-primary" href="#form-user">Tambah User</a>
</section>

<?php if (!$daftar_users) : ?>
    <div class="empty-state">
        <span class="empty-mark" aria-hidden="true"></span>
        <h3>Belum ada data User</h3>
        <p>Buat akun pertama melalui formulir di bawah ini.</p>
        <a class="button button-primary" href="#form-user">Tambah User</a>
    </div>
<?php else : ?>
    <div class="user-table-wrap">
        <table class="user-table">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Dibuat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_users as $nomor => $user) : ?>
                    <tr>
                        <td><?= $nomor + 1 ?></td>
                        <td><strong><?= e($user['username']) ?></strong></td>
                        <td><?= $user['email'] ? e($user['email']) : 'Belum diisi' ?></td>
                        <td><span class="badge-tag">User</span></td>
                        <td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td>
                        <td>
                            <div class="record-actions">
                                <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="user-delete-dialog" data-user-id="<?= e($user['id']) ?>" data-user-name="<?= e($user['username']) ?>">Hapus</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="user-card-list">
        <?php foreach ($daftar_users as $user) : ?>
            <article class="user-card user-manage-card">
                <div class="user-card-header">
                    <div>
                        <strong><?= e($user['username']) ?></strong>
                        <small><?= $user['email'] ? e($user['email']) : 'Email belum diisi' ?></small>
                        <small>Dibuat <?= e(date('d/m/Y', strtotime($user['created_at']))) ?></small>
                    </div>
                    <span class="badge-tag">User</span>
                </div>
                <div class="record-actions">
                    <button class="button button-secondary button-danger-subtle" type="button" data-open-confirm="user-delete-dialog" data-user-id="<?= e($user['id']) ?>" data-user-name="<?= e($user['username']) ?>">Hapus</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<dialog class="confirm-dialog" id="user-delete-dialog" aria-labelledby="delete-user-title">
    <h2 id="delete-user-title">Hapus akun User?</h2>
    <p>Akun <strong data-delete-name></strong> akan dihapus. Catatan monitoring pribadinya juga ikut terhapus. Artikel dan bahan pangan harus ditangani lebih dulu.</p>
    <form action="index.php?page=user_delete" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" data-delete-id>
        <input type="hidden" name="confirm_delete" value="1">
        <button class="button button-secondary" type="button" data-close-confirm>Batal</button>
        <button class="button button-danger" type="submit">Ya, Hapus User</button>
    </form>
</dialog>

<form id="form-user" class="form-card form-stack" action="index.php?page=user_create" method="post">
    <h2>Tambah User</h2>
    <p class="form-note">Akun baru ber-role User. Email harus valid dan unik.</p>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="form-field">
        <label for="new-username">Username</label>
        <input id="new-username" name="username" autocomplete="off" maxlength="50" required aria-describedby="new-username-error">
        <small class="field-error" id="new-username-error" hidden></small>
    </div>

    <div class="form-field">
        <label for="new-email">Email</label>
        <input id="new-email" name="email" type="email" autocomplete="email" maxlength="254" required aria-describedby="new-email-error">
        <small class="field-error" id="new-email-error" hidden></small>
    </div>

    <div class="form-row">
        <div class="form-field">
            <label for="new-password">Password awal <span class="label-hint">min. 8 karakter</span></label>
            <input id="new-password" name="password" type="password" minlength="8" autocomplete="new-password" required aria-describedby="new-password-error">
            <small class="field-error" id="new-password-error" hidden></small>
        </div>

        <div class="form-field">
            <label for="new-confirm">Ulangi password</label>
            <input id="new-confirm" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required aria-describedby="new-confirm-error">
            <small class="field-error" id="new-confirm-error" hidden></small>
        </div>
    </div>

    <div class="form-actions">
        <button class="button button-primary" type="submit">Tambah User</button>
    </div>
</form>

<script src="view/js/form.js" defer></script>
