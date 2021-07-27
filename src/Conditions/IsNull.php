<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;

class IsNull implements QueryCondition
{
    private $columnName;

    public function __construct(string $columnName)
    {
        $this->columnName = $columnName;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf('`%s` IS NULL', $this->columnName);
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
    }
}