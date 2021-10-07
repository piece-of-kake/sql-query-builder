<?php

namespace PoK\SQLQueryBuilder\Table\Columns;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Primary;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Unique;

class UnixTimestamp implements CanCompile, Primary, Unique
{
    private $name;
    private $isAutoIncrement = false;
    private $nullable = true;
    private $isPrimary = false;
    private $isUnique = false;

    public function __construct($name)
    {
        $this->name = $name;
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
            '`%s` INT(10) %s%s',
            $this->name,
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->isAutoIncrement ? ' AUTO_INCREMENT' : ''
        );
    }
}