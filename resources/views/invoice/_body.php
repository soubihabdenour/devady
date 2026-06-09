<?php
/**
 * Shared invoice content — used by both the print view and the PDF.
 * Expects: $invoice, $client, $company.
 */
$isExport      = !empty($invoice['is_export']);
$treaty        = trim((string)($invoice['treaty_country'] ?? ''));
$amountInWords = (string)($invoice['amount_in_words'] ?? '');
if ($amountInWords === '') {
    $amountInWords = amount_in_words((float)$invoice['total'], $invoice['currency']);
}
$placeOfIssue = $invoice['place_of_issue'] ?: ($company['company_city'] ?? '');
$issuerName   = $company['company_name'] ?: 'Your company';
$legalRep     = $company['company_legal_name'] ?? '';
$signedAt     = !empty($invoice['signed_at']) ? (int)$invoice['signed_at'] : 0;
$isSigned     = $signedAt > 0;
$signedDate   = $isSigned ? date('Y-m-d H:i', $signedAt) : '';

$hasBank = !empty($company['company_bank_name']) || !empty($company['company_rib'])
    || !empty($company['company_iban']) || !empty($company['company_swift'])
    || !empty($company['company_domiciliation'])
    || !empty($company['company_beneficiary_address'])
    || !empty($company['company_account_currency']);
?>
<div class="page">
    <div class="header-row">
        <div class="company">
            <div class="name"><?= e($issuerName) ?></div>
            <?php if ($legalRep): ?><div class="legal-line">Represented by: <?= e($legalRep) ?></div><?php endif; ?>
            <?= nl2br(e($company['company_address'] ?? '')) ?>
            <?php if (!empty($company['company_email'])): ?><br><?= e($company['company_email']) ?><?php endif; ?>
            <?php if (!empty($company['company_phone'])): ?><br>Phone: <?= e($company['company_phone']) ?><?php endif; ?>
            <div class="legal-line" style="margin-top: 6px;">
                <?php if (!empty($company['company_tax_id'])): ?>Tax ID (NIF): <?= e($company['company_tax_id']) ?><br><?php endif; ?>
                <?php if (!empty($company['company_anae_no'])): ?>ANAE Card No.: <?= e($company['company_anae_no']) ?><br><?php endif; ?>
                <?php if (!empty($company['company_activity_code'])): ?>Activity code: <?= e($company['company_activity_code']) ?><?php if (!empty($company['company_activity_label'])): ?> — <?= e($company['company_activity_label']) ?><?php endif; ?><?php endif; ?>
            </div>
        </div>
        <div class="invoice-title">
            <?php if (in_array($invoice['status'], ['paid','void','draft','sent'], true)): ?>
                <span class="status-banner <?= e($invoice['status']) ?>"><?= e($invoice['status']) ?></span><br>
            <?php endif; ?>
            <h1>INVOICE</h1>
            <div class="number">No. <?= e($invoice['number']) ?></div>
        </div>
    </div>

    <div class="meta-row">
        <div>
            <h3>Bill to</h3>
            <div class="body">
                <strong><?= e($client['name'] ?? '—') ?></strong><br>
                <?php if (!empty($client['contact_name'])): ?>Attn: <?= e($client['contact_name']) ?><br><?php endif; ?>
                <?= nl2br(e($client['address'] ?? '')) ?>
                <?php if (!empty($client['country'])): ?><br><?= e($client['country']) ?><?php endif; ?>
                <?php if (!empty($client['email'])): ?><br><?= e($client['email']) ?><?php endif; ?>
                <?php if (!empty($client['business_reg_no'])): ?><br>Business Reg. No.: <?= e($client['business_reg_no']) ?><?php endif; ?>
                <?php if (!empty($client['tax_id'])): ?><br>Tax ID: <?= e($client['tax_id']) ?><?php endif; ?>
            </div>
        </div>
        <div class="dates">
            <table>
                <?php if ($placeOfIssue): ?><tr><td>Place of issue</td><td><?= e($placeOfIssue) ?></td></tr><?php endif; ?>
                <tr><td>Issue date</td><td><?= e($invoice['issue_date']) ?></td></tr>
                <?php if (!empty($invoice['due_date'])): ?><tr><td>Due date</td><td><?= e($invoice['due_date']) ?></td></tr><?php endif; ?>
            </table>
        </div>
    </div>

    <table class="lines">
        <thead>
            <tr>
                <th>Description</th>
                <th class="num" style="width: 70px;">Qty</th>
                <th class="num" style="width: 110px;">Unit price</th>
                <th class="num" style="width: 70px;">VAT</th>
                <th class="num" style="width: 120px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice['lines'] as $line):
                $qty = (float)$line['quantity'];
                $qtyDisplay = $qty == (int)$qty ? (string)(int)$qty : number_format($qty, 2);
            ?>
                <tr>
                    <td><?= nl2br(e($line['description'])) ?></td>
                    <td class="num"><?= $qtyDisplay ?></td>
                    <td class="num"><?= number_format((float)$line['unit_price'], 2) ?></td>
                    <td class="num"><?= rtrim(rtrim(number_format((float)$line['tax_rate'], 2), '0'), '.') ?>%</td>
                    <td class="num"><?= number_format((float)$line['line_subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td><?= money((float)$invoice['subtotal'], $invoice['currency']) ?></td></tr>
        <tr><td>VAT</td><td><?= money((float)$invoice['tax_total'], $invoice['currency']) ?></td></tr>
        <tr class="grand"><td>Total due</td><td><?= money((float)$invoice['total'], $invoice['currency']) ?></td></tr>
    </table>

    <div class="amount-words">
        Amount in words: <strong><?= e($amountInWords) ?></strong>.
    </div>

    <?php if (!empty($invoice['notes'])): ?>
        <div class="notes"><strong>Notes:</strong> <?= nl2br(e($invoice['notes'])) ?></div>
    <?php endif; ?>

    <?php if ($hasBank): ?>
        <div class="bank">
            <h4>Payment — Bank details</h4>
            <div class="bank-grid">
                <?php if (!empty($company['company_legal_name'] ?: $company['company_name'])): ?>
                    <div class="k">Beneficiary</div><div class="v"><?= e($company['company_legal_name'] ?: $company['company_name']) ?></div>
                <?php endif; ?>
                <?php if (!empty($company['company_beneficiary_address'])): ?><div class="k">Beneficiary address</div><div class="v"><?= nl2br(e($company['company_beneficiary_address'])) ?></div><?php endif; ?>
                <?php if (!empty($company['company_bank_name'])): ?><div class="k">Bank</div><div class="v"><?= e($company['company_bank_name']) ?></div><?php endif; ?>
                <?php if (!empty($company['company_rib'])): ?><div class="k">RIB</div><div class="v"><?= e($company['company_rib']) ?></div><?php endif; ?>
                <?php if (!empty($company['company_iban'])): ?><div class="k">IBAN</div><div class="v"><?= e($company['company_iban']) ?></div><?php endif; ?>
                <?php if (!empty($company['company_swift'])): ?><div class="k">SWIFT / BIC</div><div class="v"><?= e($company['company_swift']) ?></div><?php endif; ?>
                <?php if (!empty($company['company_account_currency'])): ?><div class="k">Account currency</div><div class="v"><?= e($company['company_account_currency']) ?></div><?php endif; ?>
                <?php if (!empty($company['company_domiciliation'])): ?><div class="k">Domiciliation ref.</div><div class="v"><?= e($company['company_domiciliation']) ?></div><?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="legal">
        <h4>Legal notices</h4>
        <ul>
            <li>Tax regime: Algerian auto-entrepreneur, subject to the flat-rate tax (IFU) under Law no. 22-23 of 18 May 2022.</li>
            <?php if ($isExport): ?>
                <li>VAT not applicable &mdash; export of services (Art. 13 of the Algerian Turnover Tax Code / CTCA).</li>
            <?php else: ?>
                <li>VAT-exempt under the Algerian auto-entrepreneur regime (Art. 282 ter of the Direct Taxes Code / CIDTA).</li>
            <?php endif; ?>
            <li>Exempt from stamp duty &mdash; settlement by wire transfer (Art. 258 of the Algerian Stamp Duty Code).</li>
            <?php if ($treaty !== ''): ?>
                <li>Tax residence: the supplier is a tax resident of Algeria. Services are rendered entirely from Algeria; the supplier has no permanent establishment, fixed base, office or employee in <?= e($treaty) ?>. Under the Double Taxation Convention between Algeria and <?= e($treaty) ?>, business profits are taxable only in Algeria (Art. 7). No withholding tax should therefore be applied to this invoice. A Certificate of Tax Residence issued by the Algerian Direction Générale des Impôts (DGI) is available on request.</li>
            <?php endif; ?>
            <li>Late payment shall incur interest at the applicable statutory rate without prior reminder.</li>
        </ul>
    </div>

    <div class="signature">
        <div class="box">
            <?php $hasSig = $isSigned && !empty($company['company_signature_file']); ?>
            <?php $hasStamp = $isSigned && !empty($company['company_stamp_file']); ?>
            <?php if ($hasSig || $hasStamp): ?>
                <div class="marks">
                    <?php if ($hasStamp): ?>
                        <img class="stamp" src="<?= isset($pdfMode) ? ($kernel->base . '/storage/' . $company['company_stamp_file']) : '/asset?type=stamp' ?>" alt="stamp">
                    <?php endif; ?>
                    <?php if ($hasSig): ?>
                        <img class="sig" src="<?= isset($pdfMode) ? ($kernel->base . '/storage/' . $company['company_signature_file']) : '/asset?type=signature' ?>" alt="signature">
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="marks"></div>
            <?php endif; ?>
            <div class="line">
                <?= e($legalRep ?: $issuerName) ?><br>
                Signature &amp; stamp
                <?php if ($isSigned): ?>
                    <div style="font-size: 10px; color: #94a3b8; margin-top: 4px;">Signed <?= e($signedDate) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
