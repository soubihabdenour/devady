<?php
/** @var App\Kernel $kernel */
/** @var array $settings */
/** @var bool $authed */
/** @var bool $sent */
/** @var ?string $err */

use App\Support\Brand;
use App\Support\I18n;

$brand    = $settings['company_name']           ?: 'DevAdy';
$founder  = $settings['company_legal_name']     ?: '';
$email    = $settings['company_email']          ?: '';
$activity = $settings['company_activity_label'] ?: 'Software engineering';
$anaeNo   = $settings['company_anae_no']        ?: '';
$lang     = I18n::current();
$dir      = I18n::dir();
$dark     = Brand::DARK;

$stack = [
    'lang'  => ['TypeScript', 'Python', 'PHP', 'Go', 'SQL', 'Bash'],
    'fe'    => ['React', 'Next.js', 'Vue', 'Astro', 'Tailwind', 'Vite'],
    'be'    => ['Node', 'FastAPI', 'Laravel', 'Express', 'tRPC'],
    'data'  => ['PostgreSQL', 'MySQL', 'SQLite', 'Redis', 'BigQuery'],
    'cloud' => ['AWS', 'GCP', 'Cloudflare', 'Docker', 'Kubernetes', 'Terraform'],
    'ai'    => ['OpenAI', 'Anthropic', 'LangChain', 'pgvector', 'Evals'],
];

$serviceCards = [
    ['icon'=>'code','title'=>'svc1_title','body'=>'svc1_body','tags'=>['Next.js','React Native','TypeScript']],
    ['icon'=>'server','title'=>'svc2_title','body'=>'svc2_body','tags'=>['Node','Python','PostgreSQL']],
    ['icon'=>'cloud','title'=>'svc3_title','body'=>'svc3_body','tags'=>['AWS','Docker','Terraform']],
    ['icon'=>'spark','title'=>'svc4_title','body'=>'svc4_body','tags'=>['OpenAI','Anthropic','RAG']],
    ['icon'=>'shield','title'=>'svc5_title','body'=>'svc5_body','tags'=>['Architecture','Performance','Mentoring']],
    ['icon'=>'wrench','title'=>'svc6_title','body'=>'svc6_body','tags'=>['Refactor','Docs','Migration']],
];

$processSteps = [
    ['n'=>'01','title'=>'p1_title','body'=>'p1_body'],
    ['n'=>'02','title'=>'p2_title','body'=>'p2_body'],
    ['n'=>'03','title'=>'p3_title','body'=>'p3_body'],
    ['n'=>'04','title'=>'p4_title','body'=>'p4_body'],
];

$engagementCards = [
    ['tag'=>'e1_tag','title'=>'e1_title','body'=>'e1_body','bullets'=>['e1_b1','e1_b2','e1_b3']],
    ['tag'=>'e2_tag','title'=>'e2_title','body'=>'e2_body','bullets'=>['e2_b1','e2_b2','e2_b3']],
    ['tag'=>'e3_tag','title'=>'e3_title','body'=>'e3_body','bullets'=>['e3_b1','e3_b2','e3_b3']],
];

$faqItems = [
    ['q'=>'faq1_q','a'=>'faq1_a'],['q'=>'faq2_q','a'=>'faq2_a'],['q'=>'faq3_q','a'=>'faq3_a'],
    ['q'=>'faq4_q','a'=>'faq4_a'],['q'=>'faq5_q','a'=>'faq5_a'],['q'=>'faq6_q','a'=>'faq6_a'],
];

$icon = function(string $n): string {
    $s = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
    $p = [
        'code'=>'<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        'server'=>'<rect x="2" y="3" width="20" height="7" rx="2"/><rect x="2" y="14" width="20" height="7" rx="2"/><line x1="6" y1="6.5" x2="6.01" y2="6.5"/><line x1="6" y1="17.5" x2="6.01" y2="17.5"/>',
        'cloud'=>'<path d="M17.5 19a4.5 4.5 0 1 0-1.5-8.74A6 6 0 0 0 5 11.5 4.5 4.5 0 0 0 6.5 19h11z"/>',
        'spark'=>'<path d="M12 2v6"/><path d="M12 16v6"/><path d="M4.93 4.93l4.24 4.24"/><path d="M14.83 14.83l4.24 4.24"/><path d="M2 12h6"/><path d="M16 12h6"/><path d="M4.93 19.07l4.24-4.24"/><path d="M14.83 9.17l4.24-4.24"/>',
        'shield'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'wrench'=>'<path d="M14.7 6.3a4 4 0 0 1-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 0 1 5.4-5.4l-3 3-2-2 3-3z"/>',
        'check'=>'<polyline points="20 6 9 17 4 12"/>',
        'arrow'=>'<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        'mail'=>'<rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="2 6 12 13 22 6"/>',
        'plus'=>'<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'gh'=>'<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>',
        'in'=>'<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
        'x'=>'<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
    ];
    return $s . ($p[$n] ?? '') . '</svg>';
};
?>
<!doctype html>
<html lang="<?= e(I18n::htmlLang()) ?>" dir="<?= e($dir) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($brand) ?> &mdash; <?= e(t('hero_title_prefix') . ' ' . t('hero_title_grad') . ' ' . t('hero_title_suffix')) ?></title>
<meta name="description" content="<?= e(t('footer_blurb')) ?>">
<meta name="theme-color" content="<?= $dark['bg'] ?>">
<link rel="icon" type="image/svg+xml" href="<?= Brand::faviconDataUri() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Cairo:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/css/brand.css">
<link rel="stylesheet" href="/css/landing.css">
</head>
<body>

<nav class="nav">
    <a href="<?= e(I18n::switchUrl($lang)) ?>#top" style="text-decoration:none;"><?= Brand::logo($brand) ?></a>
    <div class="nav-links">
        <a href="#services"><?= e(t('nav_services')) ?></a>
        <a href="#stack"><?= e(t('nav_stack')) ?></a>
        <a href="#process"><?= e(t('nav_process')) ?></a>
        <a href="#engagements"><?= e(t('nav_engagements')) ?></a>
        <a href="#faq"><?= e(t('nav_faq')) ?></a>

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
            <a href="#contact" class="cta"><?= e(t('nav_cta')) ?></a>
        <?php endif; ?>
    </div>
</nav>

<header class="hero" id="top">
    <div class="eyebrow"><span class="dot"></span> <?= e(t('hero_eyebrow')) ?></div>
    <h1><?= e(t('hero_title_prefix')) ?> <span class="grad"><?= e(t('hero_title_grad')) ?></span> <?= e(t('hero_title_suffix')) ?></h1>
    <p class="sub"><?= e(t('hero_sub', ['brand' => $brand])) ?></p>
    <div class="ctas">
        <a href="#contact" class="btn primary"><?= e(t('hero_cta_primary')) ?> <?= $icon('arrow') ?></a>
        <a href="#engagements" class="btn ghost"><?= e(t('hero_cta_ghost')) ?></a>
    </div>

    <div class="terminal reveal">
        <div class="bar">
            <span class="dot r"></span><span class="dot y"></span><span class="dot g"></span>
            <span class="title"><?= e(t('terminal_title')) ?></span>
        </div>
        <div class="body">
            <div><span class="prompt">$</span><span class="cmd">devady --hire</span></div>
            <span class="out"><?= e(t('terminal_out1')) ?></span>
            <span class="out"><?= e(t('terminal_out2')) ?></span>
            <span class="out"><?= e(t('terminal_out3_pre')) ?> <span class="key">honest_estimates</span>=true</span>
            <span class="out"><span class="ok">&#10003;</span> <?= e(t('terminal_out4')) ?></span>
            <div><span class="prompt">$</span><span class="cmd">devady --ship<span class="cursor"></span></span></div>
        </div>
    </div>
</header>

<div class="trust">
    <div><div class="v"><span class="grad">10+</span></div><div class="l"><?= e(t('trust_years_l')) ?></div></div>
    <div><div class="v"><span class="grad">100%</span></div><div class="l"><?= e(t('trust_remote_l')) ?></div></div>
    <div><div class="v"><span class="grad">UTC+1</span></div><div class="l"><?= e(t('trust_tz_l')) ?></div></div>
    <div><div class="v"><span class="grad">EN&middot;FR&middot;AR</span></div><div class="l"><?= e(t('trust_lang_l')) ?></div></div>
</div>

<section id="services" class="reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('services_label')) ?></span>
        <h2><?= e(t('services_h')) ?></h2>
        <p><?= e(t('services_p')) ?></p>
    </div>
    <div class="svc-grid">
        <?php foreach ($serviceCards as $svc): ?>
            <div class="svc-card">
                <div class="icon"><?= $icon($svc['icon']) ?></div>
                <h3><?= e(t($svc['title'])) ?></h3>
                <p><?= e(t($svc['body'])) ?></p>
                <div class="tags">
                    <?php foreach ($svc['tags'] as $tg): ?><span class="chip"><?= e($tg) ?></span><?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="stack" class="reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('stack_label')) ?></span>
        <h2><?= e(t('stack_h')) ?></h2>
        <p><?= e(t('stack_p')) ?></p>
    </div>
    <div class="stack-grid">
        <?php foreach ($stack as $cat => $items): ?>
            <div class="stack-card">
                <h4><?= e(t('stack_cat_' . $cat)) ?></h4>
                <div class="items">
                    <?php foreach ($items as $it): ?><span class="chip"><?= e($it) ?></span><?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="process" class="reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('process_label')) ?></span>
        <h2><?= e(t('process_h')) ?></h2>
        <p><?= e(t('process_p')) ?></p>
    </div>
    <div class="process">
        <?php foreach ($processSteps as $p): ?>
            <div class="step">
                <span class="n"><?= e($p['n']) ?></span>
                <h4><?= e(t($p['title'])) ?></h4>
                <p><?= e(t($p['body'])) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="engagements" class="reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('eng_label')) ?></span>
        <h2><?= e(t('eng_h')) ?></h2>
        <p><?= e(t('eng_p')) ?></p>
    </div>
    <div class="eng-grid">
        <?php foreach ($engagementCards as $eng): ?>
            <div class="eng-card">
                <span class="tag"><?= e(t($eng['tag'])) ?></span>
                <h3><?= e(t($eng['title'])) ?></h3>
                <p><?= e(t($eng['body'])) ?></p>
                <ul>
                    <?php foreach ($eng['bullets'] as $b): ?>
                        <li><?= $icon('check') ?> <?= e(t($b)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="about" class="reveal">
    <div class="about">
        <div class="about-text">
            <span class="label-chip"><?= e(t('about_label')) ?></span>
            <h2><?= e(t('about_h')) ?></h2>
            <p><?= $founder
                ? e(t('about_p1', ['brand' => $brand, 'founder' => $founder]))
                : e(t('about_p1_anon', ['brand' => $brand])) ?></p>
            <p><?= e(t('about_p2', ['activity' => mb_strtolower($activity)])) ?></p>
            <p><?= e(t('about_p3')) ?></p>
            <a href="#contact" class="btn primary" style="margin-top: 12px;"><?= e(t('about_cta')) ?> <?= $icon('arrow') ?></a>
        </div>
        <div class="id-card">
            <div class="row-line"><span class="k"><?= e(t('idcard_founder')) ?></span><span class="v accent"><?= e($founder ?: t('idcard_engineer')) ?></span></div>
            <div class="row-line"><span class="k"><?= e(t('idcard_practice')) ?></span><span class="v"><?= e($brand) ?></span></div>
            <div class="row-line"><span class="k"><?= e(t('idcard_activity')) ?></span><span class="v"><?= e($activity) ?></span></div>
            <div class="sep"></div>
            <div class="row-line"><span class="k"><?= e(t('idcard_status')) ?></span><span class="v"><?= e(t('idcard_status_v')) ?></span></div>
            <?php if ($anaeNo): ?>
                <div class="row-line"><span class="k"><?= e(t('idcard_anae')) ?></span><span class="v ltr-force"><?= e($anaeNo) ?></span></div>
            <?php endif; ?>
            <div class="row-line"><span class="k"><?= e(t('idcard_timezone')) ?></span><span class="v ltr-force">UTC+1</span></div>
            <div class="sep"></div>
            <div class="row-line"><span class="k"><?= e(t('idcard_available')) ?></span><span class="v" style="color: var(--accent-3);">true</span></div>
        </div>
    </div>
</section>

<section id="faq" class="reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('faq_label')) ?></span>
        <h2><?= e(t('faq_h')) ?></h2>
        <p><?= e(t('faq_p')) ?></p>
    </div>
    <div class="faq">
        <?php foreach ($faqItems as $i => $f): ?>
            <details<?= $i === 0 ? ' open' : '' ?>>
                <summary>
                    <span><?= e(t($f['q'])) ?></span>
                    <span class="toggle"><?= $icon('plus') ?></span>
                </summary>
                <div class="answer"><?= e(t($f['a'])) ?></div>
            </details>
        <?php endforeach; ?>
    </div>
</section>

<div class="cta-banner reveal">
    <h2><?= e(t('cta_h')) ?></h2>
    <p><?= e(t('cta_p')) ?></p>
    <a href="#contact" class="btn primary"><?= e(t('cta_btn')) ?> <?= $icon('arrow') ?></a>
</div>

<section id="contact" class="reveal">
    <div class="section-head">
        <span class="label-chip"><?= e(t('contact_label')) ?></span>
        <h2><?= e(t('contact_h')) ?></h2>
        <p><?= e(t('contact_p')) ?></p>
    </div>

    <?php if ($email): ?>
        <div class="contact-meta">
            <a href="mailto:<?= e($email) ?>"><?= $icon('mail') ?> <span class="ltr-force"><?= e($email) ?></span></a>
        </div>
    <?php endif; ?>

    <div class="contact-wrap">
        <?php if ($sent): ?>
            <div class="alert ok"><?= $icon('check') ?> <?= e(t('contact_ok')) ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert err"><?= e((string)$err) ?></div>
        <?php endif; ?>

        <form method="post" action="/contact?lang=<?= e($lang) ?>" autocomplete="on">
            <div class="hp" aria-hidden="true">
                <label><?= e(t('form_hp_label')) ?></label>
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="row">
                <div>
                    <label for="lead_name"><?= e(t('form_name')) ?></label>
                    <input id="lead_name" type="text" name="name" required maxlength="120" placeholder="<?= e(t('form_name_ph')) ?>">
                </div>
                <div>
                    <label for="lead_email"><?= e(t('form_email')) ?></label>
                    <input id="lead_email" type="email" name="email" required maxlength="160" placeholder="<?= e(t('form_email_ph')) ?>" dir="ltr">
                </div>
            </div>

            <label for="lead_subject"><?= e(t('form_subject')) ?></label>
            <input id="lead_subject" type="text" name="subject" maxlength="200" placeholder="<?= e(t('form_subject_ph')) ?>">

            <label for="lead_message"><?= e(t('form_message')) ?></label>
            <textarea id="lead_message" name="message" required maxlength="5000" placeholder="<?= e(t('form_message_ph')) ?>"></textarea>

            <button type="submit"><?= e(t('form_submit')) ?> <?= $icon('arrow') ?></button>
        </form>
    </div>
</section>

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
                <a href="#services"><?= e(t($sKey)) ?></a>
            <?php endforeach; ?>
        </div>
        <div>
            <h5><?= e(t('footer_company_h')) ?></h5>
            <a href="#about"><?= e(t('footer_about_link')) ?></a>
            <a href="#process"><?= e(t('nav_process')) ?></a>
            <a href="#engagements"><?= e(t('nav_engagements')) ?></a>
            <a href="#faq"><?= e(t('nav_faq')) ?></a>
            <a href="#contact"><?= e(t('footer_contact_h')) ?></a>
        </div>
        <div>
            <h5><?= e(t('footer_contact_h')) ?></h5>
            <?php if ($email): ?><a href="mailto:<?= e($email) ?>" class="ltr-force"><?= e($email) ?></a><?php endif; ?>
            <a href="#contact"><?= e(t('footer_contact_form_link')) ?></a>
        </div>
    </div>

    <div class="footer-bottom">
        <div>
            &copy; <?= date('Y') ?> <?= e($brand) ?>.
            <?= e(t('footer_legal')) ?><?= $anaeNo ? ' &middot; ' . e($anaeNo) : '' ?>.
        </div>
        <a class="admin" href="<?= $authed ? '/admin' : '/login' ?>">&rarr; admin</a>
    </div>
</footer>

<script src="/js/reveal.js"></script>
</body>
</html>
