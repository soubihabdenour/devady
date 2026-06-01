<?php declare(strict_types=1);

namespace App\Stores;

use PDO;
use RuntimeException;

final class ClientStore
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM clients ORDER BY name COLLATE NOCASE')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clients WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): int
    {
        $now    = time();
        $params = [
            ':n'   => $data['name'],
            ':e'   => $data['email']           ?? null,
            ':p'   => $data['phone']           ?? null,
            ':a'   => $data['address']         ?? null,
            ':t'   => $data['tax_id']          ?? null,
            ':no'  => $data['notes']           ?? null,
            ':co'  => $data['country']         ?? null,
            ':brn' => $data['business_reg_no'] ?? null,
            ':cn'  => $data['contact_name']    ?? null,
            ':u'   => $now,
        ];

        if ($id) {
            $stmt = $this->pdo->prepare(
                'UPDATE clients
                    SET name = :n, email = :e, phone = :p, address = :a,
                        tax_id = :t, notes = :no, country = :co,
                        business_reg_no = :brn, contact_name = :cn,
                        updated_at = :u
                  WHERE id = :id'
            );
            $params[':id'] = $id;
            $stmt->execute($params);
            return $id;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO clients
                (name, email, phone, address, tax_id, notes,
                 country, business_reg_no, contact_name,
                 created_at, updated_at)
             VALUES
                (:n, :e, :p, :a, :t, :no, :co, :brn, :cn, :c, :u)'
        );
        $params[':c'] = $now;
        $stmt->execute($params);
        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $check = $this->pdo->prepare('SELECT COUNT(*) FROM invoices WHERE client_id = :id');
        $check->execute([':id' => $id]);
        if ((int)$check->fetchColumn() > 0) {
            throw new RuntimeException('Cannot delete a client that has invoices. Delete or reassign the invoices first.');
        }
        $this->pdo->prepare('DELETE FROM clients WHERE id = :id')->execute([':id' => $id]);
    }
}
