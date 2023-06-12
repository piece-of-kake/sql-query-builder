
<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class DropIndex implements CanCompile
{
    private string $tableName;
    private string $indexName;

    public function __construct(string $tableName, string $indexName)
    {
        $this->tableName = $tableName;
        $this->indexName = $indexName;
    }

    public function compile()
    {
        return "DROP INDEX $this->indexName ON $this->tableName";
    }
}
