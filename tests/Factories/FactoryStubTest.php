<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Factories;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Factories\FactoryStub;
use Alexeykhr\ClickhouseMigrations\Stubs\DefaultMigrationStub;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class FactoryStubTest extends TestCase
{
    /**
     * @return void
     * @throws ClickhouseStubException
     */
    public function testCreateExistsStub(): void
    {
        $stubs = FactoryStub::getStubs();

        $stub = FactoryStub::create(key($stubs));

        $this->assertInstanceOf(MigrationStubContract::class, $stub);
    }

    /**
     * @return void
     * @throws ClickhouseStubException
     */
    public function testCreateExistsPackageStub(): void
    {
        config(['clickhouse.stubs' => ['myCustomStub' => DefaultMigrationStub::class]]);

        $stub = FactoryStub::create('myCustomStub');

        $this->assertInstanceOf(MigrationStubContract::class, $stub);
    }

    /**
     * @return void
     */
    public function testCreateNotExistsStub(): void
    {
        try {
            FactoryStub::create('not-exists-type');

            $this->fail('Exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(get_class($e), ClickhouseStubException::class);
        }
    }
}
