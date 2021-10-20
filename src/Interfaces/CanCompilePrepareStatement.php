<?php

namespace PoK\SQLQueryBuilder\Interfaces;

interface CanCompilePrepareStatement
{
    public function compilePrepare();

    public function compileExecute();
}
