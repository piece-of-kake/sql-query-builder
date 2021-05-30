<?php

namespace PoK\SQLQueryBuilder\Table\Fields;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

/**
 * ItnF because Int is a reserved word
 */
class Decimal implements CanCompile
{
    private $name;
    private $size;
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
            '`%s` decimal%s %s',
            $this->name,
            $size,
            $this->nullable ? 'NULL' : 'NOT NULL'
        );
    }
}