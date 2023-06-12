<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;
use PoK\SQLQueryBuilder\NameIncrementor;

class MatchAgainst implements QueryCondition, CanCompilePrepareStatement
{
    private $columnName;
    private $value;
    private $valuePlaceholder;

    public function __construct(string $columnName, string $value)
    {
        $this->columnName = $columnName;
        $this->value = $value;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            'MATCH (`%s` ) AGAINST("%s")',
            $this->columnName,
            $this->value
        );
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if ($this->value === null) throw new MissingValueException();
    }

    public function compilePrepare(): string
    {
        $this->validateCondition();

        return sprintf(
            'MATCH (`%s` ) AGAINST(%s)',
            $this->columnName,
            $this->getValuePlaceholder()
        );
    }

    public function compileExecute(): array
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