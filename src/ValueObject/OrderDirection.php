<?php

namespace PoK\SQLQueryBuilder\ValueObject;

use PoK\SQLQueryBuilder\ValueObject\Exceptions\InvalidOrderDirectionException;

class OrderDirection
{
    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    private $value;

    public function __construct($direction)
    {
        $this->value = $direction;
        $this->validateValue();
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public static function makeASC()
    {
        return new static(self::DIRECTION_ASC);
    }

    public static function makeDESC()
    {
        return new static(self::DIRECTION_DESC);
    }

    private function validateValue()
    {
        if (
            $this->value !== self::DIRECTION_ASC &&
            $this->value !== self::DIRECTION_DESC
        )
            throw new InvalidOrderDirectionException();
    }
}
