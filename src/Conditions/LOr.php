<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\InvalidNumberOfConditionsException;

class LOr implements QueryCondition
{
    private $conditions = [];

    public function __construct(QueryCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public function compile()
    {
        $this->validateCondition();

        $compiledArray = array_map(function ($condition) {
            return $condition->compile();
        }, $this->conditions);
        return sprintf('(%s)', implode(' OR ', $compiledArray));
    }

    private function validateCondition()
    {
        if (count($this->conditions) < 2) throw new InvalidNumberOfConditionsException();
    }
}
