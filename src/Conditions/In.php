<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingFieldNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class In implements QueryCondition
{
    private $fieldName;
    private $values;

    public function __construct(string $fieldName, $values)
    {
        $this->fieldName = $fieldName;
        $this->values = $values;
    }

    public function compile()
    {
        $this->validateCondition();

        if ($this->values instanceof CanCompile) {
            $compiledValues = $this->values->compile();
        } else {
            $compiledValues = array_map(function ($value) {
                return is_string($value)
                    ? "'$value'"
                    : (string)$value;
            }, $this->values);
            $compiledValues = implode(',', $compiledValues);
        }

        return sprintf('`%s` IN (%s)', $this->fieldName, $compiledValues);
    }

    private function validateCondition()
    {
        if (!$this->fieldName) throw new MissingFieldNameException();
        if (
            ($this->values === null || !is_array($this->values) || empty($this->values)) &&
            !($this->values instanceof CanCompile)
        ) throw new MissingValuesException();
    }
}