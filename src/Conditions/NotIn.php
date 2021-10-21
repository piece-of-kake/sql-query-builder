<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValuesException;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\NameIncrementor;
use PoK\ValueObject\Collection;

class NotIn implements QueryCondition, CanCompilePrepareStatement
{
    private $columnName;
    /**
     * @var array | CanCompile | CanCompilePrepareStatement
     */
    private $values;
    private $valuePlaceholders;

    public function __construct(string $columnName, $values)
    {
        $this->columnName = $columnName;
        $this->values = $values;
    }

    public function compile()
    {
        $this->validateCondition();

        if ($this->values instanceof CanCompile) {
            $compiledValues = $this->values->compile();
        } else {
            $compiledValues = array_map(function ($value) {
                return is_string($value)
                    ? "'$value'"
                    : (string)$value;
            }, $this->values);
            $compiledValues = implode(',', $compiledValues);
        }

        return sprintf('`%s` NOT IN (%s)', $this->columnName, $compiledValues);
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if (
            ($this->values === null || !is_array($this->values) || empty($this->values)) &&
            !($this->values instanceof CanCompile)
        ) throw new MissingValuesException();
    }

    public function compilePrepare(): string
    {
        $this->validateCondition();

        if ($this->values instanceof CanCompilePrepareStatement) {
            $compiledValues = $this->values->compilePrepare();
        } else if ($this->values instanceof CanCompile) {
            $compiledValues = $this->values->compile();
        } else {
            $compiledValues = implode(', ', $this->getValuePlaceholders());
        }

        return sprintf('`%s` NOT IN (%s)', $this->columnName, $compiledValues);
    }

    public function compileExecute(): array
    {
        $this->validateCondition();

        if ($this->values instanceof CanCompilePrepareStatement) {
            return $this->values->compileExecute();
        } else if (is_array($this->values)) {
            return (new Collection($this->values))->replaceKeys($this->getValuePlaceholders())->toArray();
        }
        return [];
    }

    private function getValuePlaceholders()
    {
        if (!$this->valuePlaceholders)
            $this->valuePlaceholders = NameIncrementor::multipleNext(count($this->values), ':');

        return $this->valuePlaceholders;
    }
}