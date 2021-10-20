<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingTableNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNamesException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\LastInsertId;
use PoK\SQLQueryBuilder\NameIncrementor;
use PoK\ValueObject\Collection;

class Insert implements CanCompile, LastInsertId, CanCompilePrepareStatement
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
     * @var array
     */
    private $valuePlaceholders;

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

    public function compilePrepare()
    {
        $this->validateQuery();
        $columnNames = sprintf('`%s`', implode('`, `', $this->columnNames));

        return sprintf("INSERT INTO `%s` (%s) VALUES (%s)",
            $this->tableName,
            $columnNames,
            implode(', ', $this->getValuePlaceholders())
        );
    }

    private function getValuePlaceholders()
    {
        if (!$this->valuePlaceholders)
            $this->valuePlaceholders = NameIncrementor::multipleNext(count($this->columnNames), ':');

        return $this->valuePlaceholders;
    }

    public function compileExecute()
    {
        $this->validateQuery();

        $values = [];
        foreach ($this->rows as $row) {
            $values[] = (new Collection($row))->replaceKeys($this->getValuePlaceholders())->toArray();
        }

        return $values;
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
