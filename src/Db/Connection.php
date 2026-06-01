<?php declare(strict_types=1);

namespace App\Db;

use PDO;
use RuntimeException;

final class Connection
{
    private ?PDO $pdo = null;

    public function __construct(private string $path) {}

    public function pdo(): PDO
    {
        if ($this->pdo === null) {
            $dir = dirname($this->path);
            if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
                throw new RuntimeException("Storage directory not writable: $dir");
            }
            $this->pdo = new PDO('sqlite:' . $this->path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec('PRAGMA journal_mode = WAL');
            $this->pdo->exec('PRAGMA foreign_keys = ON');
        }
        return $this->pdo;
    }
}
