<?php

namespace PoK\SQLQueryBuilder\ValueObject;

use PoK\SQLQueryBuilder\Exceptions\Builder\MissingValueException;

class UpdateValue
{
    private $tableName;
    private $fieldName;
    private $hasValue = false;
    private $value;
    private $expression;
    private $fromTableName;
    private $fromFieldName;

    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function setFieldName(string $fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    public function setValue($value)
    {
        $this->hasValue = true;
        $this->value = $value;
        return $this;
    }

    public function setExpression($expression)
    {
        $this->expression = $expression;
        return $this;
    }

    public function setFromTableName(string $tableName)
    {
        $this->fromTableName = $tableName;
        return $this;
    }

    public function setFromFieldName(string $fieldName)
    {
        $this->fromFieldName = $fieldName;
        return $this;
    }

    public function __toString()
    {
        $updateQuery = '';
        if ($this->tableName) $updateQuery .= "`$this->tableName`.";
        $updateQuery .= "`$this->fieldName`=";

        switch (true) {
            case $this->hasValue:
                $updateQuery .= $this->compileValue();
                break;
            case $this->expression !== null:
                $updateQuery .= $this->expression;
                break;
            case $this->fromFieldName !== null:
                $updateQuery .= $this->compileFromFieldName();
                break;
            default:
                throw new MissingValueException();
        }

        return $updateQuery;
    }

    private function compileValue()
    {
        switch (true) {
            case $this->value === null:
                return 'NULL';
            case is_string($this->value):
                return sprintf('"%s"', $this->value);
            default:
                return $this->value;
        }
    }

    private function compileFromFieldName()
    {
        return ($this->fromTableName)
            ? "`$this->fromTableName`.`$this->fromFieldName`"
            : "`$this->fromFieldName`";
    }
}
