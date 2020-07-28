<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use Alexeykhr\ClickhouseMigrations\Clickhouse;

class ClickhouseTest extends TestCase
{
    /**
     * @return void
     */
    public function testMainConfig(): void
    {
        $clickhouse = new Clickhouse([
            'host' => 'example.com',
            'port' => 9000,
            'username' => 'user1',
            'password' => '12345',
            'options' => [
                'database' => 'my_db1',
                'timeout' => 150,
                'connectTimeOut' => 151,
            ],
        ]);

        $client = $clickhouse->getClient();

        $this->assertEquals('example.com', $client->getConnectHost());
        $this->assertEquals(9000, $client->getConnectPort());
        $this->assertEquals('user1', $client->getConnectUsername());
        $this->assertEquals('12345', $client->getConnectPassword());
        $this->assertEquals(151, $client->getConnectTimeOut());
        $this->assertEquals(150, $client->getTimeout());
        $this->assertEquals('my_db1', $client->settings()->getDatabase());
    }
}
