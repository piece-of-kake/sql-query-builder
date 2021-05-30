<?php

namespace PoK\SQLQueryBuilder\Exceptions\Builder;

use PoK\Exception\ServerError\InternalServerErrorException;

class MissingTableNameException extends InternalServerErrorException
{

    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('MISSING_TABLE_NAME', $previous);
    }
}
