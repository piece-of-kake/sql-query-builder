<?php

namespace PoK\SQLQueryBuilder\ValueObject\Exceptions;

use PoK\Exception\ServerError\InternalServerErrorException;

class InvalidOrderDirectionException extends InternalServerErrorException
{

    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('INVALID_ORDER_DIRECTION', $previous);
    }
}
