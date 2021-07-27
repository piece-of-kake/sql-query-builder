<?php

namespace PoK\SQLQueryBuilder\Table\Alter;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class DropColumn implements CanCompile
{
    /**
     * @var string
     */
    private $name;

    /**
     * DropColumn constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function compile()
    {
        return sprintf('DROP COLUMN `%s`', $this->name);
    }
}