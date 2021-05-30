<?php

namespace PoK\SQLQueryBuilder\Profiler;

interface RecordQueryInterface
{
    public function recordQuery(string $query);
}