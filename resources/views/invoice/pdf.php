<?php
/** @var App\Kernel $kernel */
/** @var array $invoice */
/** @var ?array $client */
/** @var array $company */

$pdfMode = true;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice <?= e($invoice['number']) ?></title>
<style>
<?php include __DIR__ . '/print.css.php'; ?>
body { background: white !important; }
.page { margin: 0; padding: 0; border: 0; max-width: none; }
</style>
</head>
<body>
<?php include __DIR__ . '/_body.php'; ?>
</body>
</html>
