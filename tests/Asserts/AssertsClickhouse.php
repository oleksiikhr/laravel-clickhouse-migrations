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
            $migration = '2020_01_01_000000_create_'.$migration.'_table';
        }

        $result = $this->repository()->find($migration);

        self::assertNotNull($result);
        self::assertContains($migration, $result);
    }
}
