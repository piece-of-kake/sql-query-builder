<?php

namespace PoK\SQLQueryBuilder\ValueObject\Exceptions;

use PoK\Exception\ServerError\InternalServerErrorException;

class InvalidIndexTypeException extends InternalServerErrorException
{

    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('INVALID_INDEX_TYPE', $previous);
    }
}
