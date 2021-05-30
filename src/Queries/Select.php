<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\OrderBy;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Interfaces\IsCollectable;
use PoK\SQLQueryBuilder\Interfaces\CanPaginate;
use PoK\SQLQueryBuilder\Interfaces\IsDataType;
use PoK\ValueObject\Pagination;

class Select implements CanCompile, IsCollectable, CanPaginate, IsDataType
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $fieldNames = [];

    /**
     * @var QueryCondition
     */
    private $where;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string
     */
    private $groupBy;

    /**
     * @var OrderBy
     */
    private $orderBy;

    /**
     * @var Pagination
     */
    private $pagination;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param string $fieldName
     * @return Select
     */
    public function field(string $fieldName): Select
    {
        $this->fieldNames[] = $fieldName;
        return $this;
    }

    /**
     * @param [string] $fieldNames
     * @return Select
     */
    public function fields(string ...$fieldNames): Select
    {
        $this->fieldNames = array_merge($this->fieldNames, $fieldNames);
        return $this;
    }

    /**
     * @param QueryCondition $condition
     * @return Select
     */
    public function where(QueryCondition $condition): Select
    {
        $this->where = $condition;
        return $this;
    }

    /**
     * @param int $limit
     * @return Select
     */
    public function limit(int $limit): Select
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return Select
     */
    public function offset(int $offset): Select
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param string $fieldName
     * @return Select
     */
    public function groupBy(string $fieldName): Select
    {
        $this->groupBy = $fieldName;
        return $this;
    }

    /**
     * @param OrderBy $orderBy
     * @return Select
     */
    public function orderBy(OrderBy $orderBy): Select
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function paginate(Pagination $pagination): Select
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function hasPagination(): bool
    {
        return $this->pagination instanceof Pagination;
    }

    public function getDataType(): int
    {
        return \PDO::FETCH_ASSOC;
    }

    public function cloneForTotalCount(): Select
    {
        $clone = (new static($this->tableName))->field('count(*)');
        if ($this->where instanceof QueryCondition) $clone->where($this->where);
        // Other parameters re not relevant for total count
        return $clone;
    }

    public function compile()
    {
        $this->validateQuery();

        $fieldNames = empty($this->fieldNames)
            ? '*'
            : implode(',', $this->fieldNames);

        $where = $this->where instanceof QueryCondition
            ? ' WHERE ' . $this->where->compile()
            : '';

        $groupBy = $this->groupBy === null
            ? ''
            : ' GROUP BY ' . $this->groupBy;

        $orderBy = $this->orderBy instanceof OrderBy
            ? $this->orderBy->compile()
            : '';

        $limit = $this->hasPagination()
            ? sprintf(' LIMIT %s', $this->pagination->getPerPage()->getValue())
            : ($this->limit === null
                ? ''
                : " LIMIT $this->limit");

        $offset = $this->hasPagination()
            ? sprintf(' OFFSET %s', ($this->pagination->getPage()->getValue() - 1) * $this->pagination->getPerPage()->getValue())
            : ($this->offset === null
                ? ''
                : " OFFSET $this->offset");

        return "SELECT $fieldNames FROM `$this->tableName`$where$groupBy$orderBy$limit$offset";
    }

    /**
     * @throws MissingTableNameException
     */
    private function validateQuery()
    {
        if (!$this->tableName) throw new MissingTableNameException();
    }
}
