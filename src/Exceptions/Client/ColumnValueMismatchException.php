<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\ServerError\InternalServerErrorException;

class ColumnValueMismatchException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('COLUMN_MISMATCH', $previous);
    }
}