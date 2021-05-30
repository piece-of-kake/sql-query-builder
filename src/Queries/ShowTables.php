<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\IsCollectable;
use PoK\SQLQueryBuilder\Interfaces\IsDataType;

class ShowTables implements CanCompile, IsCollectable, IsDataType
{

    public function getDataType(): int
    {
        return \PDO::FETCH_COLUMN;
    }

    public function compile()
    {
        return "SHOW TABLES";
    }
}
