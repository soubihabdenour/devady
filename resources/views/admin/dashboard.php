<?php
/** @var App\Kernel $kernel */
/** @var array $invoices */
/** @var array $totals */

ob_start();
?>
<div class="toolbar">
    <h1><?= e(t('admin_nav_invoices')) ?></h1>
    <a href="/admin/invoices/new" class="btn primary"><?= e(t('dash_new')) ?></a>
</div>

<div class="stats">
    <div class="stat">
        <div class="label"><?= e(t('dash_billed')) ?></div>
        <div class="value"><?= money((float)$totals['billed']) ?></div>
    </div>
    <div class="stat good">
        <div class="label"><?= e(t('dash_paid')) ?></div>
        <div class="value"><?= money((float)$totals['paid']) ?></div>
    </div>
    <div class="stat warn">
        <div class="label"><?= e(t('dash_outstanding')) ?></div>
        <div class="value"><?= money((float)$totals['outstanding']) ?></div>
    </div>
</div>

<div class="card" style="padding: 0;">
    <?php if (empty($invoices)): ?>
        <div class="empty">
            <p><?= e(t('dash_empty')) ?></p>
            <a href="/admin/invoices/new" class="btn primary" style="margin-top: 14px;"><?= e(t('dash_create_first')) ?></a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?= e(t('dash_col_number')) ?></th>
                    <th><?= e(t('dash_col_client')) ?></th>
                    <th><?= e(t('dash_col_issue')) ?></th>
                    <th><?= e(t('dash_col_due')) ?></th>
                    <th><?= e(t('dash_col_status')) ?></th>
                    <th class="num"><?= e(t('dash_col_total')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $i): ?>
                    <tr style="cursor: pointer;" onclick="location.href='/admin/invoices/<?= (int)$i['id'] ?>'">
                        <td><strong class="mono"><?= e($i['number']) ?></strong></td>
                        <td><?= e($i['client_name'] ?? '—') ?></td>
                        <td><?= e($i['issue_date']) ?></td>
                        <td><?= e($i['due_date'] ?? '—') ?></td>
                        <td>
                            <span class="status <?= e($i['status']) ?>"><?= e($i['status']) ?></span>
                            <?php if (!empty($i['signed_at'])): ?>
                                <span style="margin-inline-start: 6px; font-size: 11px; color: var(--accent-3);" title="<?= e(date('Y-m-d H:i', (int)$i['signed_at'])) ?>">&#10003; <?= e(t('dash_signed')) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="num"><?= money((float)$i['total'], $i['currency']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$body = ob_get_clean();
$title = t('admin_nav_invoices');
$current = 'invoices';
require __DIR__ . '/../layouts/admin.php';
