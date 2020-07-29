<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use Alexeykhr\ClickhouseMigrations\StubFactory;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class StubFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws ClickhouseStubException
     */
    public function testCreateExistsStub(): void
    {
        $stubs = StubFactory::getExistsStubs();

        $stub = StubFactory::create(key($stubs));

        $this->assertInstanceOf(MigrationStubContract::class, $stub);
    }

    /**
     * @return void
     */
    public function testCreateNotExistsStub(): void
    {
        try {
            StubFactory::create('not-exists-type');

            $this->fail('Exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(get_class($e), ClickhouseStubException::class);
        }
    }
}
