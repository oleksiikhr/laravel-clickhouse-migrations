<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\assets;

use Alexeykhr\ClickhouseMigrations\Migrations\MigrationCreator;

class MigrationCreatorExtended extends MigrationCreator
{
    /**
     * @inheritDoc
     */
    protected function getDatePrefix(): string
    {
        return '2020_01_01_000000';
    }
}
