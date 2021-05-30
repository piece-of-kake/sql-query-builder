<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class TableExists implements CanCompile
{
    /**
     * @var string
     */
    private $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function compile()
    {
        return sprintf('SELECT 1 FROM %s LIMIT 1', $this->tableName);
    }
}
