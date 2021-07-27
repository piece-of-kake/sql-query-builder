<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class NotIn implements QueryCondition
{
    private $columnName;
    private $values;

    public function __construct(string $columnName, $values)
    {
        $this->columnName = $columnName;
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

        return sprintf('`%s` NOT IN (%s)', $this->columnName, $compiledValues);
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if (
            ($this->values === null || !is_array($this->values) || empty($this->values)) &&
            !($this->values instanceof CanCompile)
        ) throw new MissingValuesException();
    }
}