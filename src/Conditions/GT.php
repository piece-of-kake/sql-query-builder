<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;

class GT implements QueryCondition
{
    private $columnName;
    private $value;

    public function __construct(string $columnName, $value)
    {
        $this->columnName = $columnName;
        $this->value = $value;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            '`%s` > %s',
            $this->columnName,
            is_string($this->value)
                ? "'$this->value'"
                : (string)$this->value
        );
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if ($this->value === null) throw new MissingValueException();
    }
}