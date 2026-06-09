<?php declare(strict_types=1);

namespace App\Settings;

use DateTimeImmutable;
use PDO;
use Throwable;

final class Settings
{
    private const DEFAULTS = [
        // Issuer identity (auto-entrepreneur, Algeria)
        'company_name'           => 'DevAdy',
        'company_legal_name'     => '',
        'company_address'        => '',
        'company_city'           => '',
        'company_email'          => '',
        'company_phone'          => '',
        'company_tax_id'         => '',
        'company_anae_no'        => '',
        'company_activity_code'  => '',
        'company_activity_label' => '',
        // Banking
        'company_bank_name'           => '',
        'company_rib'                 => '',
        'company_iban'                => '',
        'company_swift'               => '',
        'company_domiciliation'       => '',
        'company_beneficiary_address' => '',
        'company_account_currency'    => '',
        // Signature & stamp
        'company_signature_file' => '',
        'company_stamp_file'     => '',
        // Invoice defaults
        'currency'         => 'DZD',
        'default_tax_rate' => '0',
        'invoice_prefix'   => 'INV',
        'invoice_padding'  => '4',
        'invoice_year'     => '',
        'last_invoice_seq' => '0',
    ];

    public function __construct(private PDO $pdo) {}

    public function get(string $key): string
    {
        $stmt = $this->pdo->prepare('SELECT value FROM settings WHERE key = :k');
        $stmt->execute([':k' => $key]);
        $v = $stmt->fetchColumn();
        return $v !== false ? (string)$v : (self::DEFAULTS[$key] ?? '');
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO settings (key, value) VALUES (:k, :v)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );
        $stmt->execute([':k' => $key, ':v' => $value]);
    }

    /** @return array<string,string> */
    public function all(): array
    {
        $out = self::DEFAULTS;
        foreach ($this->pdo->query('SELECT key, value FROM settings') as $row) {
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    /** Atomically allocate the next invoice number (prefix-YYYY-NNNN). */
    public function nextInvoiceNumber(): string
    {
        $this->pdo->beginTransaction();
        try {
            $year       = (new DateTimeImmutable('today'))->format('Y');
            $storedYear = $this->get('invoice_year');
            $seq        = (int)$this->get('last_invoice_seq');

            if ($storedYear !== $year) {
                $seq = 0;
                $this->set('invoice_year', $year);
            }
            $seq++;
            $this->set('last_invoice_seq', (string)$seq);

            $prefix  = $this->get('invoice_prefix') ?: 'INV';
            $padding = max(1, (int)$this->get('invoice_padding'));

            $this->pdo->commit();
            return sprintf('%s-%s-%0' . $padding . 'd', $prefix, $year, $seq);
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
