<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingFieldNamesException;
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
    private $fieldNames = [];

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
     * @param [string] $fieldNames
     * @return InsertOrUpdate
     */
    public function fields(string ...$fieldNames)
    {
        $this->fieldNames = $fieldNames;
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


        $fieldNames = sprintf('`%s`', implode('`, `', $this->fieldNames));
        $values = sprintf("'%s'", implode("', '", $this->values));

        $updateValues = [];
        foreach ($this->fieldNames as $pointer => $fieldName) {
            $updateValues[] = sprintf("`%s`='%s'", $fieldName, $this->values[$pointer]);
        }
        $updateValues = implode(', ', $updateValues);

        return "INSERT INTO `$this->tableName` ($fieldNames) VALUES ($values) ON DUPLICATE KEY UPDATE $updateValues";
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
        if (empty($this->values)) throw new MissingValuesException();
    }
}
