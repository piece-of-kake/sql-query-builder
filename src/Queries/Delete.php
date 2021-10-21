<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Table\OrderBy;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;


//$queryBuilder = (new Delete('user_credentials'))
//    ->where(
//        new LAnd(
//            new Equal('type', 2),
//            new GT('type', 1),
//            new GTE('type', 2),
//            new In('user_id', [1,2,3,4,5]),
//            new IsNull('test'),
//            new Like('representer', '%profile%'),
//            new LOr(
//                new GT('type', 2),
//                new GTE('type', 2)
//            ),
//            new LT('type', 3),
//            new LTE('type', 2),
//            new NotEqual('type', 1),
//            new NotIn('user_id', [1,2,3,4]),
//            new NotLike('representer', '%admin%'),
//            new NotNull('type')
//        )
//    );
class Delete implements CanCompile, CanCompilePrepareStatement
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

    // DELETE FROM `user_credentials`
    // WHERE (
    //  `type` = 2 AND
    //  `type` > 1 AND
    //  `type` >= 2 AND
    //  `user_id` IN (1,2,3,4,5) AND
    //  `test` IS NULL AND
    //  `representer` LIKE '%profile%' AND
    //  (`type` > 2 OR `type` >= 2) AND
    //  `type` < 3 AND
    //  `type` <= 2 AND
    //  `type` <> 1 AND
    //  `user_id` NOT IN (1,2,3,4) AND
    //  `representer` NOT LIKE '%admin%' AND
    //  `type` IS NOT NULL
    // )
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

    // DELETE FROM `user_credentials` WHERE (
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

        $where = $this->where instanceof QueryCondition
            ? ' WHERE ' . ($this->where instanceof CanCompilePrepareStatement
                ? $this->where->compilePrepare()
                : $this->where->compile())
            : '';

        $orderBy = $this->orderBy instanceof OrderBy
            ? $this->orderBy->compile()
            : '';

        $limit = $this->limit === null
            ? ''
            : " LIMIT $this->limit";

        return "DELETE FROM `$this->tableName`$where$orderBy$limit";
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
    //    [":i"]=> "%profile%"
    //    [":j"]=> 2
    //    [":k"]=> 2
    //    [":l"]=> 3
    //    [":m"]=> 2
    //    [":n"]=> 1
    //    [":o"]=> 1
    //    [":p"]=> 2
    //    [":q"]=> 3
    //    [":r"]=> 4
    //    [":s"]=> "%admin%"
    //   ]
    // ]
    public function compileExecute(): array
    {
        return $this->where instanceof CanCompilePrepareStatement
            ? [$this->where->compileExecute()]
            : [];
    }
}
