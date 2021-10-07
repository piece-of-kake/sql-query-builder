<?php

namespace PoK\SQLQueryBuilder\Exceptions\Builder;

use PoK\Exception\ServerError\InternalServerErrorException;

class UpdateDefaultOrChangePositionException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('UPDATE_DEFAULT_OR_CHANGE_POSITION', $previous);
    }
}
