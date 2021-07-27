<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Alter\AddColumn;
use PoK\SQLQueryBuilder\Table\Alter\DropColumn;
use PoK\ValueObject\Collection;

class AlterTable implements CanCompile
{
    private $tableName;
    private $newColumns;
    private $dropColumns;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->newColumns = new Collection([]);
        $this->dropColumns = new Collection([]);
    }

    public function addColumn(string $columnName): AddColumn
    {
        $builder = new AddColumn($columnName);
        $this->newColumns[] = $builder;
        return $builder;
    }

    public function dropColumn(string $columnName): DropColumn
    {
        $builder = new DropColumn($columnName);
        $this->dropColumns[] = $builder;
        return $builder;
    }

    public function compile()
    {
        $modifications = $this->newColumns
            ->map(function ($newColumn) {
                return $newColumn->compile();
            })
            ->merge(
                $this->dropColumns
                    ->map(function ($dropColumn) {
                        return $dropColumn->compile();
                    })
            )
            ->toArray();

        return sprintf('ALTER TABLE `%s` %s;', $this->tableName, implode(', ', $modifications));
    }
}