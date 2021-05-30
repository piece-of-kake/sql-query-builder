<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\ServerError\InternalServerErrorException;

class DuplicateEntryException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('ENTRY_ALREADY_EXISTS', $previous);
    }
}