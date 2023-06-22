<?php

namespace PoK\SQLQueryBuilder\Conditions;

use PoK\SQLQueryBuilder\Interfaces\CanCompilePrepareStatement;
use PoK\SQLQueryBuilder\Interfaces\QueryCondition;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingColumnNameException;
use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;
use PoK\SQLQueryBuilder\NameIncrementor;
use PoK\ValueObject\Collection;

class MatchAgainst implements QueryCondition, CanCompilePrepareStatement
{
    /** @var Collection  */
    private $columnNames;
    /** @var string  */
    private $value;
    /** @var  */
    private $valuePlaceholder;

    /** @var bool  */
    private $isBooleanMode = false;
    /** @var bool  */
    private $isWithQueryExpansionMode = false;
    /** @var bool  */
    private $isNaturalLanguageMode = false;

    public function __construct(string $value, string ...$columnNames )
    {
        $this->value = $value;
        $this->columnNames = new Collection($columnNames);
    }

    public function setBooleanMode(): MatchAgainst
    {
        $this->isBooleanMode = true;
        return $this;
    }

    public function setNaturalLanguageMode(): MatchAgainst
    {
        $this->isNaturalLanguageMode = true;
        return $this;
    }

    public function setWithQueryExpansionMode(): MatchAgainst
    {
        $this->isWithQueryExpansionMode = true;
        return $this;
    }

    public function compile()
    {
        $this->validateCondition();

        $mode = $this->isNaturalLanguageMode
            ? " IN NATURAL LANGUAGE MODE"
            : ($this->isWithQueryExpansionMode
                ? " WITH QUERY EXPANSION"
                : ($this->isBooleanMode
                    ? " IN BOOLEAN MODE"
                    : ""));

        $columnNames = implode(", ", $this->columnNames
            ->map(function (string $columnName) {
                return "`$columnName`";
            })
            ->toArray()
        );

        return sprintf(
            'MATCH (%s) AGAINST("%s"%s)',
            $columnNames,
            $this->value,
            $mode
        );
    }

    private function validateCondition()
    {
        if (!$this->columnNames) throw new MissingColumnNameException();
        if ($this->value === null) throw new MissingValueException();
    }

    public function compilePrepare(): string
    {
        $this->validateCondition();

        $mode = $this->isNaturalLanguageMode
            ? " IN NATURAL LANGUAGE MODE"
            : ($this->isWithQueryExpansionMode
                ? " WITH QUERY EXPANSION"
                : ($this->isBooleanMode
                    ? " IN BOOLEAN MODE"
                    : ""));

        $columnNames = implode(", ", $this->columnNames
            ->map(function (string $columnName) {
                return "`$columnName`";
            })
            ->toArray()
        );

        return sprintf(
            'MATCH (%s) AGAINST(%s%s)',
            $columnNames,
            $this->getValuePlaceholder(),
            $mode
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