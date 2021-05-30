<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class ForeignKeyChecks implements CanCompile
{
    private $switch;

    public function __construct(bool $switch)
    {
        $this->switch = $switch;
    }

    public function compile()
    {
        return sprintf('SET FOREIGN_KEY_CHECKS=%s', $this->switch ? '1' : '0');
    }
}
