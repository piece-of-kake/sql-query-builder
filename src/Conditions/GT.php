<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingFieldNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;

class GT implements QueryCondition
{
    private $fieldName;
    private $value;

    public function __construct(string $fieldName, $value)
    {
        $this->fieldName = $fieldName;
        $this->value = $value;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            '`%s` > %s',
            $this->fieldName,
            is_string($this->value)
                ? "'$this->value'"
                : (string)$this->value
        );
    }

    private function validateCondition()
    {
        if (!$this->fieldName) throw new MissingFieldNameException();
        if ($this->value === null) throw new MissingValueException();
    }
}