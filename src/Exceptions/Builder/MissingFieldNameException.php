<?php

namespace PoK\SQLQueryBuilder\Exceptions\Builder;

use PoK\Exception\ServerError\InternalServerErrorException;

class MissingFieldNameException extends InternalServerErrorException
{

    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('INVALID_STRING_VALUE', $previous);
    }
}
