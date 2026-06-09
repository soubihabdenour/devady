<?php
/** @var App\Kernel $kernel */
/** @var array $invoice */
/** @var ?array $client */
/** @var array $company */
/** @var bool $public */

use App\Support\Brand;

$err = $_GET['err'] ?? null;
$shareToken = $invoice['public_token'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice <?= e($invoice['number']) ?></title>
<meta name="theme-color" content="<?= Brand::DARK['bg'] ?>">
<link rel="icon" type="image/svg+xml" href="<?= Brand::faviconDataUri() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap">
<link rel="stylesheet" href="/css/brand.css">
<style>
<?php include __DIR__ . '/print.css.php'; ?>
</style>
</head>
<body class="invoice-screen">

<?php if (!$public): ?>
<div class="toolbar">
    <?php if (!empty($err)): ?>
        <span class="muted" style="color: #fca5a5;"><?= e((string)$err) ?></span>
    <?php endif; ?>
    <a href="/admin/invoices/<?= (int)$invoice['id'] ?>/edit" class="btn secondary">Edit</a>
    <a href="/admin" class="btn secondary">&larr; Invoices</a>
    <?php if (!empty($invoice['signed_at'])): ?>
        <form method="post" action="/admin/invoices/<?= (int)$invoice['id'] ?>/unsign" style="display:inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn secondary" onclick="return confirm('Remove signature?')">Unsign</button>
        </form>
    <?php else: ?>
        <form method="post" action="/admin/invoices/<?= (int)$invoice['id'] ?>/sign" style="display:inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn primary">Sign invoice</button>
        </form>
    <?php endif; ?>
    <?php if ($shareToken): ?>
        <form method="post" action="/admin/invoices/<?= (int)$invoice['id'] ?>/revoke" style="display:inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn secondary" onclick="return confirm('Revoke public link?')">Revoke link</button>
        </form>
    <?php else: ?>
        <form method="post" action="/admin/invoices/<?= (int)$invoice['id'] ?>/share" style="display:inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn secondary">Public link</button>
        </form>
    <?php endif; ?>
    <a href="/admin/invoices/<?= (int)$invoice['id'] ?>/pdf" class="btn primary">PDF</a>
    <button onclick="window.print()" class="btn primary">Print</button>
</div>

<?php if ($shareToken): ?>
    <div style="max-width: 800px; margin: 12px auto 0;">
        <div class="share-box">
            <strong style="font-family: var(--mono); font-size: 12px;">Public link:</strong>
            <input type="text" readonly value="<?= e(($_SERVER['HTTP_HOST'] ?? 'localhost') ? 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/i/' . $shareToken : '/i/' . $shareToken) ?>" onclick="this.select()">
        </div>
    </div>
<?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/_body.php'; ?>

</body>
</html>
