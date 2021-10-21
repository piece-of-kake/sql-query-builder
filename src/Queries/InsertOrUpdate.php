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

class InsertOrUpdate implements CanCompile, CanCompilePrepareStatement, LastInsertId
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
     * @return InsertOrUpdate
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

        $updateValues = [];
        foreach ($this->columnNames as $columnName) {
            $updateValues[] = sprintf("`%s` = VALUES(`%s`)", $columnName, $columnName);
        }
        $updateValues = implode(', ', $updateValues);

        return "INSERT INTO `$this->tableName` ($columnNames) VALUES $values ON DUPLICATE KEY UPDATE $updateValues";
    }

//INSERT INTO `table_name`(`column_1`, `column_2`, `column_3`, `column_4`, `column_5`) VALUES
//('value_11','value_12','value_13','value_14','value_15'),
//('value_21','value_22','value_23','value_24','value_25')
//ON DUPLICATE KEY UPDATE
//`column_1` = VALUES(`column_1`),
//`column_2` = VALUES(`column_2`),
//`column_3` = VALUES(`column_3`),
//`column_4` = VALUES(`column_4`),
//`column_5` = VALUES(`column_5`);

    public function compilePrepare(): string
    {
        $this->validateQuery();

        $columnNames = sprintf('`%s`', implode('`, `', $this->columnNames));

        $updateValues = [];
        foreach ($this->columnNames as $columnName) {
            $updateValues[] = sprintf("`%s` = VALUES(`%s`)", $columnName, $columnName);
        }
        $updateValues = implode(', ', $updateValues);

        return sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
            $this->tableName,
            $columnNames,
            implode(', ', $this->getValuePlaceholders()),
            $updateValues
        );
    }

    public function compileExecute(): array
    {
        $this->validateQuery();

        $values = [];
        foreach ($this->rows as $row) {
            $values[] = (new Collection($row))->replaceKeys($this->getValuePlaceholders())->toArray();
        }

        return $values;
    }

    private function getValuePlaceholders()
    {
        if (!$this->valuePlaceholders)
            $this->valuePlaceholders = NameIncrementor::multipleNext(count($this->columnNames), ':');

        return $this->valuePlaceholders;
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
