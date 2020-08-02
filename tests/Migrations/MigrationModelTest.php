<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Migrations;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationRepository;

class MigrationModelTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateTable(): void
    {
        $repository = new MigrationRepository('myTable1', $this->getClient());

        $checkTablePresent = function () {
            return (bool) $this->getClient()->select("EXISTS TABLE myTable1")
                ->fetchOne('result');
        };

        $this->assertFalse($checkTablePresent());

        $repository->create();

        $this->assertTrue($checkTablePresent());
    }

    /**
     * @return void
     */
    public function testGetAllMigrations(): void
    {
        $repository = new MigrationRepository('t', $this->getClient());
        $repository->create();

        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_1', 1), ('m_2', 1), ('m_3', 2)");

        $migrations = $repository->all();

        $this->assertContains('m_1', $migrations);
        $this->assertContains('m_2', $migrations);
        $this->assertContains('m_3', $migrations);
        $this->assertCount(3, $migrations);
    }

    /**
     * @return void
     */
    public function testGetLatestMigrations(): void
    {
        $repository = new MigrationRepository('t', $this->getClient());
        $repository->create();

        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_1', 1), ('m_2', 1)");
        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_3', 5), ('m_4', 3)");
        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_5', 2), ('m_7', 2), ('m_6', 2)");

        $migrations = $repository->latest();

        $this->assertEquals($migrations[0], 'm_3'); // because last batch
        $this->assertEquals($migrations[1], 'm_4');
        $this->assertEquals($migrations[2], 'm_7'); // executed last because of the name
        $this->assertEquals($migrations[3], 'm_6');
        $this->assertEquals($migrations[4], 'm_5');
        $this->assertEquals($migrations[5], 'm_2');
        $this->assertEquals($migrations[6], 'm_1');
    }

    /**
     * @return void
     */
    public function testLastBatchNumber(): void
    {
        $repository = new MigrationRepository('t', $this->getClient());
        $repository->create();

        $this->assertEquals($repository->getLastBatchNumber(), 0);

        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6)");

        $this->assertEquals($repository->getLastBatchNumber(), 11);
    }

    /**
     * @return void
     */
    public function testNextBatchNumber(): void
    {
        $repository = new MigrationRepository('t', $this->getClient());
        $repository->create();

        $this->assertEquals($repository->getNextBatchNumber(), 1);

        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6)");

        $this->assertEquals($repository->getNextBatchNumber(), 12);
    }

    /**
     * @return void
     */
    public function testAddMigration(): void
    {
        $repository = new MigrationRepository('t', $this->getClient());
        $repository->create();

        $repository->add('m_1', 1);
        $repository->add('m_2', 2);
        $repository->add('m_3', 2);

        $migrations = $this->getClient()->select("SELECT * FROM t")->rowsAsTree('migration');

        $this->assertArrayHasKey('m_1', $migrations);
        $this->assertEquals($migrations['m_1']['batch'], 1);
        $this->assertArrayHasKey('m_1', $migrations);
        $this->assertEquals($migrations['m_2']['batch'], 2);
        $this->assertArrayHasKey('m_1', $migrations);
        $this->assertEquals($migrations['m_3']['batch'], 2);
    }

    /**
     * @return void
     */
    public function testDeleteMigration(): void
    {
        $repository = new MigrationRepository('t', $this->getClient());
        $repository->create();

        $this->getClient()->write("INSERT INTO t (migration, batch) VALUES ('m_2', 11), ('m_1', 6), ('m_3', 4)");

        $repository->delete('m_3');

        $this->assertNotContains('m_3', $repository->latest());
    }

    /**
     * @return void
     */
    public function testExistsTable(): void
    {
        $repository = new MigrationRepository('myTable2', $this->getClient());

        $this->assertFalse($repository->exists());

        $this->getClient()->write("CREATE TABLE myTable2 (date DateTime) ENGINE = MergeTree() ORDER BY date");

        $this->assertTrue($repository->exists());
    }
}
