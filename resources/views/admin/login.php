<?php
/** @var App\Kernel $kernel */
/** @var ?string $error */

use App\Support\Brand;
use App\Support\I18n;

$brand = $kernel->settings()->get('company_name') ?: 'DevAdy';
$lang  = I18n::current();
$dir   = I18n::dir();
?><!doctype html>
<html lang="<?= e(I18n::htmlLang()) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(t('login_title')) ?> &mdash; <?= e($brand) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= Brand::faviconDataUri() ?>">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Cairo:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/css/brand.css">
<link rel="stylesheet" href="/css/admin.css">
</head>
<body class="admin">
<div class="login-wrap">
    <div style="margin-bottom: 24px;"><?= Brand::logo($brand, 'light', 26) ?></div>
    <h1><?= e(t('login_title')) ?></h1>
    <p><?= e(t('login_sub')) ?></p>
    <?php if ($error): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="/login">
        <label><?= e(t('login_password')) ?></label>
        <input type="password" name="password" autofocus required>
        <button type="submit" class="btn primary lg" style="margin-top: 18px; width: 100%; justify-content: center;">
            <?= e(t('login_submit')) ?>
        </button>
    </form>
</div>
</body>
</html>
