<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use ClickHouseDB\Client;

class Clickhouse
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(array $config = [])
    {
        $config = $config ?: $this->getConfig();

        $this->client = $this->makeClient($config);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param  Client  $client
     * @return $this
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Creating a new instance of ClickHouse Client
     *
     * @param  array  $config
     * @return Client
     */
    protected function makeClient(array $config): Client
    {
        $options = [];

        if (isset($config['options'])) {
            $options = $config['options'];

            unset($config['options']);
        }

        $client = new Client($config);

        foreach ($options as $option => $value) {
            if (method_exists($client, $option)) {
                $method = $option;
            } elseif (method_exists($client, 'set'.ucwords($option))) {
                $method = 'set'.ucwords($option);
            } else {
                throw new \RuntimeException("Unknown ClickHouse DB option {$option}");
            }

            $client->$method($value);
        }

        return $client;
    }

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        return config('clickhouse.config', []);
    }
}
