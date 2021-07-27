<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNamesException;
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
    private $columnNames = [];

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
     * @param [string] $columnNames
     * @return Insert
     */
    public function columns(string ...$columnNames)
    {
        $this->columnNames = $columnNames;
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

        $columnNames = sprintf('`%s`', implode('`, `', $this->columnNames));
        $values = [];
        foreach ($this->rows as $row) {
            $values[] = sprintf("'%s'", implode("', '", $row));
        }
        $values = sprintf('(%s)', implode('), (', $values));
        return "INSERT INTO `$this->tableName` ($columnNames) VALUES $values";
    }

    /**
     * @throws MissingTableNameException
     * @throws MissingColumnNamesException
     * @throws MissingValuesException
     */
    private function validateQuery()
    {
        if (!$this->tableName) throw new MissingTableNameException();
        if (empty($this->columnNames)) throw new MissingColumnNamesException();
        if (empty($this->rows)) throw new MissingValuesException();
    }
}
