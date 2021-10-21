<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Table\OrderBy;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Interfaces\IsCollectable;
use PoK\SQLQueryBuilder\Interfaces\CanPaginate;
use PoK\SQLQueryBuilder\Interfaces\IsDataType;
use PoK\ValueObject\Pagination;

//$queryBuilder = (new Select('user_credentials'))
//    ->where(
//        new LAnd(
//            new Equal('type', 2),
//            new GT('type', 1),
//            new GTE('type', 2),
//            new In('user_id', [1,2,3,4,5]),
//            new IsNull('test'),
//            new Like('representer', '%admin%'),
//            new LOr(
//                new GT('type', 2),
//                new GTE('type', 2)
//            ),
//            new LT('type', 3),
//            new LTE('type', 2),
//            new NotEqual('type', 1),
//            new NotIn('user_id', [2,3,4,5]),
//            new NotLike('representer', '%profile%'),
//            new NotNull('type')
//        )
//    );
class Select implements CanCompile, CanCompilePrepareStatement, IsCollectable, CanPaginate, IsDataType
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

    // SELECT * FROM `user_credentials`
    // WHERE (
    //  `type` = 2 AND
    //  `type` > 1 AND
    //  `type` >= 2 AND
    //  `user_id` IN (1,2,3,4,5) AND
    //  `test` IS NULL AND
    //  `representer` LIKE '%admin%' AND
    //  (`type` > 2 OR `type` >= 2) AND
    //  `type` < 3 AND
    //  `type` <= 2 AND
    //  `type` <> 1 AND
    //  `user_id` NOT IN (2,3,4,5) AND
    //  `representer` NOT LIKE '%profile%' AND
    //  `type` IS NOT NULL
    // )
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

    // SELECT * FROM `user_credentials` WHERE (
    // `type` = :a AND
    // `type` > :b AND
    // `type` >= :c AND
    // `user_id` IN (:d, :e, :f, :g, :h) AND
    // `test` IS NULL AND
    // `representer` LIKE :i AND
    // (`type` > :j OR `type` >= :k) AND
    // `type` < :l AND
    // `type` <= :m AND
    // `type` <> :n AND
    // `user_id` NOT IN (:o, :p, :q, :r) AND
    // `representer` NOT LIKE :s AND
    // `type` IS NOT NULL)
    public function compilePrepare(): string
    {
        $this->validateQuery();

        $columnNames = empty($this->columnNames)
            ? '*'
            : implode(',', $this->columnNames);

        $where = $this->where instanceof QueryCondition
            ? ' WHERE ' . ($this->where instanceof CanCompilePrepareStatement
                ? $this->where->compilePrepare()
                : $this->where->compile())
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

    // [
    //   [
    //    [":a"]=> 2
    //    [":b"]=> 1
    //    [":c"]=> 2
    //    [":d"]=> 1
    //    [":e"]=> 2
    //    [":f"]=> 3
    //    [":g"]=> 4
    //    [":h"]=> 5
    //    [":i"]=> "%admin%"
    //    [":j"]=> 2
    //    [":k"]=> 2
    //    [":l"]=> 3
    //    [":m"]=> 2
    //    [":n"]=> 1
    //    [":o"]=> 2
    //    [":p"]=> 3
    //    [":q"]=> 4
    //    [":r"]=> 5
    //    [":s"]=> "%profile%"
    //   ]
    // ]
    public function compileExecute(): array
    {
        return $this->where instanceof CanCompilePrepareStatement
            ? [$this->where->compileExecute()]
            : [];
    }
}
