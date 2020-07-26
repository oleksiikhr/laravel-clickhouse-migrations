<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use ClickHouseDB\Client;
use ClickHouseDB\Statement;

class MigrationModel
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(string $tableName, Client $client)
    {
        $this->tableName = $tableName;
        $this->client = $client;
    }

    /**
     * Creating a new table to store migrations
     *
     * @return Statement
     */
    public function create(): Statement
    {
        return $this->client->write("
            CREATE TABLE IF NOT EXISTS {$this->tableName} (
                migration String,
                batch UInt32
            )
            ENGINE = ReplacingMergeTree()
            ORDER BY migration
        ");
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $rows = $this->client->select("SELECT migration FROM {$this->tableName}")->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * Get the last migration batch
     *
     * @return array
     */
    public function getLast(): array
    {
        $rows = $this->client->select("
            SELECT migration
            FROM {$this->tableName}
            WHERE batch = (
                SELECT MAX(batch)
                FROM {$this->tableName}
            )
            ORDER BY migration DESC
        ")->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * @return int
     */
    public function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * @return int
     */
    public function getLastBatchNumber(): int
    {
        return $this->client
            ->select("SELECT MAX(batch) AS batch FROM {$this->tableName}")
            ->fetchOne()['batch'];
    }

    /**
     * @param  string  $migration
     * @param  int  $batch
     * @return Statement
     */
    public function add(string $migration, int $batch): Statement
    {
        return $this->client->insert($this->tableName, [[$migration, $batch]], ['migration', 'batch']);
    }

    /**
     * @param  string  $migration
     * @return Statement
     */
    public function delete(string $migration): Statement
    {
        return $this->client->write("ALTER TABLE {$this->tableName} DELETE WHERE migration=:migration", [
            'migration' => $migration,
        ]);
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (int) $this->client->write("EXISTS TABLE {$this->tableName}")->rawData() === 1;
    }
}
