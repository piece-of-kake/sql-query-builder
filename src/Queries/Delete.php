<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Table\OrderBy;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;

class Delete implements CanCompile
{
    private $tableName;
    private $where;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var OrderBy
     */
    private $orderBy;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function __toString()
    {
        return $this->compile();
    }

    public function where(QueryCondition $condition)
    {
        $this->where = $condition;
        return $this;
    }

    /**
     * @param int $limit
     * @return Delete
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param OrderBy $orderBy
     * @return Delete
     */
    public function orderBy(OrderBy $orderBy): Select
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function compile()
    {
        $this->validateQuery();

        $where = $this->where instanceof QueryCondition
            ? ' WHERE ' . $this->where->compile()
            : '';

        $orderBy = $this->orderBy instanceof OrderBy
            ? $this->orderBy->compile()
            : '';

        $limit = $this->limit === null
            ? ''
            : " LIMIT $this->limit";

        return "DELETE FROM `$this->tableName`$where$orderBy$limit";
    }

    private function validateQuery()
    {
        if (empty($this->tableName)) throw new MissingTableNameException();
    }
}
