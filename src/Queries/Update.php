<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\NameIncrementor;
use PoK\SQLQueryBuilder\ValueObject\UpdateValue;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\ValueObject\Collection;

//$queryBuilder = (new Update('user_credentials'))
//    ->setValues(
//        (new UpdateValue())
//            ->setColumnName('representer')
//            ->setValue('new'),
//        (new UpdateValue())
//            ->setColumnName('test')
//            ->setValue(null)
//    )
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
//            new NotIn('user_id', [1,2,4]),
//            new NotLike('representer', '%admin%'),
//            new NotNull('type')
//        )
//    );
class Update implements CanCompile, CanCompilePrepareStatement
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

    // UPDATE `user_credentials`
    // SET `representer`="new", `test`=NULL WHERE (
    // `type` = 2 AND
    // `type` > 1 AND
    // `type` >= 2 AND
    // `user_id` IN (1,2,3,4,5) AND
    // `test` IS NULL AND
    // `representer` LIKE '%profile%' AND
    // (`type` > 2 OR `type` >= 2) AND
    // `type` < 3 AND `type` <= 2 AND
    // `type` <> 1 AND
    // `user_id` NOT IN (1,2,4) AND
    // `representer` NOT LIKE '%admin%' AND
    // `type` IS NOT NULL
    // )
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

    // UPDATE `user_credentials`
    // SET `representer`=:s, `test`=:t WHERE (
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
    // `user_id` NOT IN (:o, :p, :q) AND
    // `representer` NOT LIKE :r AND
    // `type` IS NOT NULL
    // )
    public function compilePrepare(): string
    {
        $this->validateQuery();

        $where = $this->where instanceof QueryCondition
            ? ' WHERE ' . ($this->where instanceof CanCompilePrepareStatement
                ? $this->where->compilePrepare()
                : $this->where->compile())
            : '';

        $values = (new Collection($this->values))
            ->map(function (UpdateValue $value) {
                return $value->getPreparedQuery();
            });

        return sprintf(
            'UPDATE `%s` SET %s%s',
            implode('`, `', $this->tableNames),
            implode(', ', $values->toArray()),
            $where
        );
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
    //    [":q"]=> 4
    //    [":r"]=> "%admin%"
    //    [":s"]=> "new"
    //    [":t"]=> NULL
    //  ]
    //]
    public function compileExecute(): array
    {
        $values = $this->where instanceof CanCompilePrepareStatement
            ? $this->where->compileExecute()
            : [];

        (new Collection($this->values))
            ->each(function (UpdateValue $value) use (&$values) {
                $values = array_merge($values, $value->getExecuteValue());
            });

        return [$values];
    }

    private function getValuePlaceholders()
    {
        if (!$this->valuePlaceholders)
            $this->valuePlaceholders = NameIncrementor::multipleNext(count($this->columnNames), ':');

        return $this->valuePlaceholders;
    }
}
