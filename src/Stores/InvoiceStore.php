<?php declare(strict_types=1);

namespace App\Stores;

use App\Settings\Settings;
use PDO;
use Throwable;

final class InvoiceStore
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT i.*, c.name AS client_name
               FROM invoices i
               LEFT JOIN clients c ON c.id = i.client_id
              ORDER BY i.issue_date DESC, i.id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM invoices WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $invoice = $stmt->fetch();
        if (!$invoice) return null;

        $linesStmt = $this->pdo->prepare(
            'SELECT * FROM invoice_lines WHERE invoice_id = :id ORDER BY position'
        );
        $linesStmt->execute([':id' => $id]);
        $invoice['lines'] = $linesStmt->fetchAll();
        return $invoice;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id FROM invoices WHERE public_token = :t');
        $stmt->execute([':t' => $token]);
        $id = $stmt->fetchColumn();
        return $id ? $this->find((int)$id) : null;
    }

    public function create(array $data, array $lines, Settings $settings): int
    {
        $number = $data['number'] ?? $settings->nextInvoiceNumber();

        $this->pdo->beginTransaction();
        try {
            $now    = time();
            $totals = self::computeTotals($lines);

            $stmt = $this->pdo->prepare(
                'INSERT INTO invoices
                    (number, client_id, issue_date, due_date, status, notes,
                     currency, subtotal, tax_total, total,
                     place_of_issue, amount_in_words, is_export, treaty_country,
                     created_at, updated_at)
                 VALUES
                    (:n, :ci, :iss, :dd, :st, :no, :cu, :sub, :tax, :tot,
                     :poi, :aiw, :ex, :tc, :c, :u)'
            );
            $stmt->execute([
                ':n'   => $number,
                ':ci'  => (int)$data['client_id'],
                ':iss' => $data['issue_date'],
                ':dd'  => $data['due_date'] ?: null,
                ':st'  => $data['status'] ?? 'draft',
                ':no'  => $data['notes'] ?? null,
                ':cu'  => $data['currency'] ?? 'DZD',
                ':sub' => $totals['subtotal'],
                ':tax' => $totals['tax_total'],
                ':tot' => $totals['total'],
                ':poi' => $data['place_of_issue']  ?? null,
                ':aiw' => $data['amount_in_words'] ?? null,
                ':ex'  => !empty($data['is_export']) ? 1 : 0,
                ':tc'  => $data['treaty_country']  ?? null,
                ':c'   => $now,
                ':u'   => $now,
            ]);
            $invoiceId = (int)$this->pdo->lastInsertId();
            $this->writeLines($invoiceId, $lines);
            $this->pdo->commit();
            return $invoiceId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data, array $lines): void
    {
        $this->pdo->beginTransaction();
        try {
            $totals = self::computeTotals($lines);
            $stmt = $this->pdo->prepare(
                'UPDATE invoices
                    SET client_id = :ci, issue_date = :iss, due_date = :dd,
                        status = :st, notes = :no, currency = :cu,
                        subtotal = :sub, tax_total = :tax, total = :tot,
                        place_of_issue = :poi, amount_in_words = :aiw,
                        is_export = :ex, treaty_country = :tc,
                        paid_at = CASE WHEN :st = "paid" AND paid_at IS NULL THEN :now ELSE paid_at END,
                        updated_at = :u
                  WHERE id = :iid'
            );
            $now = time();
            $stmt->execute([
                ':iid' => $id,
                ':ci'  => (int)$data['client_id'],
                ':iss' => $data['issue_date'],
                ':dd'  => $data['due_date'] ?: null,
                ':st'  => $data['status'] ?? 'draft',
                ':no'  => $data['notes'] ?? null,
                ':cu'  => $data['currency'] ?? 'DZD',
                ':sub' => $totals['subtotal'],
                ':tax' => $totals['tax_total'],
                ':tot' => $totals['total'],
                ':poi' => $data['place_of_issue']  ?? null,
                ':aiw' => $data['amount_in_words'] ?? null,
                ':ex'  => !empty($data['is_export']) ? 1 : 0,
                ':tc'  => $data['treaty_country']  ?? null,
                ':now' => $now,
                ':u'   => $now,
            ]);

            $this->pdo->prepare('DELETE FROM invoice_lines WHERE invoice_id = :id')
                ->execute([':id' => $id]);
            $this->writeLines($id, $lines);
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM invoices WHERE id = :id')->execute([':id' => $id]);
    }

    public function sign(int $id): void
    {
        $now = time();
        $this->pdo->prepare('UPDATE invoices SET signed_at = :t, updated_at = :t WHERE id = :id')
            ->execute([':t' => $now, ':id' => $id]);
    }

    public function unsign(int $id): void
    {
        $this->pdo->prepare('UPDATE invoices SET signed_at = NULL, updated_at = :t WHERE id = :id')
            ->execute([':t' => time(), ':id' => $id]);
    }

    /** Generate (or rotate) the public-link token for an invoice. */
    public function generatePublicToken(int $id): string
    {
        $token = bin2hex(random_bytes(16));
        $this->pdo->prepare('UPDATE invoices SET public_token = :t, updated_at = :u WHERE id = :id')
            ->execute([':t' => $token, ':u' => time(), ':id' => $id]);
        return $token;
    }

    public function revokePublicToken(int $id): void
    {
        $this->pdo->prepare('UPDATE invoices SET public_token = NULL, updated_at = :u WHERE id = :id')
            ->execute([':u' => time(), ':id' => $id]);
    }

    public function totals(): array
    {
        return $this->pdo->query(
            "SELECT
               COUNT(*) AS count,
               COALESCE(SUM(total), 0) AS billed,
               COALESCE(SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END), 0) AS paid,
               COALESCE(SUM(CASE WHEN status = 'sent' THEN total ELSE 0 END), 0) AS outstanding
             FROM invoices"
        )->fetch() ?: ['count' => 0, 'billed' => 0, 'paid' => 0, 'outstanding' => 0];
    }

    private function writeLines(int $invoiceId, array $lines): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO invoice_lines
                (invoice_id, position, description, quantity, unit_price,
                 tax_rate, line_subtotal, line_tax, line_total)
             VALUES
                (:i, :p, :d, :q, :up, :tr, :ls, :lt, :lto)'
        );
        $pos = 1;
        foreach ($lines as $line) {
            $qty   = (float)($line['quantity'] ?? 1);
            $price = (float)($line['unit_price'] ?? 0);
            $rate  = (float)($line['tax_rate'] ?? 0);
            $sub   = round($qty * $price, 2);
            $tax   = round($sub * $rate / 100, 2);

            $stmt->execute([
                ':i'   => $invoiceId,
                ':p'   => $pos++,
                ':d'   => $line['description'] ?? '',
                ':q'   => $qty,
                ':up'  => $price,
                ':tr'  => $rate,
                ':ls'  => $sub,
                ':lt'  => $tax,
                ':lto' => round($sub + $tax, 2),
            ]);
        }
    }

    /** @return array{subtotal:float, tax_total:float, total:float} */
    public static function computeTotals(array $lines): array
    {
        $sub = 0.0;
        $tax = 0.0;
        foreach ($lines as $line) {
            $qty   = (float)($line['quantity'] ?? 1);
            $price = (float)($line['unit_price'] ?? 0);
            $rate  = (float)($line['tax_rate'] ?? 0);
            $lineSub = round($qty * $price, 2);
            $lineTax = round($lineSub * $rate / 100, 2);
            $sub += $lineSub;
            $tax += $lineTax;
        }
        return [
            'subtotal'  => round($sub, 2),
            'tax_total' => round($tax, 2),
            'total'     => round($sub + $tax, 2),
        ];
    }
}
