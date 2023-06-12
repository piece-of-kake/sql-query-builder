<?php

namespace PoK\SQLQueryBuilder\ValueObject;

use PoK\SQLQueryBuilder\ValueObject\Exceptions\InvalidIndexTypeException;

class IndexType
{
    const TYPE_UNIQUE = 'UNIQUE';
    const TYPE_FULLTEXT = 'FULLTEXT';
    const TYPE_SPATIAL = 'SPATIAL';

    private $value;

    public function __construct(string $type)
    {
        $this->value = $type;
        $this->validateValue();
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public static function makeUnique()
    {
        return new static(self::TYPE_UNIQUE);
    }

    public static function makeFullText()
    {
        return new static(self::TYPE_FULLTEXT);
    }

    public static function makeSpatial()
    {
        return new static(self::TYPE_SPATIAL);
    }

    private function validateValue()
    {
        if (
            $this->value !== self::TYPE_UNIQUE &&
            $this->value !== self::TYPE_FULLTEXT &&
            $this->value !== self::TYPE_SPATIAL
        )
            throw new InvalidIndexTypeException();
    }
}