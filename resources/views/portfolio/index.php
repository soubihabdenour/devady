<?php
/** @var App\Kernel $kernel */
/** @var array $settings */
/** @var bool $authed */
/** @var array $items */
/** @var array $research */

use App\Support\Brand;
use App\Support\I18n;

$brand    = $settings['company_name']           ?: 'DevAdy';
$founder  = $settings['company_legal_name']     ?: '';
$email    = $settings['company_email']          ?: '';
$lang     = I18n::current();
$dir      = I18n::dir();
$dark     = Brand::DARK;

/* Section-anchor nav targets need to be absolute since we're not on /. */
$home = '/';

$icon = function(string $n): string {
    $s = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
    $p = [
        'arrow' => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        'external' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>',
        'mail'  => '<rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="2 6 12 13 22 6"/>',
        'gh'    => '<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>',
        'in'    => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
        'x'     => '<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
    ];
    return $s . ($p[$n] ?? '') . '</svg>';
};
?>
<!doctype html>
<html lang="<?= e(I18n::htmlLang()) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($brand) ?> &mdash; <?= e(t('nav_portfolio')) ?></title>
<meta name="description" content="<?= e(t('portfolio_p')) ?>">
<meta name="theme-color" content="<?= $dark['bg'] ?>">
<link rel="icon" type="image/svg+xml" href="<?= Brand::faviconDataUri() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Cairo:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/css/brand.css">
<link rel="stylesheet" href="/css/landing.css">
</head>
<body>

<nav class="nav">
    <a href="<?= e($home) ?>" style="text-decoration:none;"><?= Brand::logo($brand) ?></a>
    <div class="nav-links">
        <a href="<?= e($home) ?>#services"><?= e(t('nav_services')) ?></a>
        <a href="/portfolio" aria-current="page"><?= e(t('nav_portfolio')) ?></a>
        <a href="<?= e($home) ?>#stack"><?= e(t('nav_stack')) ?></a>
        <a href="<?= e($home) ?>#process"><?= e(t('nav_process')) ?></a>
        <a href="<?= e($home) ?>#engagements"><?= e(t('nav_engagements')) ?></a>
        <a href="<?= e($home) ?>#faq"><?= e(t('nav_faq')) ?></a>

        <div class="lang-switch" role="group" aria-label="Language">
            <?php foreach (I18n::AVAILABLE as $L): ?>
                <a class="lang-pill <?= $L === $lang ? 'active' : '' ?>"
                   href="<?= e(I18n::switchUrl($L)) ?>"
                   hreflang="<?= e($L) ?>"
                   aria-current="<?= $L === $lang ? 'true' : 'false' ?>">
                    <?= e(I18n::nativeName($L)) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($authed): ?>
            <a href="/admin" class="cta"><?= e(t('nav_dashboard')) ?></a>
        <?php else: ?>
            <a href="<?= e($home) ?>#contact" class="cta"><?= e(t('nav_cta')) ?></a>
        <?php endif; ?>
    </div>
</nav>

<header class="portfolio-hero">
    <span class="label-chip"><?= e(t('portfolio_label')) ?></span>
    <h1><?= e(t('portfolio_h')) ?></h1>
    <p class="sub"><?= e(t('portfolio_p')) ?></p>
</header>

<section class="portfolio-section reveal">
    <div class="portfolio-grid">
        <?php foreach ($items as $i => $p): ?>
            <article class="portfolio-card">
                <div class="portfolio-meta">
                    <span class="portfolio-period mono ltr-force"><?= e($p['period']) ?></span>
                    <span class="portfolio-role"><?= e(t($p['role'])) ?></span>
                </div>
                <h3><?= e($p['title']) ?></h3>
                <p><?= e(t($p['body'])) ?></p>
                <div class="portfolio-stack">
                    <span class="portfolio-stack-l"><?= e(t('portfolio_stack_l')) ?></span>
                    <div class="tags">
                        <?php foreach ($p['tags'] as $tg): ?>
                            <span class="chip"><?= e($tg) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if (!empty($p['url'])): ?>
                    <a class="portfolio-link" href="<?= e($p['url']) ?>" target="_blank" rel="noopener">
                        <?= e(t($p['cta'] ?? 'portfolio_view_repo')) ?>
                        <?= $icon('external') ?>
                    </a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="research-strip reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('research_label')) ?></span>
        <h2><?= e(t('research_h')) ?></h2>
        <p><?= e(t('research_p')) ?></p>
    </div>
    <div class="research-links">
        <?php foreach ($research as $r): ?>
            <a class="research-link" href="<?= e($r['url']) ?>" target="_blank" rel="noopener">
                <span><?= e($r['label']) ?></span>
                <?= $icon('external') ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="cta-banner reveal">
    <h2><?= e(t('portfolio_cta_h')) ?></h2>
    <p><?= e(t('portfolio_cta_p')) ?></p>
    <a href="<?= e($home) ?>#contact" class="btn primary"><?= e(t('hero_cta_primary')) ?> <?= $icon('arrow') ?></a>
</div>

<footer>
    <div class="footer-grid">
        <div>
            <?= Brand::logo($brand, 'light', 24) ?>
            <p class="about-blurb"><?= e(t('footer_blurb')) ?></p>
            <div class="social" aria-label="Social links">
                <a href="#" aria-label="GitHub" rel="noopener"><?= $icon('gh') ?></a>
                <a href="#" aria-label="LinkedIn" rel="noopener"><?= $icon('in') ?></a>
                <a href="#" aria-label="X / Twitter" rel="noopener"><?= $icon('x') ?></a>
            </div>
        </div>
        <div>
            <h5><?= e(t('footer_services_h')) ?></h5>
            <?php foreach (['svc1_title','svc2_title','svc3_title','svc4_title','svc5_title','svc6_title'] as $sKey): ?>
                <a href="<?= e($home) ?>#services"><?= e(t($sKey)) ?></a>
            <?php endforeach; ?>
        </div>
        <div>
            <h5><?= e(t('footer_company_h')) ?></h5>
            <a href="<?= e($home) ?>#about"><?= e(t('footer_about_link')) ?></a>
            <a href="/portfolio"><?= e(t('nav_portfolio')) ?></a>
            <a href="<?= e($home) ?>#process"><?= e(t('nav_process')) ?></a>
            <a href="<?= e($home) ?>#engagements"><?= e(t('nav_engagements')) ?></a>
            <a href="<?= e($home) ?>#contact"><?= e(t('footer_contact_h')) ?></a>
        </div>
        <div>
            <h5><?= e(t('footer_contact_h')) ?></h5>
            <?php if ($email): ?><a href="mailto:<?= e($email) ?>" class="ltr-force"><?= e($email) ?></a><?php endif; ?>
            <a href="<?= e($home) ?>#contact"><?= e(t('footer_contact_form_link')) ?></a>
        </div>
    </div>

    <div class="footer-bottom">
        <div>
            &copy; <?= date('Y') ?> <?= e($brand) ?>.
        </div>
        <a class="admin" href="<?= $authed ? '/admin' : '/login' ?>">&rarr; admin</a>
    </div>
</footer>

<script src="/js/reveal.js"></script>
</body>
</html>
