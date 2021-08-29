<?php

namespace PoK\SQLQueryBuilder\Exceptions\Client;

use PoK\Exception\HasDataInterface;
use PoK\Exception\ServerError\InternalServerErrorException;

class UnhandledMySQLException extends InternalServerErrorException implements HasDataInterface
{
    private $unhandledCode;
    private $unhandledMessage;

    public function __construct($code = null, $message = null, \Throwable $previous = NULL)
    {
        parent::__construct('UNHANDLED_MYSQL_EXCEPTION', $previous);
        $this->unhandledCode = $code;
        $this->unhandledMessage = $message;
    }

    public function getData()
    {
        return [
            'code' => $this->unhandledCode,
            'message' => $this->unhandledMessage
        ];
    }
}
