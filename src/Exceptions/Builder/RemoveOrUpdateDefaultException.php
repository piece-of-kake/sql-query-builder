<?php

namespace PoK\SQLQueryBuilder\Exceptions\Builder;

use PoK\Exception\ServerError\InternalServerErrorException;

class RemoveOrUpdateDefaultException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('REMOVE_OR_UPDATE_DEFAULT', $previous);
    }
}
