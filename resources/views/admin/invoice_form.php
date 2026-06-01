<?php
/** @var App\Kernel $kernel */
/** @var ?array $invoice */
/** @var array $allClients */
/** @var array $companyDefaults */
/** @var ?string $error */

$lines = $invoice['lines'] ?? [];
$defaultTax = (float)($companyDefaults['default_tax_rate'] ?? 0);
if (empty($lines)) {
    $lines = [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => $defaultTax]];
}
$action = $invoice ? '/admin/invoices/' . (int)$invoice['id'] . '/edit' : '/admin/invoices/new';
ob_start();
?>
<div class="toolbar">
    <h1><?= $invoice ? e(t('inv_edit')) . ' ' . e($invoice['number']) : e(t('inv_new')) ?></h1>
    <a href="/admin" class="muted"><?= e(t('inv_back')) ?></a>
</div>

<?php if (!empty($error)): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>

<?php if (empty($allClients)): ?>
    <div class="alert warn" style="margin-bottom: 16px;">
        <?= e(t('inv_no_clients')) ?>
        <a href="/admin/clients/new" class="btn primary sm" style="margin-inline-start: 8px;"><?= e(t('cl_new')) ?></a>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" id="invoice-form">
    <div class="card">
        <div class="row three">
            <div>
                <label><?= e(t('inv_form_client')) ?> *</label>
                <select name="client_id" required>
                    <option value="">—</option>
                    <?php foreach ($allClients as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= isset($invoice['client_id']) && (int)$invoice['client_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><?= e(t('inv_form_issue')) ?> *</label>
                <input type="date" name="issue_date" required value="<?= e($invoice['issue_date'] ?? date('Y-m-d')) ?>">
            </div>
            <div>
                <label><?= e(t('inv_form_due')) ?></label>
                <input type="date" name="due_date" value="<?= e($invoice['due_date'] ?? '') ?>">
            </div>
        </div>

        <div class="row three">
            <div>
                <label><?= e(t('inv_form_status')) ?></label>
                <select name="status">
                    <?php foreach (['draft','sent','paid','void'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($invoice['status'] ?? 'draft') === $st ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><?= e(t('inv_form_currency')) ?></label>
                <input type="text" name="currency" maxlength="3" value="<?= e($invoice['currency'] ?? $companyDefaults['currency']) ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('inv_form_place')) ?></label>
                <input type="text" name="place_of_issue" value="<?= e($invoice['place_of_issue'] ?? $companyDefaults['company_city']) ?>">
            </div>
        </div>

        <div class="row">
            <div>
                <label style="display: flex; align-items: center; gap: 8px; text-transform: none; letter-spacing: 0; font-family: var(--sans);">
                    <input type="checkbox" name="is_export" value="1" <?= !empty($invoice['is_export']) ? 'checked' : '' ?> style="width: auto;">
                    <span><?= e(t('inv_form_export')) ?></span>
                </label>
            </div>
            <div>
                <label><?= e(t('inv_form_treaty')) ?></label>
                <input type="text" name="treaty_country" value="<?= e($invoice['treaty_country'] ?? '') ?>">
            </div>
        </div>

        <label><?= e(t('inv_form_words')) ?></label>
        <input type="text" name="amount_in_words" value="<?= e($invoice['amount_in_words'] ?? '') ?>">
    </div>

    <div class="card">
        <h2><?= e(t('inv_form_lines')) ?></h2>
        <table id="lines" class="lines-table">
            <thead>
                <tr>
                    <th><?= e(t('inv_form_desc')) ?></th>
                    <th class="num" style="width: 90px;"><?= e(t('inv_form_qty')) ?></th>
                    <th class="num" style="width: 130px;"><?= e(t('inv_form_price')) ?></th>
                    <th class="num" style="width: 90px;"><?= e(t('inv_form_tax')) ?></th>
                    <th class="num" style="width: 130px;"><?= e(t('inv_form_total')) ?></th>
                    <th style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as $idx => $line): ?>
                    <tr>
                        <td><input type="text" name="lines[<?= $idx ?>][description]" value="<?= e($line['description'] ?? '') ?>"></td>
                        <td><input type="number" step="0.01" min="0" class="qty"   name="lines[<?= $idx ?>][quantity]"   value="<?= e((string)($line['quantity']   ?? 1)) ?>" dir="ltr"></td>
                        <td><input type="number" step="0.01" min="0" class="price" name="lines[<?= $idx ?>][unit_price]" value="<?= e((string)($line['unit_price'] ?? 0)) ?>" dir="ltr"></td>
                        <td><input type="number" step="0.01" min="0" class="tax"   name="lines[<?= $idx ?>][tax_rate]"   value="<?= e((string)($line['tax_rate']   ?? $defaultTax)) ?>" dir="ltr"></td>
                        <td class="num line-total">0.00</td>
                        <td><button type="button" class="btn secondary sm remove-row" style="padding: 4px 10px;">×</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" class="btn secondary sm" id="add-row" style="margin-top: 12px;">+ <?= e(t('inv_form_add_line')) ?></button>

        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            <table class="lines-totals">
                <tr><td><?= e(t('inv_form_subtotal')) ?></td><td id="subtotal">0.00</td></tr>
                <tr><td><?= e(t('inv_form_taxsum')) ?></td><td id="tax-total">0.00</td></tr>
                <tr class="grand"><td><?= e(t('inv_form_grand')) ?></td><td id="grand-total">0.00</td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <label><?= e(t('inv_form_notes')) ?></label>
        <textarea name="notes" rows="3"><?= e($invoice['notes'] ?? '') ?></textarea>
    </div>

    <div style="display: flex; gap: 8px;">
        <button type="submit" class="btn primary"><?= e(t('inv_save')) ?></button>
        <a href="<?= $invoice ? '/admin/invoices/' . (int)$invoice['id'] : '/admin' ?>" class="btn secondary"><?= e(t('inv_cancel')) ?></a>
        <?php if ($invoice): ?>
            <div style="flex: 1;"></div>
            <a href="/admin/invoices/<?= (int)$invoice['id'] ?>" class="btn secondary"><?= e(t('inv_view_print')) ?></a>
            <button type="submit" form="delete-form" class="btn danger" onclick="return confirm(<?= json_encode(t('inv_delete_confirm')) ?>);"><?= e(t('inv_delete')) ?></button>
        <?php endif; ?>
    </div>
</form>

<?php if ($invoice): ?>
    <form id="delete-form" method="post" action="/admin/invoices/<?= (int)$invoice['id'] ?>/delete" style="display:none;"></form>
<?php endif; ?>

<script>
(function () {
    var defaultTax = <?= json_encode($defaultTax) ?>;
    var tbody = document.querySelector('#lines tbody');
    var nextIdx = tbody.querySelectorAll('tr').length;

    function recompute() {
        var sub = 0, tax = 0;
        tbody.querySelectorAll('tr').forEach(function (row) {
            var q = parseFloat(row.querySelector('.qty').value)   || 0;
            var p = parseFloat(row.querySelector('.price').value) || 0;
            var t = parseFloat(row.querySelector('.tax').value)   || 0;
            var lineSub = Math.round(q * p * 100) / 100;
            var lineTax = Math.round(lineSub * t) / 100;
            row.querySelector('.line-total').textContent = (lineSub + lineTax).toFixed(2);
            sub += lineSub; tax += lineTax;
        });
        document.getElementById('subtotal').textContent    = sub.toFixed(2);
        document.getElementById('tax-total').textContent   = tax.toFixed(2);
        document.getElementById('grand-total').textContent = (sub + tax).toFixed(2);
    }
    tbody.addEventListener('input', recompute);
    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            if (tbody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
                recompute();
            }
        }
    });
    document.getElementById('add-row').addEventListener('click', function () {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><input type="text" name="lines[' + nextIdx + '][description]"></td>' +
            '<td><input type="number" step="0.01" min="0" class="qty" name="lines[' + nextIdx + '][quantity]" value="1" dir="ltr"></td>' +
            '<td><input type="number" step="0.01" min="0" class="price" name="lines[' + nextIdx + '][unit_price]" value="0" dir="ltr"></td>' +
            '<td><input type="number" step="0.01" min="0" class="tax" name="lines[' + nextIdx + '][tax_rate]" value="' + defaultTax + '" dir="ltr"></td>' +
            '<td class="num line-total">0.00</td>' +
            '<td><button type="button" class="btn secondary sm remove-row">×</button></td>';
        tbody.appendChild(tr);
        nextIdx++;
        recompute();
    });
    recompute();
})();
</script>
<?php
$body = ob_get_clean();
$title = $invoice ? t('inv_edit') : t('inv_new');
$current = 'invoices';
require __DIR__ . '/../layouts/admin.php';
