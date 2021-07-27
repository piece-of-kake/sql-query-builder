<?php

namespace PoK\SQLQueryBuilder\Table;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\ValueObject\OrderDirection;

class OrderBy implements CanCompile
{
    private $orders = [];

    /**
     * OrderBy constructor.
     * @param string $columnNameOrExpression
     * @param OrderDirection|null $direction
     */
    public function __construct(string $columnNameOrExpression, OrderDirection $direction = null)
    {
        $this->add($columnNameOrExpression, $direction);
    }

    /**
     * @param string $columnNameOrExpression
     * @param OrderDirection|null $direction
     * @return OrderBy
     */
    public function add(string $columnNameOrExpression, OrderDirection $direction = null): OrderBy
    {
        $this->orders[] = [
            $columnNameOrExpression,
            $direction
        ];
        return $this;
    }

    public function compile()
    {
        $formattedOrders = [];
        foreach ($this->orders as $order) {
            $formattedOrders[] = $order[1]
                ? $order[0] . " " . $order[1]
                : $order[0];
        }

        return ' ORDER BY ' . implode(',', $formattedOrders);
    }
}