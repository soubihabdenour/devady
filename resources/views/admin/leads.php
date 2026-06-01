<?php
/** @var App\Kernel $kernel */
/** @var array $leads */
/** @var ?string $error */

ob_start();
?>
<div class="toolbar">
    <h1><?= e(t('leads_title')) ?></h1>
    <span class="muted"><?= e(t('leads_total', ['count' => count($leads)])) ?></span>
</div>

<?php if (!empty($error)): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>

<div class="card" style="padding: 0;">
    <?php if (empty($leads)): ?>
        <div class="empty"><p><?= e(t('leads_empty')) ?></p></div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 18px;"></th>
                    <th><?= e(t('leads_col_from')) ?></th>
                    <th><?= e(t('leads_col_subj')) ?></th>
                    <th><?= e(t('leads_col_when')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead): $unread = empty($lead['read_at']); ?>
                    <tr style="cursor: pointer;" onclick="location.href='/admin/leads/<?= (int)$lead['id'] ?>'">
                        <td><?php if ($unread): ?><span class="unread-dot" title="<?= e(t('leads_unread')) ?>"></span><?php endif; ?></td>
                        <td>
                            <strong style="<?= $unread ? 'font-weight: 700' : 'font-weight: 500' ?>"><?= e($lead['name']) ?></strong>
                            <div class="muted ltr-force" style="font-size: 12px;"><?= e($lead['email'] ?? '') ?></div>
                        </td>
                        <td>
                            <?php if (!empty($lead['converted_client_id'])): ?>
                                <span class="status paid" style="margin-inline-end: 6px;"><?= e(t('leads_converted')) ?></span>
                            <?php endif; ?>
                            <?= e($lead['subject'] ?? '—') ?>
                        </td>
                        <td class="muted" style="font-size: 13px;"><?= e(date('Y-m-d H:i', (int)$lead['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$body = ob_get_clean();
$title = t('leads_title');
$current = 'leads';
require __DIR__ . '/../layouts/admin.php';
