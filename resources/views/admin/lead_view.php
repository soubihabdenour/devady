<?php
/** @var App\Kernel $kernel */
/** @var array $lead */

$converted = !empty($lead['converted_client_id']);
ob_start();
?>
<div class="toolbar">
    <h1><?= e($lead['name']) ?></h1>
    <a href="/admin/leads" class="muted"><?= e(t('leads_view_back')) ?></a>
</div>

<div class="card">
    <div class="row">
        <div>
            <label><?= e(t('leads_from')) ?></label>
            <div><strong><?= e($lead['name']) ?></strong></div>
            <div class="muted ltr-force">
                <?php if (!empty($lead['email'])): ?><a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a><?php endif; ?>
            </div>
        </div>
        <div>
            <label><?= e(t('leads_received')) ?></label>
            <div><?= e(date('Y-m-d H:i', (int)$lead['created_at'])) ?></div>
            <?php if (!empty($lead['ip'])): ?>
                <div class="muted ltr-force" style="font-size: 12px;">IP: <?= e($lead['ip']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($lead['subject'])): ?>
        <label><?= e(t('leads_subject')) ?></label>
        <div style="font-weight: 500;"><?= e($lead['subject']) ?></div>
    <?php endif; ?>

    <label><?= e(t('leads_message')) ?></label>
    <div style="white-space: pre-wrap; line-height: 1.6; padding: 16px; background: var(--bg-2); border: 1px solid var(--border); border-radius: 8px;">
        <?= e($lead['message'] ?? '') ?>
    </div>

    <div style="margin-top: 24px; display: flex; gap: 8px; align-items: center;">
        <?php if ($converted): ?>
            <a href="/admin/clients/<?= (int)$lead['converted_client_id'] ?>/edit" class="btn primary"><?= e(t('leads_open_client')) ?></a>
            <span class="muted"><?= e(t('leads_already_conv')) ?></span>
        <?php else: ?>
            <form method="post" action="/admin/leads/<?= (int)$lead['id'] ?>/convert" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn primary"><?= e(t('leads_convert')) ?></button>
            </form>
        <?php endif; ?>
        <div style="flex: 1;"></div>
        <form method="post" action="/admin/leads/<?= (int)$lead['id'] ?>/delete" style="display:inline;"
              onsubmit="return confirm(<?= json_encode(t('leads_delete_confirm')) ?>);">
            <?= csrf_field() ?>
            <button type="submit" class="btn danger"><?= e(t('leads_delete')) ?></button>
        </form>
    </div>
</div>
<?php
$body = ob_get_clean();
$title = t('leads_title');
$current = 'leads';
require __DIR__ . '/../layouts/admin.php';
