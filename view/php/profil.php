<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
/** @var array{username:string,email:?string,role:string} $profil */
$profile_errors = is_array($profile_state['errors'] ?? null) ? $profile_state['errors'] : [];
$profile_form = $profile_state['form'] ?? '';
$username_value = $profile_form === 'username' && is_string($profile_state['username'] ?? null)
    ? $profile_state['username'] : $profil['username'];
$error_targets = [
    'username' => 'profil-username',
    'current_password' => $profile_form === 'username' ? 'profil-username-password' : 'profil-password-saat-ini',
    'new_password' => 'profil-password-baru',
    'confirm_password' => 'profil-password-konfirmasi'
];
?>
<section class="page-intro private-intro">
    <p class="eyebrow"><span class="eyebrow-line"></span> PENGATURAN AKUN</p>
    <h1>Profil Saya</h1>
    <p class="page-lead">Kelola informasi dan keamanan akun Anda.</p>
</section>

<?php if ($profile_errors) : ?>
    <?php foreach ($profile_errors as $field => $message) : ?>
        <p class="form-status is-error" role="alert"><a href="#<?= e($error_targets[$field] ?? 'main-content') ?>"><?= e($message) ?> Periksa isian terkait.</a></p>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($pesan) : ?>
    <p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="status"><?= e($pesan['teks']) ?></p>
<?php endif; ?>

<div class="profile-layout">
    <section class="form-card profile-overview" aria-labelledby="informasi-akun">
        <div class="profile-heading">
            <span class="profile-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.6-3.3 3.2-5 7-5s6.4 1.7 7 5"></path></svg>
            </span>
            <div>
                <h2 id="informasi-akun">Informasi Akun</h2>
                <p>Ringkasan identitas akun Anda.</p>
            </div>
            <span class="profile-role"><?= e($profil['role'] === 'admin' ? 'Admin' : 'User') ?></span>
        </div>
        <dl class="profile-details">
            <div><dt>Username</dt><dd><?= e($profil['username']) ?></dd></div>
            <div><dt>Email</dt><dd><?= $profil['email'] ? e($profil['email']) : 'Belum tersedia' ?></dd></div>
        </dl>
    </section>

    <div class="profile-settings-grid">
        <form class="form-card form-stack profile-form" action="index.php?page=profil_username" method="post" aria-labelledby="ubah-username-title">
            <div class="profile-heading">
                <span class="profile-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                </span>
                <div>
                    <h2 id="ubah-username-title">Ubah Username</h2>
                    <p>Perbarui nama akun untuk masuk ke Gizantara.</p>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-field">
                <label for="profil-username">Username Baru</label>
                <input id="profil-username" name="username" value="<?= e($username_value) ?>" maxlength="50" autocomplete="username" required aria-describedby="profil-username-error" <?= isset($profile_errors['username']) ? 'aria-invalid="true"' : '' ?>>
                <small class="field-error" id="profil-username-error" <?= isset($profile_errors['username']) ? '' : 'hidden' ?>><?= e($profile_errors['username'] ?? '') ?></small>
            </div>
            <div class="form-field">
                <label for="profil-username-password">Password Saat Ini</label>
                <input id="profil-username-password" name="current_password" type="password" autocomplete="current-password" required aria-describedby="profil-username-password-error" <?= $profile_form === 'username' && isset($profile_errors['current_password']) ? 'aria-invalid="true"' : '' ?>>
                <small class="field-error" id="profil-username-password-error" <?= $profile_form === 'username' && isset($profile_errors['current_password']) ? '' : 'hidden' ?>><?= e($profile_form === 'username' ? ($profile_errors['current_password'] ?? '') : '') ?></small>
            </div>
            <div class="form-actions"><button class="button button-primary" type="submit">Simpan Username</button></div>
        </form>

        <form class="form-card form-stack profile-form" action="index.php?page=profil_password" method="post" aria-labelledby="ubah-password-title">
            <div class="profile-heading">
                <span class="profile-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path><path d="M12 14v3"></path></svg>
                </span>
                <div>
                    <h2 id="ubah-password-title">Ubah Password</h2>
                    <p>Gunakan password kuat yang belum pernah dipakai.</p>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-field">
                <label for="profil-password-saat-ini">Password Saat Ini</label>
                <input id="profil-password-saat-ini" name="current_password" type="password" autocomplete="current-password" required aria-describedby="profil-password-saat-ini-error" <?= $profile_form === 'password' && isset($profile_errors['current_password']) ? 'aria-invalid="true"' : '' ?>>
                <small class="field-error" id="profil-password-saat-ini-error" <?= $profile_form === 'password' && isset($profile_errors['current_password']) ? '' : 'hidden' ?>><?= e($profile_form === 'password' ? ($profile_errors['current_password'] ?? '') : '') ?></small>
            </div>
            <div class="form-field">
                <label for="profil-password-baru">Password Baru <span class="label-hint">min. 8 karakter</span></label>
                <input id="profil-password-baru" name="new_password" type="password" autocomplete="new-password" minlength="8" required aria-describedby="profil-password-baru-error" <?= isset($profile_errors['new_password']) ? 'aria-invalid="true"' : '' ?>>
                <small class="field-error" id="profil-password-baru-error" <?= isset($profile_errors['new_password']) ? '' : 'hidden' ?>><?= e($profile_errors['new_password'] ?? '') ?></small>
            </div>
            <div class="form-field">
                <label for="profil-password-konfirmasi">Konfirmasi Password Baru</label>
                <input id="profil-password-konfirmasi" name="confirm_password" type="password" autocomplete="new-password" minlength="8" required aria-describedby="profil-password-konfirmasi-error" <?= isset($profile_errors['confirm_password']) ? 'aria-invalid="true"' : '' ?>>
                <small class="field-error" id="profil-password-konfirmasi-error" <?= isset($profile_errors['confirm_password']) ? '' : 'hidden' ?>><?= e($profile_errors['confirm_password'] ?? '') ?></small>
            </div>
            <div class="form-actions"><button class="button button-primary" type="submit">Ubah Password</button></div>
        </form>
    </div>
</div>
