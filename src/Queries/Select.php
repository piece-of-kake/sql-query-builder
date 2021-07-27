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
    private $columnNames = [];

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
     * @param string $columnName
     * @return Select
     */
    public function column(string $columnName): Select
    {
        $this->columnNames[] = $columnName;
        return $this;
    }

    /**
     * @param [string] $columnNames
     * @return Select
     */
    public function columns(string ...$columnNames): Select
    {
        $this->columnNames = array_merge($this->columnNames, $columnNames);
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
     * @param string $columnName
     * @return Select
     */
    public function groupBy(string $columnName): Select
    {
        $this->groupBy = $columnName;
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
        $clone = (new static($this->tableName))->column('count(*)');
        if ($this->where instanceof QueryCondition) $clone->where($this->where);
        // Other parameters re not relevant for total count
        return $clone;
    }

    public function compile()
    {
        $this->validateQuery();

        $columnNames = empty($this->columnNames)
            ? '*'
            : implode(',', $this->columnNames);

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

        return "SELECT $columnNames FROM `$this->tableName`$where$groupBy$orderBy$limit$offset";
    }

    /**
     * @throws MissingTableNameException
     */
    private function validateQuery()
    {
        if (!$this->tableName) throw new MissingTableNameException();
    }
}
