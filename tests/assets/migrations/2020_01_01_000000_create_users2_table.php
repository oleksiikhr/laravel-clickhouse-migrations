<?php

declare(strict_types=1);

use Alexeykhr\ClickhouseMigrations\ClickhouseMigration;

class CreateUsers2Table extends ClickhouseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $db = $this->getDatabaseName();

        $this->client->write("
            CREATE TABLE IF NOT EXISTS {$db}.users2 (
                id UInt64,
                name String
            ) ENGINE = MergeTree()
            ORDER BY id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $db = $this->getDatabaseName();

        $this->client->write("DROP TABLE IF EXISTS {$db}.users2");
    }
}
