<?php
if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#175C51">
    <meta name="description" content="Gizantara — Gizi &amp; Kesehatan untuk Nusantara. Kenali pangan dan bangun kebiasaan sehat.">
    <title><?= e($page_title) ?> — Gizantara</title>
    <link rel="stylesheet" href="view/css/style.css">
    <link rel="stylesheet" href="view/css/navigation.css">
    <link rel="stylesheet" href="view/css/article.css">
    <?php if ($template === 'artikel_form.php') : ?><link rel="stylesheet" href="view/css/vendor/quill.snow.css"><?php endif; ?>
</head>
<body>
