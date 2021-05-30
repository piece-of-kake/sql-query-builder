<?php

namespace PoK\SQLQueryBuilder\Table\Fields;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Fields\Interfaces\PrimaryField;
use PoK\SQLQueryBuilder\Table\Fields\Interfaces\UniqueField;

/**
 * ItnF because Int is a reserved word
 */
class TinyInt implements CanCompile, PrimaryField, UniqueField
{
    private $name;
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
            '`%s` tinyint %s %s %s %s',
            $this->name,
            $this->unsigned ? 'UNSIGNED' : '',
            $this->nullable ? 'NULL' : 'NOT NULL',
            $this->isAutoIncrement ? 'AUTO_INCREMENT' : '',
            $this->hasDefault
                ? ($this->default !== null ? "DEFAULT $this->default" : 'DEFAULT NULL')
                : ''
        );
    }
}