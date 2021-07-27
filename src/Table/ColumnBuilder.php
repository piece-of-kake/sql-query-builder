<?php

namespace PoK\SQLQueryBuilder\Table;

use PoK\SQLQueryBuilder\Table\Columns\IntF;
use PoK\SQLQueryBuilder\Table\Columns\TinyInt;
use PoK\SQLQueryBuilder\Table\Columns\Varchar;
use PoK\SQLQueryBuilder\Table\Columns\Timestamp;
use PoK\SQLQueryBuilder\Table\Columns\UnixTimestamp;
use PoK\SQLQueryBuilder\Table\Columns\Decimal;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Primary;
use PoK\SQLQueryBuilder\Table\Columns\Interfaces\Unique;

class ColumnBuilder implements CanCompile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var CanCompile
     */
    private $column;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return IntF
     */
    public function autoIncrement(): IntF
    {
        $this->column = new IntF($this->name);
        $this->column->autoIncrement();
        return $this->column;
    }

    /**
     * @return IntF
     */
    public function integer(): IntF
    {
        $this->column = new IntF($this->name);
        return $this->column;
    }

    /**
     * @return TinyInt
     */
    public function tinyInt(): TinyInt
    {
        $this->column = new TinyInt($this->name);
        return $this->column;
    }

    /**
     * @return Varchar
     */
    public function string(): Varchar
    {
        $this->column = new Varchar($this->name);
        return $this->column;
    }

    /**
     * @return Timestamp
     */
    public function timestamp(): Timestamp
    {
        $this->column = new Timestamp($this->name);
        return $this->column;
    }

    /**
     * @return UnixTimestamp
     */
    public function unixTimestamp(): UnixTimestamp
    {
        $this->column = new UnixTimestamp($this->name);
        return $this->column;
    }

    /**
     * @return Decimal
     */
    public function decimal(): Decimal
    {
        $this->column = new Decimal($this->name);
        return $this->column;
    }

    public function isPrimary(): bool
    {
        return $this->column instanceof Primary && $this->column->isPrimary();
    }

    public function isUnique(): bool
    {
        return $this->column instanceof Unique && $this->column->isUnique();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function compile()
    {
        return $this->column->compile();
    }
}
