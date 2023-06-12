<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\ValueObject\IndexType;
use PoK\ValueObject\Collection;
use function Clue\StreamFilter\fun;
use function DI\string;

class CreateIndex implements CanCompile
{
    private IndexType $indexType;
    private string $indexName;
    private string $tableName;
    private Collection $columnNames;

    public function __construct(
        string $tableName,
        IndexType $indexType,
        string $indexName,
        Collection $columnNames
    )
    {
        $this->tableName = $tableName;
        $this->indexType = $indexType;
        $this->indexName = $indexName;
        $this->columnNames = $columnNames;
    }

    public function compile()
    {
        $columnNames = $this->columnNames
            ->map(function ($columnName) {
                return "`$columnName`";
            })
            ->implode(', ');

        return sprintf(
            'CREATE %s INDEX %s ON %s (%s)',
            (string) $this->indexType,
            $this->indexName,
            $this->tableName,
            $columnNames
        );
    }


}