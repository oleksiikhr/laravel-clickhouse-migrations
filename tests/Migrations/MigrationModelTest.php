<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Migrations;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationModel;

class MigrationModelTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateTable(): void
    {
        $model = new MigrationModel('migrationsTable2', $this->getClient());

        $checkTablePresent = function () {
            return (bool) $this->getClient()->select("EXISTS TABLE migrationsTable2")
                ->fetchOne()['result'];
        };

        $this->assertFalse($checkTablePresent());

        $model->create();

        $this->assertTrue($checkTablePresent());
    }
}
