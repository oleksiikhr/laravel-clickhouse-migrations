<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Migrations;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationRepository;

class MigrationRepositoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateTable(): void
    {
        $repository = new MigrationRepository('myTable1', $this->clickhouse());

        $checkTablePresent = function () {
            return (bool) $this->clickhouse()->select("EXISTS TABLE myTable1")
                ->fetchOne('result');
        };

        self::assertFalse($checkTablePresent());

        $repository->create();

        self::assertTrue($checkTablePresent());
    }

    /**
     * @return void
     */
    public function testGetAllMigrations(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_1', 1), ('m_2', 1), ('m_3', 2)");

        $migrations = $repository->all();

        self::assertContains('m_1', $migrations);
        self::assertContains('m_2', $migrations);
        self::assertContains('m_3', $migrations);
        self::assertCount(3, $migrations);
    }

    /**
     * @return void
     */
    public function testGetLatestMigrations(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_1', 1), ('m_2', 1)");
        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_3', 5), ('m_4', 3)");
        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_5', 2), ('m_7', 2), ('m_6', 2)");

        $migrations = $repository->latest();

        self::assertEquals('m_3', $migrations[0]); // because last batch
        self::assertEquals('m_4', $migrations[1]);
        self::assertEquals('m_7', $migrations[2]); // executed last because of the name
        self::assertEquals('m_6', $migrations[3]);
        self::assertEquals('m_5', $migrations[4]);
        self::assertEquals('m_2', $migrations[5]);
        self::assertEquals('m_1', $migrations[6]);
    }

    /**
     * @return void
     */
    public function testLastBatchNumber(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        self::assertEquals(0, $repository->getLastBatchNumber());

        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6)");

        self::assertEquals(11, $repository->getLastBatchNumber());
    }

    /**
     * @return void
     */
    public function testNextBatchNumber(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        self::assertEquals(1, $repository->getNextBatchNumber());

        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6)");

        self::assertEquals(12, $repository->getNextBatchNumber());
    }

    /**
     * @return void
     */
    public function testAddMigration(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        $repository->add('m_1', 1);
        $repository->add('m_2', 2);
        $repository->add('m_3', 2);

        $migrations = $this->clickhouse()->select("SELECT * FROM t")->rowsAsTree('migration');

        self::assertArrayHasKey('m_1', $migrations);
        self::assertEquals(1, $migrations['m_1']['batch']);
        self::assertArrayHasKey('m_1', $migrations);
        self::assertEquals(2, $migrations['m_2']['batch']);
        self::assertArrayHasKey('m_1', $migrations);
        self::assertEquals(2, $migrations['m_3']['batch']);
    }

    /**
     * @return void
     */
    public function testDeleteMigration(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6), ('m_3', 4)");

        $repository->delete('m_3');

        self::assertNotContains('m_3', $repository->latest());
    }

    /**
     * @return void
     */
    public function testExistsTable(): void
    {
        $repository = new MigrationRepository('myTable2', $this->clickhouse());

        self::assertFalse($repository->exists());

        $this->clickhouse()->write("CREATE TABLE myTable2 (date DateTime) ENGINE = MergeTree() ORDER BY date");

        self::assertTrue($repository->exists());
    }

    /**
     * @return void
     */
    public function testFindMigration(): void
    {
        $repository = new MigrationRepository('t', $this->clickhouse());
        $repository->create();

        $this->clickhouse()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6), ('m_3', 4)");

        $migration = $repository->find('m_1');

        self::assertContains('m_1', $migration);
    }
}
