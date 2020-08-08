<?php declare(strict_types=1);

use Alexeykhr\ClickhouseMigrations\ClickhouseMigration;

class CreateUsers3ExceptionTable extends ClickhouseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->client->write("CREATE anything");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $this->client->write("DROP TABLE anything");
    }
}
