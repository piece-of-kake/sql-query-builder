<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;

class Like implements QueryCondition
{
    private $columnName;
    private $pattern;

    public function __construct(string $columnName, $pattern)
    {
        $this->columnName = $columnName;
        $this->pattern = $pattern;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            '`%s` LIKE \'%s\'',
            $this->columnName,
            $this->pattern
        );
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if ($this->pattern === null) throw new MissingValueException();
    }
}