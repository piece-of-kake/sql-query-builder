<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\HasDataInterface;
use PoK\Exception\ServerError\InternalServerErrorException;

class DataTooLongException extends InternalServerErrorException implements HasDataInterface
{
    private $SQLMessage;

    public function __construct(string $SQLMessage, \Throwable $previous = NULL)
    {
        parent::__construct('DATA_TOO_LONG', $previous);
        $this->SQLMessage = $SQLMessage;
    }

    public function getData()
    {
        return [
            'explanation' => $this->SQLMessage
        ];
    }
}