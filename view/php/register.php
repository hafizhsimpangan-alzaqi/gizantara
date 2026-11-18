<?php if (!defined('APP_ENTRY')) { http_response_code(403); exit; } ?>
<section class="login-page" aria-labelledby="register-title">
    <div class="login-card">
        <p class="eyebrow"><span class="eyebrow-line"></span> AKSES AKUN</p>
        <h1 id="register-title">Buat akun Gizantara</h1>
        <p class="page-lead">Daftar sebagai User untuk mulai menggunakan fitur personal Gizantara.</p>
        <?php if ($pesan) : ?><p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert"><?= e($pesan['teks']) ?></p><?php endif; ?>
        <form id="register-form" class="form-card form-stack" action="index.php?page=register" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-field">
                <label for="register-username">Username</label>
                <input id="register-username" name="username" autocomplete="username" maxlength="50" value="<?= e($register_form['username']) ?>" required aria-describedby="register-username-error">
                <small class="field-error" id="register-username-error" <?= empty($register_errors['username']) ? 'hidden' : '' ?>><?= e($register_errors['username'] ?? '') ?></small>
            </div>
            <div class="form-field">
                <label for="register-email">Email</label>
                <input id="register-email" name="email" type="email" autocomplete="email" maxlength="254" value="<?= e($register_form['email']) ?>" required aria-describedby="register-email-error">
                <small class="field-error" id="register-email-error" <?= empty($register_errors['email']) ? 'hidden' : '' ?>><?= e($register_errors['email'] ?? '') ?></small>
            </div>
            <div class="form-field">
                <label for="register-password">Password <span class="label-hint">minimal 8 karakter</span></label>
                <input id="register-password" name="password" type="password" autocomplete="new-password" minlength="8" required aria-describedby="register-password-error">
                <small class="field-error" id="register-password-error" <?= empty($register_errors['password']) ? 'hidden' : '' ?>><?= e($register_errors['password'] ?? '') ?></small>
            </div>
            <div class="form-field">
                <label for="register-confirm">Konfirmasi Password</label>
                <input id="register-confirm" name="confirm_password" type="password" autocomplete="new-password" minlength="8" required aria-describedby="register-confirm-error">
                <small class="field-error" id="register-confirm-error" <?= empty($register_errors['confirm_password']) ? 'hidden' : '' ?>><?= e($register_errors['confirm_password'] ?? '') ?></small>
            </div>
            <button class="button button-primary" type="submit">Daftar</button>
        </form>
        <p class="login-register-link">Sudah punya akun? <a href="index.php?page=login">Masuk</a></p>
    </div>
    <div class="auth-brand">
        <p class="eyebrow">GIZANTARA / SDG 2 &amp; SDG 3</p>
        <h2>Pahami pangan.<br>Bangun kebiasaan baik.</h2>
        <p>Artikel, katalog pangan, dan catatan harian dalam satu ruang informasi yang mudah digunakan.</p>
        <span class="auth-brand-foot">Informasi Pangan &amp; Kesehatan</span>
    </div>
</section>
<script src="view/js/form.js" defer></script>
