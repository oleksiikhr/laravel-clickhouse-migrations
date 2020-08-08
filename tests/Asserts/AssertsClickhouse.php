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
     * @return void
     */
    public function assertClickhouseContainsMigration(string $migration): void
    {
        $result = $this->repository()->find($migration);

        self::assertNotNull($result);
        self::assertContains($migration, $result);
    }
}
