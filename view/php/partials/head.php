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
    <meta name="theme-color" content="#F8FAFC">
    <meta name="description" content="Informasi pangan, gizi, dan kesehatan yang mudah dijelajahi.">
    <title><?= e($page_title) ?> — Platform Informasi Pangan, Gizi, dan Kesehatan</title>
    <link rel="stylesheet" href="view/css/style.css">
    <link rel="stylesheet" href="view/css/navigation.css">
    <link rel="stylesheet" href="view/css/article.css">
    <?php if ($template === 'artikel_form.php') : ?><link rel="stylesheet" href="view/css/vendor/quill.snow.css"><?php endif; ?>
</head>
<body>
