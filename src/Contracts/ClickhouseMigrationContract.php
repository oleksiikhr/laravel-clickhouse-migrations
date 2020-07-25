<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Contracts;

interface ClickhouseMigrationContract
{
    /**
     * @return void
     */
    public function up(): void;

    /**
     * @return void
     */
    public function down(): void;
}
