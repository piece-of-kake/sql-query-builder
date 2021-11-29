<?php

namespace PoK\SQLQueryBuilder\Table\Columns;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Primary;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Unique;

/**
 * ItnF because Int is a reserved word
 */
class IntF implements CanCompile, Primary, Unique
{
    private $name;
    private $size;
    private $isAutoIncrement = false;
    private $nullable = true;
    private $isPrimary = false;
    private $isUnique = false;
    private $unsigned = false;
    private $hasDefault = false;
    private $default;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function size(int $size)
    {
        $this->size = $size;
        return $this;
    }

    public function autoIncrement()
    {
        $this->isAutoIncrement = true;
        return $this;
    }

    public function primary()
    {
        $this->isPrimary = true;
        return $this;
    }

    public function unique()
    {
        $this->isUnique = true;
        return $this;
    }

    public function notNull()
    {
        $this->nullable = false;
        return $this;
    }

    public function unsigned()
    {
        $this->unsigned = true;
        return $this;
    }

    public function default($default = null)
    {
        $this->hasDefault = true;
        $this->default = $default;
        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    public function compile()
    {
        return sprintf(
            '`%s` INT(%s) %s %s%s%s',
            $this->name,
            $this->size ? $this->size : '',
            $this->unsigned ? 'UNSIGNED' : '',
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->hasDefault
                ? ($this->default !== null ? " DEFAULT $this->default" : ' DEFAULT NULL')
                : '',
            $this->isAutoIncrement ? ' AUTO_INCREMENT' : ''
        );
    }
}