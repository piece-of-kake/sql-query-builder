<?php

namespace PoK\SQLQueryBuilder\Table\Columns;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Primary;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Unique;

class Timestamp implements CanCompile, Primary, Unique
{
    private $name;
    private $nullable = true;
    private $isPrimary = false;
    private $isUnique = false;
    private $hasDefault = false;
    private $default;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function notNull()
    {
        $this->nullable = false;
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

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    public function default($default = null)
    {
        $this->hasDefault = true;
        $this->default = $default;
        return $this;
    }

    public function compile()
    {
        return sprintf(
            '`%s` TIMESTAMP %s%s',
            $this->name,
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->hasDefault
                ? ($this->default ? " DEFAULT $this->default" : ' DEFAULT NULL')
                : ''
        );
    }
}