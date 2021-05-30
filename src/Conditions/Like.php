<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingFieldNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;

class Like implements QueryCondition
{
    private $fieldName;
    private $pattern;

    public function __construct(string $fieldName, $pattern)
    {
        $this->fieldName = $fieldName;
        $this->pattern = $pattern;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            '`%s` LIKE \'%s\'',
            $this->fieldName,
            $this->pattern
        );
    }

    private function validateCondition()
    {
        if (!$this->fieldName) throw new MissingFieldNameException();
        if ($this->pattern === null) throw new MissingValueException();
    }
}