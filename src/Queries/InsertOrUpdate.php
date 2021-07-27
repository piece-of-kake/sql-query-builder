<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNamesException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\SQLQueryBuilder\Interfaces\LastInsertId;

class InsertOrUpdate implements CanCompile, LastInsertId
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
    private $values = [];

    /**
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param [string] $columnNames
     * @return InsertOrUpdate
     */
    public function columns(string ...$columnNames)
    {
        $this->columnNames = $columnNames;
        return $this;
    }

    /**
     * @param [*] $values
     * @return InsertOrUpdate
     */
    public function values(...$values)
    {
        $this->values = $values;
        return $this;
    }

    public function compile()
    {
        $this->validateQuery();


        $columnNames = sprintf('`%s`', implode('`, `', $this->columnNames));
        $values = sprintf("'%s'", implode("', '", $this->values));

        $updateValues = [];
        foreach ($this->columnNames as $pointer => $columnName) {
            $updateValues[] = sprintf("`%s`='%s'", $columnName, $this->values[$pointer]);
        }
        $updateValues = implode(', ', $updateValues);

        return "INSERT INTO `$this->tableName` ($columnNames) VALUES ($values) ON DUPLICATE KEY UPDATE $updateValues";
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
        if (empty($this->values)) throw new MissingValuesException();
    }
}
