<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\ValueObject\UpdateValue;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;

class Update implements CanCompile
{
    private $tableNames = [];
    private $where;
    private $values = [];

    public function __construct(string ...$tableNames)
    {
        $this->tableNames = $tableNames;
    }

    public function where(QueryCondition $condition)
    {
        $this->where = $condition;
        return $this;
    }

    public function setValue(UpdateValue $updateValue)
    {
        $this->values[] = $updateValue;
        return $this;
    }

    public function setValues(UpdateValue ...$updateValues)
    {
        $this->values = $updateValues;
        return $this;
    }

    public function compile()
    {
        $this->validateQuery();

        $where = $this->where instanceof QueryCondition
            ? ' WHERE ' . $this->where->compile()
            : '';

        return sprintf(
            'UPDATE `%s` SET %s%s',
            implode('`, `', $this->tableNames),
            implode(', ', $this->values),
            $where
        );
    }

    /**
     * @throws MissingTableNameException
     * @throws MissingValuesException
     */
    private function validateQuery()
    {
        if (empty($this->tableNames)) throw new MissingTableNameException();
        if (empty($this->values)) throw new MissingValuesException();
    }
}
