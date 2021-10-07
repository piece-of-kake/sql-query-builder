<?php

namespace PoK\SQLQueryBuilder\Table\Columns;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Primary;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Unique;

class Decimal implements CanCompile, Primary, Unique
{
    private $name;
    private $size;
    private $isPrimary = false;
    private $isUnique = false;
    private $decimals;
    private $nullable = true;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function size(int $size)
    {
        $this->size = $size;
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

    public function decimals(int $decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function notNull()
    {
        $this->nullable = false;
        return $this;
    }

    public function compile()
    {
        $size = '';
        if ($this->size) {
            $size = sprintf(
                '(%d, %d)',
                $this->size,
                $this->decimals ? : 0
            );
        }

        return sprintf(
            '`%s` DECIMAL%s %s',
            $this->name,
            $size,
            $this->nullable ? 'NULL' : 'NOT NULL'
        );
    }
}