<?php
/** @var App\Kernel $kernel */
/** @var array $settings */
/** @var bool $saved */
/** @var ?string $error */

$nextSeq = (int)$settings['last_invoice_seq'] + 1;
$nextNumber = sprintf(
    '%s-%s-%0' . max(1, (int)$settings['invoice_padding']) . 'd',
    $settings['invoice_prefix'] ?: 'INV',
    date('Y'),
    $nextSeq
);
$cacheBust = (string)time();
ob_start();
?>
<div class="toolbar">
    <h1><?= e(t('set_title')) ?></h1>
</div>

<?php if (!empty($error)): ?><div class="alert err" style="margin-bottom: 16px;"><?= e($error) ?></div><?php endif; ?>
<?php if (!empty($saved) && empty($error)): ?><div class="alert ok" style="margin-bottom: 16px;"><?= e(t('set_saved')) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" action="/admin/settings" id="settings-form">
    <?= csrf_field() ?>
    <div class="card">
        <h2><?= e(t('set_issuer_h')) ?></h2>
        <div class="row">
            <div>
                <label><?= e(t('set_company')) ?></label>
                <input type="text" name="company_name" value="<?= e($settings['company_name']) ?>">
            </div>
            <div>
                <label><?= e(t('set_legal_name')) ?></label>
                <input type="text" name="company_legal_name" value="<?= e($settings['company_legal_name']) ?>">
            </div>
        </div>

        <label><?= e(t('set_address')) ?></label>
        <textarea name="company_address" rows="3"><?= e($settings['company_address']) ?></textarea>

        <div class="row three">
            <div>
                <label><?= e(t('set_city')) ?></label>
                <input type="text" name="company_city" value="<?= e($settings['company_city']) ?>">
            </div>
            <div>
                <label><?= e(t('set_email')) ?></label>
                <input type="email" name="company_email" value="<?= e($settings['company_email']) ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('set_phone')) ?></label>
                <input type="text" name="company_phone" value="<?= e($settings['company_phone']) ?>" dir="ltr">
            </div>
        </div>

        <div class="row three">
            <div>
                <label><?= e(t('set_tax_id')) ?></label>
                <input type="text" name="company_tax_id" value="<?= e($settings['company_tax_id']) ?>" dir="ltr" maxlength="15">
            </div>
            <div>
                <label><?= e(t('set_anae')) ?></label>
                <input type="text" name="company_anae_no" value="<?= e($settings['company_anae_no']) ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('set_activity_code')) ?></label>
                <input type="text" name="company_activity_code" value="<?= e($settings['company_activity_code']) ?>" dir="ltr">
            </div>
        </div>

        <label><?= e(t('set_activity_label')) ?></label>
        <input type="text" name="company_activity_label" value="<?= e($settings['company_activity_label']) ?>">
    </div>

    <div class="card">
        <h2><?= e(t('set_bank_h')) ?></h2>
        <div class="row">
            <div>
                <label><?= e(t('set_bank_name')) ?></label>
                <input type="text" name="company_bank_name" value="<?= e($settings['company_bank_name']) ?>">
            </div>
            <div>
                <label><?= e(t('set_rib')) ?></label>
                <input type="text" name="company_rib" value="<?= e($settings['company_rib']) ?>" dir="ltr">
            </div>
        </div>
        <div class="row">
            <div>
                <label><?= e(t('set_iban')) ?></label>
                <input type="text" name="company_iban" value="<?= e($settings['company_iban']) ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('set_swift')) ?></label>
                <input type="text" name="company_swift" value="<?= e($settings['company_swift']) ?>" dir="ltr">
            </div>
        </div>
        <label><?= e(t('set_domiciliation')) ?></label>
        <input type="text" name="company_domiciliation" value="<?= e($settings['company_domiciliation']) ?>" dir="ltr">
        <div class="muted" style="font-size: 12px; margin-top: 6px;"><?= e(t('set_dom_help')) ?></div>

        <div class="row">
            <div>
                <label><?= e(t('set_beneficiary_address')) ?></label>
                <textarea name="company_beneficiary_address" rows="2"><?= e($settings['company_beneficiary_address']) ?></textarea>
            </div>
            <div>
                <label><?= e(t('set_account_currency')) ?></label>
                <input type="text" name="company_account_currency" maxlength="3" value="<?= e($settings['company_account_currency']) ?>" dir="ltr">
            </div>
        </div>
    </div>

    <div class="card">
        <h2><?= e(t('set_sig_h')) ?></h2>
        <p class="muted" style="font-size: 13px; margin-bottom: 14px;"><?= e(t('set_sig_help')) ?></p>

        <div class="row">
            <div>
                <label><?= e(t('set_sig_image')) ?></label>
                <?php if (!empty($settings['company_signature_file'])): ?>
                    <div class="image-preview">
                        <img src="/asset?type=signature&v=<?= e($cacheBust) ?>" alt="signature">
                    </div>
                    <label style="text-transform: none; letter-spacing: 0; font-family: var(--sans); font-size: 13px; color: var(--text-dim); display: flex; align-items: center; gap: 6px;">
                        <input type="checkbox" name="remove_signature" value="1" style="width: auto;">
                        <?= e(t('set_sig_remove')) ?>
                    </label>
                <?php endif; ?>
                <input type="file" name="signature_upload" accept="image/png,image/jpeg">

                <label style="margin-top: 18px;"><?= e(t('set_sig_draw')) ?></label>
                <canvas id="sig-pad" class="sig-pad" width="600" height="160"></canvas>
                <input type="hidden" name="signature_drawing" id="sig-drawing">
                <div class="sig-actions">
                    <button type="button" id="sig-clear" class="btn secondary sm"><?= e(t('set_sig_clear')) ?></button>
                </div>
            </div>

            <div>
                <label><?= e(t('set_stamp_image')) ?></label>
                <?php if (!empty($settings['company_stamp_file'])): ?>
                    <div class="image-preview">
                        <img src="/asset?type=stamp&v=<?= e($cacheBust) ?>" alt="stamp">
                    </div>
                    <label style="text-transform: none; letter-spacing: 0; font-family: var(--sans); font-size: 13px; color: var(--text-dim); display: flex; align-items: center; gap: 6px;">
                        <input type="checkbox" name="remove_stamp" value="1" style="width: auto;">
                        <?= e(t('set_stamp_remove')) ?>
                    </label>
                <?php endif; ?>
                <input type="file" name="stamp_upload" accept="image/png,image/jpeg">
            </div>
        </div>
    </div>

    <div class="card">
        <h2><?= e(t('set_defaults_h')) ?></h2>
        <div class="row three">
            <div>
                <label><?= e(t('set_currency')) ?></label>
                <input type="text" name="currency" maxlength="3" value="<?= e($settings['currency']) ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('set_def_tax_rate')) ?></label>
                <input type="number" step="0.01" min="0" name="default_tax_rate" value="<?= e($settings['default_tax_rate']) ?>" dir="ltr">
            </div>
            <div>
                <label><?= e(t('set_prefix')) ?></label>
                <input type="text" name="invoice_prefix" value="<?= e($settings['invoice_prefix']) ?>" dir="ltr">
            </div>
        </div>
        <label><?= e(t('set_padding')) ?></label>
        <input type="number" min="1" max="8" name="invoice_padding" value="<?= e($settings['invoice_padding']) ?>" style="width: 120px;" dir="ltr">
        <div class="muted" style="margin-top: 12px; font-size: 13px;">
            <?= e(t('set_next_invoice', ['n' => $nextNumber])) ?>
        </div>
    </div>

    <button type="submit" class="btn primary lg"><?= e(t('set_save')) ?></button>
</form>

<script>
(function() {
    var canvas = document.getElementById('sig-pad');
    var hidden = document.getElementById('sig-drawing');
    var clear  = document.getElementById('sig-clear');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var drawing = false;
    var hasInk = false;

    // High-DPI render
    function fit() {
        var ratio = window.devicePixelRatio || 1;
        var w = canvas.clientWidth, h = canvas.clientHeight;
        canvas.width = w * ratio;
        canvas.height = h * ratio;
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineWidth = 2.2;
        ctx.strokeStyle = '#0a0e1d';
    }
    fit();
    window.addEventListener('resize', fit);

    function pos(ev) {
        var rect = canvas.getBoundingClientRect();
        var p = ev.touches ? ev.touches[0] : ev;
        return { x: p.clientX - rect.left, y: p.clientY - rect.top };
    }
    function start(ev) { ev.preventDefault(); drawing = true; hasInk = true; var p = pos(ev); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
    function move(ev) { if (!drawing) return; ev.preventDefault(); var p = pos(ev); ctx.lineTo(p.x, p.y); ctx.stroke(); }
    function end()   { if (!drawing) return; drawing = false; if (hasInk) hidden.value = canvas.toDataURL('image/png'); }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    canvas.addEventListener('mouseup', end);
    canvas.addEventListener('mouseleave', end);
    canvas.addEventListener('touchstart', start);
    canvas.addEventListener('touchmove', move);
    canvas.addEventListener('touchend', end);

    clear.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hidden.value = '';
        hasInk = false;
    });
})();
</script>
<?php
$body = ob_get_clean();
$title = t('set_title');
$current = 'settings';
require __DIR__ . '/../layouts/admin.php';
