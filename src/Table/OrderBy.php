<?php

namespace PoK\SQLQueryBuilder\Table;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\ValueObject\OrderDirection;

class OrderBy implements CanCompile
{
    private $orders = [];

    /**
     * OrderBy constructor.
     * @param string $fieldNameOrExpression
     * @param OrderDirection|null $direction
     */
    public function __construct(string $fieldNameOrExpression, OrderDirection $direction = null)
    {
        $this->add($fieldNameOrExpression, $direction);
    }

    /**
     * @param string $fieldNameOrExpression
     * @param OrderDirection|null $direction
     * @return OrderBy
     */
    public function add(string $fieldNameOrExpression, OrderDirection $direction = null): OrderBy
    {
        $this->orders[] = [
            $fieldNameOrExpression,
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