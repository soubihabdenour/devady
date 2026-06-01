<?php
/** @var App\Kernel $kernel */
/** @var array $clients */
/** @var ?string $error */

ob_start();
?>
<div class="toolbar">
    <h1><?= e(t('cl_title')) ?></h1>
    <a href="/admin/clients/new" class="btn primary"><?= e(t('cl_new')) ?></a>
</div>

<?php if (!empty($error)): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>

<div class="card" style="padding: 0;">
    <?php if (empty($clients)): ?>
        <div class="empty"><p><?= e(t('cl_empty')) ?></p></div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th><?= e(t('cl_col_name')) ?></th><th><?= e(t('cl_col_country')) ?></th><th><?= e(t('cl_col_email')) ?></th><th><?= e(t('cl_col_tax')) ?></th></tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                    <tr style="cursor: pointer;" onclick="location.href='/admin/clients/<?= (int)$c['id'] ?>/edit'">
                        <td><strong><?= e($c['name']) ?></strong></td>
                        <td><?= e($c['country'] ?? '—') ?></td>
                        <td class="ltr-force"><?= e($c['email'] ?? '—') ?></td>
                        <td class="ltr-force"><?= e($c['tax_id'] ?? $c['business_reg_no'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$body = ob_get_clean();
$title = t('cl_title');
$current = 'clients';
require __DIR__ . '/../layouts/admin.php';
