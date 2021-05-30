<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\ServerError\InternalServerErrorException;

class CannotConnectException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('CANNOT_CONNECT_TO_THE_DATABASE', $previous);
    }
}