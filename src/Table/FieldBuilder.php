<?php

namespace PoK\SQLQueryBuilder\Table;

use PoK\SQLQueryBuilder\Table\Fields\IntF;
use PoK\SQLQueryBuilder\Table\Fields\TinyInt;
use PoK\SQLQueryBuilder\Table\Fields\Varchar;
use PoK\SQLQueryBuilder\Table\Fields\Timestamp;
use PoK\SQLQueryBuilder\Table\Fields\UnixTimestamp;
use PoK\SQLQueryBuilder\Table\Fields\Decimal;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\Fields\Interfaces\PrimaryField;
use PoK\SQLQueryBuilder\Table\Fields\Interfaces\UniqueField;

class FieldBuilder implements CanCompile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var CanCompile
     */
    private $field;

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
        $this->field = new IntF($this->name);
        $this->field->autoIncrement();
        return $this->field;
    }

    /**
     * @return IntF
     */
    public function integer(): IntF
    {
        $this->field = new IntF($this->name);
        return $this->field;
    }

    /**
     * @return TinyInt
     */
    public function tinyInt(): TinyInt
    {
        $this->field = new TinyInt($this->name);
        return $this->field;
    }

    /**
     * @return Varchar
     */
    public function string(): Varchar
    {
        $this->field = new Varchar($this->name);
        return $this->field;
    }

    /**
     * @return Timestamp
     */
    public function timestamp(): Timestamp
    {
        $this->field = new Timestamp($this->name);
        return $this->field;
    }

    /**
     * @return UnixTimestamp
     */
    public function unixTimestamp(): UnixTimestamp
    {
        $this->field = new UnixTimestamp($this->name);
        return $this->field;
    }

    /**
     * @return Decimal
     */
    public function decimal(): Decimal
    {
        $this->field = new Decimal($this->name);
        return $this->field;
    }

    public function isPrimary(): bool
    {
        return $this->field instanceof PrimaryField && $this->field->isPrimary();
    }

    public function isUnique(): bool
    {
        return $this->field instanceof UniqueField && $this->field->isUnique();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function compile()
    {
        return $this->field->compile();
    }
}
