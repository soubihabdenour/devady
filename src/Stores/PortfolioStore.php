<?php declare(strict_types=1);

namespace App\Stores;

use PDO;

final class PortfolioStore
{
    public function __construct(private PDO $pdo) {}

    /** @return list<array<string,mixed>> */
    public function all(): array
    {
        $rows = $this->pdo->query(
            'SELECT * FROM portfolio_items ORDER BY position ASC, id ASC'
        )->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM portfolio_items WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function save(array $data, ?int $id = null): int
    {
        $now = time();
        $params = [
            ':title'    => mb_substr(trim((string)($data['title']  ?? '')), 0, 200),
            ':period'   => mb_substr(trim((string)($data['period'] ?? '')), 0, 60) ?: null,
            ':role'     => mb_substr(trim((string)($data['role']   ?? '')), 0, 120) ?: null,
            ':body'     => mb_substr(trim((string)($data['body']   ?? '')), 0, 2000) ?: null,
            ':tags'     => json_encode(self::normalizeTags($data['tags'] ?? []), JSON_UNESCAPED_UNICODE),
            ':url'      => mb_substr(trim((string)($data['url']    ?? '')), 0, 500) ?: null,
            ':position' => (int)($data['position'] ?? 0),
            ':u'        => $now,
        ];

        if ($id) {
            $stmt = $this->pdo->prepare(
                'UPDATE portfolio_items
                    SET title = :title, period = :period, role = :role, body = :body,
                        tags = :tags, url = :url, position = :position, updated_at = :u
                  WHERE id = :id'
            );
            $params[':id'] = $id;
            $stmt->execute($params);
            return $id;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO portfolio_items
                (title, period, role, body, tags, url, position, created_at, updated_at)
             VALUES
                (:title, :period, :role, :body, :tags, :url, :position, :c, :u)'
        );
        $params[':c'] = $now;
        $stmt->execute($params);
        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM portfolio_items WHERE id = :id')->execute([':id' => $id]);
    }

    /** @return list<string> */
    public static function normalizeTags(mixed $tags): array
    {
        if (is_string($tags)) {
            $tags = preg_split('/\s*,\s*/', $tags) ?: [];
        }
        if (!is_array($tags)) return [];
        $out = [];
        foreach ($tags as $t) {
            $t = trim((string)$t);
            if ($t !== '') $out[] = mb_substr($t, 0, 40);
        }
        return array_values(array_unique($out));
    }

    /** @param array<string,mixed> $row */
    private function hydrate(array $row): array
    {
        $tags = [];
        if (!empty($row['tags'])) {
            $decoded = json_decode((string)$row['tags'], true);
            if (is_array($decoded)) $tags = array_values(array_filter(array_map('strval', $decoded), 'strlen'));
        }
        $row['tags'] = $tags;
        return $row;
    }
}
