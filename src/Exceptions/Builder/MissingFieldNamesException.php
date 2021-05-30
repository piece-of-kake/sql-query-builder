<?php

namespace PoK\SQLQueryBuilder\Exceptions\Builder;

use PoK\Exception\ServerError\InternalServerErrorException;

class MissingFieldNamesException extends InternalServerErrorException
{

    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('MISSING_FIELD_NAMES', $previous);
    }
}
