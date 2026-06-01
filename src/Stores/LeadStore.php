<?php declare(strict_types=1);

namespace App\Stores;

use PDO;

final class LeadStore
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT * FROM leads ORDER BY created_at DESC, id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM leads WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function unreadCount(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM leads WHERE read_at IS NULL')->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO leads (name, email, subject, message, ip, user_agent, created_at)
             VALUES (:n, :e, :s, :m, :ip, :ua, :c)'
        );
        $stmt->execute([
            ':n'  => mb_substr(trim($data['name']    ?? ''), 0, 120),
            ':e'  => mb_substr(trim($data['email']   ?? ''), 0, 160) ?: null,
            ':s'  => mb_substr(trim($data['subject'] ?? ''), 0, 200) ?: null,
            ':m'  => mb_substr(trim($data['message'] ?? ''), 0, 5000) ?: null,
            ':ip' => mb_substr((string)($data['ip']         ?? ''), 0, 45) ?: null,
            ':ua' => mb_substr((string)($data['user_agent'] ?? ''), 0, 300) ?: null,
            ':c'  => time(),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function markRead(int $id): void
    {
        $this->pdo->prepare('UPDATE leads SET read_at = :t WHERE id = :id AND read_at IS NULL')
            ->execute([':t' => time(), ':id' => $id]);
    }

    public function markConverted(int $id, int $clientId): void
    {
        $this->pdo->prepare('UPDATE leads SET converted_client_id = :c, read_at = COALESCE(read_at, :t) WHERE id = :id')
            ->execute([':c' => $clientId, ':t' => time(), ':id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM leads WHERE id = :id')->execute([':id' => $id]);
    }
}
