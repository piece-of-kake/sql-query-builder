<?php

namespace PoK\SQLQueryBuilder\Interfaces;

interface CanCompilePrepareStatement
{
    public function compilePrepare(): string;

    public function compileExecute(): array;
}
