<?php

namespace PoK\SQLQueryBuilder;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

interface SQLClientInterface
{
    public function execute(CanCompile $query);
}