<?php
/** Data disiapkan index.php dan loader route sebelum template dirender.
 * @var array{jenis: string, teks: string}|null $pesan
 */
 if (!defined('APP_ENTRY')) { http_response_code(403); exit; } ?>
<section class="login-page" aria-labelledby="login-title">
    <div class="login-card">
        <p class="eyebrow"><span class="eyebrow-line"></span> AREA PENGGUNA</p>
        <h1 id="login-title">Selamat datang kembali.</h1>
        <p class="page-lead">Masuk untuk mengakses ruang kerja User dan Admin.</p>
        <?php if ($pesan) : ?><p class="form-status <?= $pesan['jenis'] === 'error' ? 'is-error' : '' ?>" role="alert"><?= e($pesan['teks']) ?></p><?php endif; ?>
        <form class="form-stack" action="index.php?page=login" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label for="login-username">Username atau Email</label>
            <input id="login-username" name="username" autocomplete="username" maxlength="254" required>
            <label for="login-password">Password</label>
            <input id="login-password" name="password" type="password" autocomplete="current-password" required>
            <button class="button button-primary" type="submit">Masuk</button>
        </form>
        <p class="login-register-link">Belum punya akun? <a href="index.php?page=register">Daftar</a></p>
    </div>
    <div class="auth-brand">
        <p class="eyebrow">GIZANTARA / SDG 2 &amp; SDG 3</p>
        <h2>Pahami pangan.<br>Bangun kebiasaan baik.</h2>
        <p>Artikel, katalog pangan, dan catatan harian dalam satu ruang informasi yang mudah digunakan.</p>
        <span class="auth-brand-foot">Informasi Pangan &amp; Kesehatan</span>
    </div>
</section>
