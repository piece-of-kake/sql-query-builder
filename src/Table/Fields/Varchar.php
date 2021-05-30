<?php

namespace PoK\SQLQueryBuilder\Table\Fields;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Fields\Interfaces\PrimaryField;
use PoK\SQLQueryBuilder\Table\Fields\Interfaces\UniqueField;

class Varchar implements CanCompile, PrimaryField, UniqueField
{
    private $name;
    private $size;
    private $nullable = true;
    private $collation;
    private $hasDefault = false;
    private $default;
    private $isPrimary = false;
    private $isUnique = false;

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
            '`%s` varchar(%s) %s %s %s',
            $this->name,
            $this->size ? $this->size : '',
            $this->collation ? "COLLATE $this->collation" : '',
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->hasDefault
                ? ($this->default ? "DEFAULT $this->default" : 'DEFAULT NULL')
                : ''
        );
    }
}