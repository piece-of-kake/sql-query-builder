<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class DropTable implements CanCompile
{
    private $tableName;
    private $ifExists = false;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function ifExists(): DropTable
    {
        $this->ifExists = true;
        return $this;
    }

    public function compile()
    {
        $ifExists = $this->ifExists ? " IF EXISTS" : '';

        return "DROP TABLE$ifExists $this->tableName";
    }
}
