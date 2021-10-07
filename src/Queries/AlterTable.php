<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Alter\AddColumn;
use PoK\SQLQueryBuilder\Table\Alter\DropColumn;
use PoK\SQLQueryBuilder\Table\Alter\UpdateColumn;
use PoK\ValueObject\Collection;

class AlterTable implements CanCompile
{
    private $tableName;
    private $newColumns;
    private $dropColumns;
    private $updateColumns;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->newColumns = new Collection([]);
        $this->dropColumns = new Collection([]);
        $this->updateColumns = new Collection([]);
    }

    public function columns($callback)
    {
        $callback($this);
        return $this;
    }

    public function add(string $columnName): AddColumn
    {
        $builder = new AddColumn($columnName);
        $this->newColumns[] = $builder;
        return $builder;
    }

    public function drop(string $columnName): void
    {
        $builder = new DropColumn($columnName);
        $this->dropColumns[] = $builder;
    }

    public function update(string $columnName): UpdateColumn
    {
        $builder = new UpdateColumn($columnName);
        $this->updateColumns[] = $builder;
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
            ->merge(
                $this->updateColumns
                    ->map(function ($updateColumn) {
                        return $updateColumn->compile();
                    })
            )
            ->toArray();

        return sprintf('ALTER TABLE `%s` %s;', $this->tableName, implode(', ', $modifications));
    }
}