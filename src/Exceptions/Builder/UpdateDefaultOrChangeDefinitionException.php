<?php

namespace PoK\SQLQueryBuilder\Exceptions\Builder;

use PoK\Exception\ServerError\InternalServerErrorException;

class UpdateDefaultOrChangeDefinitionException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('UPDATE_DEFAULT_OR_CHANGE_DEFINITION', $previous);
    }
}
