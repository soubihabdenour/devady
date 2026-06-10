<?php
/** @var App\Kernel $kernel */
/** @var ?array $item */
/** @var ?string $error */

$action = $item && !empty($item['id'])
    ? '/admin/portfolio/' . (int)$item['id'] . '/edit'
    : '/admin/portfolio/new';

$tagsValue = '';
if ($item && !empty($item['tags'])) {
    $tagsValue = is_array($item['tags']) ? implode(', ', $item['tags']) : (string)$item['tags'];
}

ob_start();
?>
<div class="toolbar">
    <h1><?= e($item && !empty($item['id']) ? t('pf_edit_title') : t('pf_new_title')) ?></h1>
    <a href="/admin/portfolio" class="muted"><?= e(t('pf_back')) ?></a>
</div>

<?php if (!empty($error)): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>

<div class="card">
    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>

        <label><?= e(t('pf_form_title')) ?> *</label>
        <input type="text" name="title" required maxlength="200" value="<?= e($item['title'] ?? '') ?>">

        <div class="row three">
            <div>
                <label><?= e(t('pf_form_period')) ?></label>
                <input type="text" name="period" maxlength="60" value="<?= e($item['period'] ?? '') ?>" dir="ltr" placeholder="2026">
            </div>
            <div>
                <label><?= e(t('pf_form_role')) ?></label>
                <input type="text" name="role" maxlength="120" value="<?= e($item['role'] ?? '') ?>" placeholder="Builder">
            </div>
            <div>
                <label><?= e(t('pf_form_position')) ?></label>
                <input type="number" name="position" value="<?= e((string)($item['position'] ?? 0)) ?>" dir="ltr" style="width: 120px;">
            </div>
        </div>

        <label><?= e(t('pf_form_body')) ?></label>
        <textarea name="body" rows="4" maxlength="2000"><?= e($item['body'] ?? '') ?></textarea>

        <label><?= e(t('pf_form_tags')) ?></label>
        <input type="text" name="tags" value="<?= e($tagsValue) ?>" placeholder="TypeScript, LLM, SSE">
        <div class="muted" style="font-size: 12px; margin-top: 6px;"><?= e(t('pf_form_tags_help')) ?></div>

        <label><?= e(t('pf_form_url')) ?></label>
        <input type="url" name="url" maxlength="500" value="<?= e($item['url'] ?? '') ?>" dir="ltr" placeholder="https://github.com/...">

        <div style="display: flex; gap: 8px; margin-top: 20px;">
            <button type="submit" class="btn primary"><?= e(t('pf_save')) ?></button>
            <a href="/admin/portfolio" class="btn secondary"><?= e(t('pf_cancel')) ?></a>
            <?php if ($item && !empty($item['id'])): ?>
                <div style="flex: 1;"></div>
                <button type="submit" form="delete-form" class="btn danger"
                        onclick="return confirm(<?= json_encode(t('pf_delete_confirm')) ?>);"><?= e(t('pf_delete')) ?></button>
            <?php endif; ?>
        </div>
    </form>
    <?php if ($item && !empty($item['id'])): ?>
        <form id="delete-form" method="post" action="/admin/portfolio/<?= (int)$item['id'] ?>/delete" style="display:none;"><?= csrf_field() ?></form>
    <?php endif; ?>
</div>
<?php
$body = ob_get_clean();
$title = $item && !empty($item['id']) ? t('pf_edit_title') : t('pf_new_title');
$current = 'portfolio';
require __DIR__ . '/../layouts/admin.php';
