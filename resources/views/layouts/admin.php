<?php
/** @var App\Kernel $kernel */
/** @var string $title */
/** @var string $body */
/** @var string $current */ // active nav key

use App\Support\Brand;
use App\Support\I18n;

$lang = I18n::current();
$dir  = I18n::dir();

$brand = $kernel->settings()->get('company_name') ?: 'DevAdy';
$unread = $kernel->leads()->unreadCount();

$nav = [
    ['key' => 'invoices', 'href' => '/admin',          'label' => t('admin_nav_invoices')],
    ['key' => 'clients',  'href' => '/admin/clients',  'label' => t('admin_nav_clients')],
    ['key' => 'leads',    'href' => '/admin/leads',    'label' => t('admin_nav_leads'),    'badge' => $unread],
    ['key' => 'settings', 'href' => '/admin/settings', 'label' => t('admin_nav_settings')],
];
?><!doctype html>
<html lang="<?= e(I18n::htmlLang()) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> &mdash; <?= e($brand) ?></title>
<meta name="theme-color" content="<?= Brand::DARK['bg'] ?>">
<link rel="icon" type="image/svg+xml" href="<?= Brand::faviconDataUri() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Cairo:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/css/brand.css">
<link rel="stylesheet" href="/css/admin.css">
</head>
<body class="admin">
<header class="admin-bar">
    <a class="brand-link" href="/admin"><?= Brand::logo($brand, 'light', 22) ?></a>
    <nav class="admin-nav">
        <?php foreach ($nav as $item): ?>
            <a href="<?= e($item['href']) ?>" class="<?= ($current ?? '') === $item['key'] ? 'active' : '' ?>">
                <?= e($item['label']) ?>
                <?php if (!empty($item['badge'])): ?>
                    <span class="badge"><?= (int)$item['badge'] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-bar-right">
        <div class="lang-switch">
            <?php foreach (I18n::AVAILABLE as $L): ?>
                <a class="lang-pill <?= $L === $lang ? 'active' : '' ?>"
                   href="<?= e(I18n::switchUrl($L)) ?>"><?= e(I18n::nativeName($L)) ?></a>
            <?php endforeach; ?>
        </div>
        <a class="site-link" href="/"><?= e(t('admin_nav_site')) ?></a>
        <form method="post" action="/logout" style="display:inline;">
            <button type="submit" class="logout-link"><?= e(t('admin_nav_logout')) ?></button>
        </form>
    </div>
</header>

<main class="admin-main">
    <?= $body ?>
</main>
</body>
</html>
