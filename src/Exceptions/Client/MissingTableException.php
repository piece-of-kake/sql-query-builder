<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\ServerError\InternalServerErrorException;

class MissingTableException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('MISSING_TABLE', $previous);
    }
}