<?php

namespace PoK\SQLQueryBuilder\Table\Fields\Interfaces;

interface UniqueField
{
    public function isUnique(): bool;
}
