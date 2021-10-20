<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;
use PoK\SQLQueryBuilder\NameIncrementor;

class Like implements QueryCondition, CanCompilePrepareStatement
{
    private $columnName;
    private $pattern;
    private $valuePlaceholder;

    public function __construct(string $columnName, $pattern)
    {
        $this->columnName = $columnName;
        $this->pattern = $pattern;
    }

    public function compile()
    {
        $this->validateCondition();
        return sprintf(
            '`%s` LIKE \'%s\'',
            $this->columnName,
            $this->pattern
        );
    }

    private function validateCondition()
    {
        if (!$this->columnName) throw new MissingColumnNameException();
        if ($this->pattern === null) throw new MissingValueException();
    }

    public function compilePrepare()
    {
        $this->validateCondition();

        return sprintf(
            '`%s` LIKE %s',
            $this->columnName,
            $this->getValuePlaceholder()
        );
    }

    public function compileExecute()
    {
        $this->validateCondition();

        return [
            $this->getValuePlaceholder() => $this->pattern
        ];
    }

    private function getValuePlaceholder()
    {
        if (!$this->valuePlaceholder)
            $this->valuePlaceholder = NameIncrementor::next(':');

        return $this->valuePlaceholder;
    }
}