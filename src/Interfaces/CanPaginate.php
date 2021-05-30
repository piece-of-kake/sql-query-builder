<?php

namespace PoK\SQLQueryBuilder\Interfaces;

use PoK\ValueObject\Pagination;

interface CanPaginate
{

    public function paginate(Pagination $pagination);

    public function getPagination(): Pagination;

    public function hasPagination(): bool;
}
