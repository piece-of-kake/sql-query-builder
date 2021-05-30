<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingFieldNameException;

class IsNull implements QueryCondition
{
    private $fieldName;

    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf('`%s` IS NULL', $this->fieldName);
    }

    private function validateCondition()
    {
        if (!$this->fieldName) throw new MissingFieldNameException();
    }
}