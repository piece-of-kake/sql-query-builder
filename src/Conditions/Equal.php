<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;
use PoK\SQLQueryBuilder\NameIncrementor;

class Equal implements QueryCondition, CanCompilePrepareStatement
{
    private $columnName;
    private $value;
    private $valuePlaceholder;

    public function __construct(string $columnName, $value)
    {
        $this->columnName = $columnName;
        $this->value = $value;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            '`%s` = %s',
            $this->columnName,
            is_string($this->value)
                ? "'$this->value'"
                : (string)$this->value
        );
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if ($this->value === null) throw new MissingValueException();
    }

    public function compilePrepare()
    {
        $this->validateCondition();

        return sprintf(
            '`%s` = %s',
            $this->columnName,
            $this->getValuePlaceholder()
        );
    }

    public function compileExecute()
    {
        $this->validateCondition();

        return [
            $this->getValuePlaceholder() => $this->value
        ];
    }

    private function getValuePlaceholder()
    {
        if (!$this->valuePlaceholder)
            $this->valuePlaceholder = NameIncrementor::next(':');

        return $this->valuePlaceholder;
    }
}