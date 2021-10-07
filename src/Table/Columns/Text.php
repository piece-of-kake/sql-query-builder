<?php

namespace PoK\SQLQueryBuilder\Table\Columns;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class Text implements CanCompile
{
    private $name;
    private $nullable = true;
    private $collation;
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

    public function collation(string $collation)
    {
        $this->collation = $collation;
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
            '`%s` TEXT%s %s%s',
            $this->name,
            $this->collation ? " COLLATE $this->collation" : '',
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->hasDefault
                ? ($this->default ? " DEFAULT $this->default" : ' DEFAULT NULL')
                : ''
        );
    }
}