<?php

namespace PoK\SQLQueryBuilder\Table\Alter;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\ColumnBuilder;

class AddColumn implements CanCompile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ColumnBuilder
     */
    private $definition;

    /**
     * @var bool
     */
    private $first = false;

    /**
     * @var string
     */
    private $after;

    /**
     * AddColumn constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function definition(): ColumnBuilder
    {
        $this->definition = new ColumnBuilder($this->name);
        return $this->definition;
    }

    public function first()
    {
        $this->first = true;
        return $this;
    }

    public function after(string $columnName)
    {
        $this->after = $columnName;
        return $this;
    }

    public function compile()
    {
        $positionSuffix = '';
        if ($this->first && !$this->after) $positionSuffix = ' FIRST';
        if (!$this->first && $this->after) $positionSuffix = sprintf(' AFTER `%s`', $this->after);
        //ADD COLUMN `credential_typey` TINYINT(4) NOT NULL AFTER `representer`
        return sprintf('ADD COLUMN %s%s', $this->definition->compile(), $positionSuffix);
    }
}