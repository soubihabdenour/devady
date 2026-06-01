<?php
/** @var App\Kernel $kernel */
/** @var ?array $client */
/** @var ?string $error */

$action = $client && !empty($client['id']) ? '/admin/clients/' . (int)$client['id'] . '/edit' : '/admin/clients/new';
ob_start();
?>
<div class="toolbar">
    <h1><?= e($client && !empty($client['id']) ? t('cl_edit_title') : t('cl_new_title')) ?></h1>
    <a href="/admin/clients" class="muted"><?= e(t('cl_back')) ?></a>
</div>

<?php if (!empty($error)): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>

<div class="card">
    <form method="post" action="<?= e($action) ?>">
        <label><?= e(t('cl_form_company')) ?> *</label>
        <input type="text" name="name" required value="<?= e($client['name'] ?? '') ?>">

        <div class="row">
            <div>
                <label><?= e(t('cl_form_contact')) ?></label>
                <input type="text" name="contact_name" value="<?= e($client['contact_name'] ?? '') ?>">
            </div>
            <div>
                <label><?= e(t('cl_form_country')) ?></label>
                <input type="text" name="country" value="<?= e($client['country'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div>
                <label><?= e(t('cl_form_email')) ?></label>
                <input type="email" name="email" value="<?= e($client['email'] ?? '') ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('cl_form_phone')) ?></label>
                <input type="text" name="phone" value="<?= e($client['phone'] ?? '') ?>" dir="ltr">
            </div>
        </div>

        <label><?= e(t('cl_form_address')) ?></label>
        <textarea name="address" rows="3"><?= e($client['address'] ?? '') ?></textarea>

        <div class="row">
            <div>
                <label><?= e(t('cl_form_tax')) ?></label>
                <input type="text" name="tax_id" value="<?= e($client['tax_id'] ?? '') ?>">
            </div>
            <div>
                <label><?= e(t('cl_form_brn')) ?></label>
                <input type="text" name="business_reg_no" value="<?= e($client['business_reg_no'] ?? '') ?>">
            </div>
        </div>

        <label><?= e(t('cl_form_notes')) ?></label>
        <textarea name="notes" rows="2"><?= e($client['notes'] ?? '') ?></textarea>

        <div style="display: flex; gap: 8px; margin-top: 20px;">
            <button type="submit" class="btn primary"><?= e(t('cl_save')) ?></button>
            <a href="/admin/clients" class="btn secondary"><?= e(t('cl_cancel')) ?></a>
            <?php if ($client && !empty($client['id'])): ?>
                <div style="flex: 1;"></div>
                <button type="submit" form="delete-form" class="btn danger"
                        onclick="return confirm(<?= json_encode(t('cl_delete_confirm')) ?>);"><?= e(t('cl_delete')) ?></button>
            <?php endif; ?>
        </div>
    </form>
    <?php if ($client && !empty($client['id'])): ?>
        <form id="delete-form" method="post" action="/admin/clients/<?= (int)$client['id'] ?>/delete" style="display:none;"></form>
    <?php endif; ?>
</div>
<?php
$body = ob_get_clean();
$title = $client && !empty($client['id']) ? t('cl_edit_title') : t('cl_new_title');
$current = 'clients';
require __DIR__ . '/../layouts/admin.php';
