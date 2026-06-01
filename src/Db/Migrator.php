<?php declare(strict_types=1);

namespace App\Db;

use PDO;
use RuntimeException;

final class Migrator
{
    public function __construct(private PDO $pdo, private string $dir) {}

    public function migrate(): array
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                name TEXT PRIMARY KEY,
                applied_at INTEGER NOT NULL
             )'
        );

        $applied = [];
        foreach ($this->pdo->query('SELECT name FROM migrations') as $row) {
            $applied[$row['name']] = true;
        }

        $files = glob(rtrim($this->dir, '/') . '/*.sql') ?: [];
        sort($files, SORT_NATURAL);

        $ran = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (isset($applied[$name])) continue;

            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException("Could not read migration: $file");
            }

            $this->pdo->beginTransaction();
            try {
                $this->pdo->exec($sql);
                $stmt = $this->pdo->prepare('INSERT INTO migrations (name, applied_at) VALUES (:n, :t)');
                $stmt->execute([':n' => $name, ':t' => time()]);
                $this->pdo->commit();
                $ran[] = $name;
            } catch (\Throwable $e) {
                $this->pdo->rollBack();
                throw new RuntimeException("Migration failed in $name: " . $e->getMessage(), 0, $e);
            }
        }
        return $ran;
    }
}
