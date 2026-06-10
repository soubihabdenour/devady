<?php
/** @var App\Kernel $kernel */
/** @var array $items */

ob_start();
?>
<div class="toolbar">
    <h1><?= e(t('pf_title')) ?></h1>
    <a href="/admin/portfolio/new" class="btn primary"><?= e(t('pf_new')) ?></a>
</div>

<div class="card" style="padding: 0;">
    <?php if (empty($items)): ?>
        <div class="empty"><p><?= e(t('pf_empty')) ?></p></div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;"><?= e(t('pf_col_pos')) ?></th>
                    <th><?= e(t('pf_col_title')) ?></th>
                    <th><?= e(t('pf_col_role')) ?></th>
                    <th><?= e(t('pf_col_period')) ?></th>
                    <th><?= e(t('pf_col_tags')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $p): ?>
                    <tr style="cursor: pointer;" onclick="location.href='/admin/portfolio/<?= (int)$p['id'] ?>/edit'">
                        <td class="mono"><?= (int)$p['position'] ?></td>
                        <td><strong><?= e((string)$p['title']) ?></strong></td>
                        <td><?= e((string)($p['role'] ?? '—')) ?></td>
                        <td class="ltr-force"><?= e((string)($p['period'] ?? '—')) ?></td>
                        <td><?= e(implode(', ', $p['tags'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php
$body = ob_get_clean();
$title = t('pf_title');
$current = 'portfolio';
require __DIR__ . '/../layouts/admin.php';
