<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;

class CountQueries implements CanCompile
{
    public function compile()
    {
        return 'show session status like "Queries"';
    }
}
