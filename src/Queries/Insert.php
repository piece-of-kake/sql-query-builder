<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingFieldNamesException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\SQLQueryBuilder\Interfaces\LastInsertId;

class Insert implements CanCompile, LastInsertId
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
     * @var array
     */
    private $rows = [];

    /**
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param [string] $fieldNames
     * @return Insert
     */
    public function fields(string ...$fieldNames)
    {
        $this->fieldNames = $fieldNames;
        return $this;
    }

    /**
     * @param [*] $values
     * @return Insert
     */
    public function addValueRow(...$values)
    {
        $this->rows[] = $values;
        return $this;
    }

    public function compile()
    {
        $this->validateQuery();

        $fieldNames = sprintf('`%s`', implode('`, `', $this->fieldNames));
        $values = [];
        foreach ($this->rows as $row) {
            $values[] = sprintf("'%s'", implode("', '", $row));
        }
        $values = sprintf('(%s)', implode('), (', $values));
        return "INSERT INTO `$this->tableName` ($fieldNames) VALUES $values";
    }

    /**
     * @throws MissingTableNameException
     * @throws MissingFieldNamesException
     * @throws MissingValuesException
     */
    private function validateQuery()
    {
        if (!$this->tableName) throw new MissingTableNameException();
        if (empty($this->fieldNames)) throw new MissingFieldNamesException();
        if (empty($this->rows)) throw new MissingValuesException();
    }
}
