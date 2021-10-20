<?php

namespace PoK\SQLQueryBuilder;

class NameIncrementor
{
    private static $instance = null;

    private $count = 'a';

    private function __construct() {}

    private static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function next(string $prefix = '')
    {
        return $prefix . self::getInstance()->getNext();
    }

    public static function multipleNext(int $amount, string $prefix = '')
    {
        $names = [];
        for ($i = 0; $i < $amount; $i++)
            $names[] = self::next($prefix);

        return $names;
    }

    public function getNext()
    {
        return $this->count++;
    }
}