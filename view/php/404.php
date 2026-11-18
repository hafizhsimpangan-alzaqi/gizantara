<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<section class="not-found" aria-labelledby="not-found-title">
    <p class="eyebrow"><span class="eyebrow-line"></span> 404 / TIDAK DITEMUKAN</p>
    <div class="not-found-number" aria-hidden="true">404</div>
    <h1 id="not-found-title">Sepertinya halaman ini belum ada.</h1>
    <p><?= e($pesan_404) ?></p>
    <a class="button button-primary" href="index.php?page=home">Kembali ke beranda <span aria-hidden="true">↗</span></a>
</section>
