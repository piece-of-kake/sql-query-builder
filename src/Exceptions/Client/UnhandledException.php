<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\ServerError\InternalServerErrorException;

class UnhandledException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('UNHANDLED_EXCEPTION', $previous);
    }
}
