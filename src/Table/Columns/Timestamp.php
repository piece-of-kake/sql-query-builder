<?php

namespace PoK\SQLQueryBuilder\Table\Columns;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class Timestamp implements CanCompile
{
    private $name;
    private $nullable = true;
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

    public function default($default = null)
    {
        $this->hasDefault = true;
        $this->default = $default;
        return $this;
    }

    public function compile()
    {
        return sprintf(
            '`%s` timestamp %s %s',
            $this->name,
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->hasDefault
                ? ($this->default ? "DEFAULT $this->default" : 'DEFAULT NULL')
                : ''
        );
    }
}