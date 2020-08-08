<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Asserts;

trait AssertsClickhouse
{
    /**
     * @param  int  $excepted
     * @return void
     */
    public function assertClickhouseTotal(int $excepted): void
    {
        self::assertEquals($excepted, $this->repository()->total());
    }

    /**
     * @param  string  $migration
     * @param  bool  $usePrefix
     * @return void
     */
    public function assertClickhouseContainsMigration(string $migration, bool $usePrefix = true): void
    {
        if ($usePrefix) {
            $migration = $this->migrationPrefix($migration);
        }

        $result = $this->repository()->find($migration);

        self::assertNotNull($result);
        self::assertContains($migration, $result);
    }

    /**
     * @param  string  $fileName
     * @param  bool  $usePrefix
     * @return void
     */
    public function assertClickhouseMigrationFile(string $fileName, bool $usePrefix = true): void
    {
        if ($usePrefix) {
            $fileName = $this->migrationPrefix($fileName);
        }

        self::assertFileExists($this->dynamicPath('migrations/'.$fileName.'.php'));
    }

    /**
     * @param  string  $table
     * @return void
     */
    public function assertClickhouseTableExists(string $table): void
    {
        self::assertTrue($this->existsTable($table));
    }

    /**
     * @param  string  $table
     * @return void
     */
    public function assertClickhouseTableNotExists(string $table): void
    {
        self::assertFalse($this->existsTable($table));
    }

    /**
     * @param  string  $table
     * @return bool
     */
    private function existsTable(string $table): bool
    {
        return (bool) $this->clickhouse()->select("EXISTS TABLE {table}", [
            'table' => $table,
        ])->fetchOne('result');
    }
}
