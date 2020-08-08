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

        self::assertEquals('example.com', $client->getConnectHost());
        self::assertEquals(9000, $client->getConnectPort());
        self::assertEquals('user1', $client->getConnectUsername());
        self::assertEquals('12345', $client->getConnectPassword());
        self::assertEquals(151, $client->getConnectTimeOut());
        self::assertEquals(150, $client->getTimeout());
        self::assertEquals('my_db1', $client->settings()->getDatabase());
    }
}
