<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Stubs;

class TableMigrationStub extends BaseMigrationStub
{
    /**
     * @inheritDoc
     */
    public function generate(string $className, array $parameters = []): string
    {
        $content = parent::generate($className, $parameters);

        return $this->populateTable($parameters['table'], $content);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return 'table.stub';
    }

    /**
     * @param  string  $table
     * @param  string  $content
     * @return string
     */
    protected function populateTable(string $table, string $content): string
    {
        return str_replace(
            ['{{ table }}', '{{table}}'],
            $table,
            $content
        );
    }
}
